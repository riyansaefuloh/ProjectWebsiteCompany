<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Certification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificationIndex extends Component
{
    /*
     * WithPagination sebelumnya tidak terpasang di sini — satu-satunya halaman
     * daftar admin yang begitu. Akibatnya resetPage() tidak ada (kait updating()
     * di bawah melempar galat begitu kotak pencariannya diketik), dan tautan
     * halamannya berupa ?page=N biasa yang memuat ulang seantero halaman
     * alih-alih memperbarui tabelnya saja.
     */
    use WithPagination, WithFileUploads;

    // Data List State
    public string $search = '';

    /*
     * Penyaring status. Namanya selectedStatus, bukan status, karena $status
     * di bawah sudah dipakai sebagai isian modalnya — satu properti tidak bisa
     * merangkap dua peran: menyunting sertifikasi bakal ikut menyaring tabelnya.
     */
    public string $selectedStatus = '';

    // Form Modal State
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields
    public string $name_en = '';
    public string $name_id = '';
    public string $issuer = '';
    public ?string $certificate_number = null;
    public ?string $issued_at = null;
    public ?string $expires_at = null;

    /*
     * Status dan urutan tampil.
     *
     * Keduanya sudah ada di tabelnya sejak awal dan sudah dipakai situs
     * publik — Home dan About menyaring where('status', 'active') lalu
     * mengurutkan dengan sort_order — tapi formulirnya tidak pernah
     * menyentuhnya: save() memaksa status jadi 'active' tiap kali menyimpan,
     * dan sort_order tidak pernah disebut sama sekali.
     *
     * Akibatnya sertifikasi tidak bisa disembunyikan dari situs publik
     * kecuali dengan menghapusnya, dan urutan tampilnya tidak bisa dibetulkan
     * dari panel.
     */
    public string $status = 'active';
    public int $sort_order = 0;

    // Uploads
    public $logoFile;
    public $pdfFile;

    // Existing media (for preview/delete in edit mode)
    public ?string $existingLogoUrl = null;
    public ?string $existingPdfUrl = null;

    /**
     * Kembali ke halaman satu tiap kali penyaringnya diubah.
     *
     * Tanpa ini, menyaring saat sedang berada di halaman jauh meninggalkan
     * nomor halamannya apa adanya — dan halaman 20 dari hasil yang cuma 3
     * halaman menggambar tabel kosong beserta kalimat "tidak ada yang cocok",
     * padahal hasilnya ada, cuma tidak di halaman itu.
     */
    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus'], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            'name_en'            => 'required|string|max:150',
            'name_id'            => 'required|string|max:150',
            'issuer'             => 'required|string|max:150',
            'certificate_number' => 'nullable|string|max:100',
            'issued_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after_or_equal:issued_at',
            'status'             => 'required|in:active,inactive',
            'sort_order'         => 'integer|min:0',
            'logoFile'           => 'nullable|image|max:2048', // Max 2MB
            'pdfFile'            => 'nullable|mimes:pdf|max:5120', // Max 5MB PDF
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $cert = Certification::with('translations')->findOrFail($id);
        $this->editingId = $cert->id;
        $this->name_en = $cert->getTranslation('name', 'en') ?? '';
        $this->name_id = $cert->getTranslation('name', 'id') ?? '';
        $this->issuer = $cert->issuer;
        $this->certificate_number = $cert->certificate_number;
        $this->issued_at = $cert->issued_at ? $cert->issued_at->format('Y-m-d') : null;
        $this->expires_at = $cert->expires_at ? $cert->expires_at->format('Y-m-d') : null;
        $this->status = $cert->status;
        $this->sort_order = $cert->sort_order;
        
        $this->existingLogoUrl = $cert->getFirstMediaUrl('logos');
        $this->existingPdfUrl = $cert->getFirstMediaUrl('pdfs');

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $cert = $this->editingId 
            ? Certification::findOrFail($this->editingId)
            : new Certification();

        $cert->slug = Str::slug($this->name_en);
        $cert->issuer = $this->issuer;
        $cert->certificate_number = $this->certificate_number;
        $cert->issued_at = $this->issued_at;
        $cert->expires_at = $this->expires_at;
        $cert->status = $this->status;
        $cert->sort_order = $this->sort_order;
        $cert->save();

        // Simpan Terjemahan Nama (EN & ID)
        $cert->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $this->name_en]
        );
        $cert->translations()->updateOrCreate(
            ['locale' => 'id'],
            ['name' => $this->name_id]
        );

        // Process Upload Logo via Spatie MediaLibrary
        if ($this->logoFile) {
            $cert->clearMediaCollection('logos');
            $cert->addMedia($this->logoFile->getRealPath())->toMediaCollection('logos');
        }

        // Process Upload PDF via Spatie MediaLibrary
        if ($this->pdfFile) {
            $cert->clearMediaCollection('pdfs');
            $cert->addMedia($this->pdfFile->getRealPath())->toMediaCollection('pdfs');
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Certification saved successfully!');
    }

    public function delete(string $id): void
    {
        $cert = Certification::findOrFail($id);
        $cert->clearMediaCollection('logos');
        $cert->clearMediaCollection('pdfs');
        $cert->delete();
        session()->flash('message', 'Certification deleted successfully!');
    }

    public function deleteLogo(): void
    {
        if ($this->editingId) {
            $cert = Certification::findOrFail($this->editingId);
            $cert->clearMediaCollection('logos');
            $this->existingLogoUrl = null;
            session()->flash('message', 'Logo deleted successfully!');
        }
    }

    public function deletePdf(): void
    {
        if ($this->editingId) {
            $cert = Certification::findOrFail($this->editingId);
            $cert->clearMediaCollection('pdfs');
            $this->existingPdfUrl = null;
            session()->flash('message', 'PDF deleted successfully!');
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name_en = '';
        $this->name_id = '';
        $this->issuer = '';
        $this->certificate_number = null;
        $this->issued_at = null;
        $this->expires_at = null;
        $this->status = 'active';
        $this->sort_order = 0;
        $this->logoFile = null;
        $this->pdfFile = null;
        $this->existingLogoUrl = null;
        $this->existingPdfUrl = null;
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        /*
         * 1. Sertifikat yang perlu diurus.
         *
         * Dua kueri, bukan satu, dan persis sama dengan yang dipakai dasbor:
         * yang SUDAH lewat tanggal, dan yang akan menyusul dalam 90 hari.
         *
         * Sebelumnya di sini hanya ada satu kueri berjendela 30 hari ke depan,
         * yang berarti sertifikat yang justru sudah kedaluwarsa — keadaan
         * paling gawat — tidak pernah masuk peringatannya sama sekali.
         */
        $expiredCerts = Certification::with('translations')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->where('status', 'active')
            ->orderBy('expires_at', 'asc')
            ->get();

        $expiringSoonCerts = Certification::with('translations')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(90)])
            ->orderBy('expires_at', 'asc')
            ->get();

        // 2. Query List Sertifikat dengan Filter Search & Status
        $certifications = Certification::with(['translations', 'media'])
            /*
             * Kedua syarat pencariannya dikurung sendiri.
             *
             * when() tidak membungkus isinya dalam tanda kurung, jadi tanpa
             * $b ini SQL-nya jadi "(nama cocok) OR issuer cocok AND status =
             * ?" — dan AND mengikat lebih erat daripada OR, sehingga penyaring
             * statusnya cuma berlaku untuk cabang issuer.
             */
            ->when($this->search, function ($q) {
                $q->where(function ($b) {
                    $b->whereHas('translations', function ($trans) {
                        $trans->where('name', 'LIKE', "%{$this->search}%");
                    })->orWhere('issuer', 'LIKE', "%{$this->search}%");
                });
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.certification-index', [
            'certifications'    => $certifications,
            'expiredCerts'      => $expiredCerts,
            'expiringSoonCerts' => $expiringSoonCerts,
        ]);
    }
}
