<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Inquiry;
use App\Services\WhatsAppService;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryAutoReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class InquiryForm extends Component
{
    // Property Form (sesuai spesifikasi PRD Bab 7.10)
    public string $name = '';
    public string $company = '';
    public string $email = '';
    public string $country_code = 'US'; // Default country code ISO-2
    public ?string $phone = null;
    public ?string $product_id = null;
    public ?string $volume = null;
    public ?string $incoterms = null;
    public string $message = '';

    // Anti-spam Honeypot field (harus tetap kosong)
    public string $website_hp = '';

    // Result State
    public bool $isSubmitted = false;
    public ?string $whatsappUrl = null;

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:100',
            'company'      => 'required|string|max:150',
            'email'        => 'required|email|max:150',
            'country_code' => 'required|string|size:2',
            'phone'        => 'nullable|string|max:30',
            'product_id'   => 'nullable|exists:products,id',
            'volume'       => 'nullable|string|max:100',
            'incoterms'    => 'nullable|string|max:50',
            'message'      => 'required|string|max:2000',
        ];
    }

    public function mount(?string $productId = null): void
    {
        // Jika form dibuka dari Halaman Detail Produk, prefill product_id
        if ($productId) {
            $this->product_id = $productId;
        }
    }

    public function submit(): void
    {
        // 1. Anti-Spam Honeypot Check
        if (!empty($this->website_hp)) {
            // Jika bot mengisi honeypot, abaikan secara diam-diam
            return;
        }

        // 2. Rate Limiting Check (Maksimal 3 inquiry per 10 menit per IP)
        $executed = RateLimiter::attempt(
            'submit-inquiry:' . request()->ip(),
            $perMinute = 3,
            function () {
                $this->processInquiry();
            },
            $decaySeconds = 600
        );

        if (!$executed) {
            $this->addError('email', 'Too many requests. Please wait a few minutes before submitting another inquiry.');
        }
    }

    private function processInquiry(): void
    {
        // Validasi input
        $validatedData = $this->validate();

        // 3. Simpan ke Database
        $inquiry = Inquiry::create([
            'name'         => $validatedData['name'],
            'company'      => $validatedData['company'],
            'email'        => $validatedData['email'],
            'country_code' => strtoupper($validatedData['country_code']),
            'phone'        => $validatedData['phone'],
            'product_id'   => $validatedData['product_id'],
            'volume'       => $validatedData['volume'],
            'incoterms'    => $validatedData['incoterms'],
            'message'      => $validatedData['message'],
            'status'       => 'new',
            'ip_address'   => request()->ip(),
        ]);

        // 4. Kirim Email Async (Laravel Queue)
        // A. Kirim notifikasi ke Sales Team
        $salesEmail = config('mail.from.address', 'sales@exportercompany.com');
        Mail::to($salesEmail)->queue(new InquiryReceivedMail($inquiry));

        // B. Kirim Auto-reply ke Buyer
        Mail::to($inquiry->email)->queue(new InquiryAutoReplyMail($inquiry));

        // 5. Generate WhatsApp Deep-Link
        $this->whatsappUrl = WhatsAppService::generateLink($inquiry);
        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.public.inquiry-form');
    }
}
