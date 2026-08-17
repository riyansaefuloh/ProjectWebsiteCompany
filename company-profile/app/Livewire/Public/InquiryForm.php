<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Inquiry;
use App\Services\WhatsAppService;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryAutoReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

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
        $appName = config('app.name');

        SEOMeta::setTitle('Contact Us / Wholesale Inquiry - ' . $appName);
        SEOMeta::setDescription('Get in touch with us for bulk coffee export orders, pricing, shipping terms (FOB/CIF), and wholesale inquiries. We respond within 24 hours.');
        SEOMeta::setCanonical(route('inquiry.index'));

        OpenGraph::setTitle('Contact Us / Wholesale Inquiry - ' . $appName);
        OpenGraph::setDescription('Send us your wholesale inquiry. We offer competitive FOB/CIF pricing for coffee export.');
        OpenGraph::setUrl(route('inquiry.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Contact Us - ' . $appName);
        TwitterCard::setDescription('Wholesale coffee export inquiry. We respond within 24 hours.');

        $productId = $productId ?: request()->query('product');

        if ($productId && \App\Models\Product::where('id', $productId)->where('status', 'published')->exists()) {
            $this->product_id = $productId;
        }
    }

    public function executeRecaptcha(): void
    {
        $this->dispatch('request-recaptcha');
    }

    public function submit(string $recaptchaToken): void
    {
        // 1. Anti-Spam Honeypot Check
        if (!empty($this->website_hp)) {
            // Jika bot mengisi honeypot, abaikan secara diam-diam
            return;
        }

        if (!empty(env('RECAPTCHA_SECRET_KEY'))) {
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptchaToken,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful() || !$response->json('success') || $response->json('score') < 0.5) {
                $this->addError('message', 'reCAPTCHA validation failed. You appear to be a bot.');
                return;
            }
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

        $salesEmail = config('mail.from.address', 'sales@exportercompany.com');
        Mail::to($salesEmail)->queue(new InquiryReceivedMail($inquiry));

        // B. Kirim Auto-reply ke Buyer
        Mail::to($inquiry->email)->queue(new InquiryAutoReplyMail($inquiry));

        // 5. Generate WhatsApp Deep-Link
        $this->whatsappUrl = WhatsAppService::generateLink($inquiry);
        $this->isSubmitted = true;
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $products = \App\Models\Product::where('status', 'published')
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('livewire.public.inquiry-form', [
            'products' => $products,
            'settings' => $settings,
        ]);
    }
}
