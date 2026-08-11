<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Certification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificationIndex extends Component
{
    use WithFileUploads;

    // Data List State
    public string $search = '';

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
    
    // Uploads
    public $logoFile;
    public $pdfFile;

    // Existing media (for preview/delete in edit mode)
    public ?string $existingLogoUrl = null;
    public ?string $existingPdfUrl = null;

    protected function rules(): array
    {
        return [
            'name_en'            => 'required|string|max:150',
            'name_id'            => 'required|string|max:150',
            'issuer'             => 'required|string|max:150',
            'certificate_number' => 'nullable|string|max:100',
            'issued_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after_or_equal:issued_at',
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
        $cert->status = 'active';
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
        $this->logoFile = null;
        $this->pdfFile = null;
        $this->existingLogoUrl = null;
        $this->existingPdfUrl = null;
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Query Sertifikat yang mendekati Kedaluwarsa (<= 30 hari ke depan)
        $expiringCertifications = Certification::with('translations')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(30)])
            ->get();

        // 2. Query List Sertifikat dengan Filter Search
        $certifications = Certification::with(['translations', 'media'])
            ->when($this->search, function ($q) {
                $q->whereHas('translations', function ($trans) {
                    $trans->where('name', 'LIKE', "%{$this->search}%");
                })->orWhere('issuer', 'LIKE', "%{$this->search}%");
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.certification-index', [
            'certifications'         => $certifications,
            'expiringCertifications' => $expiringCertifications,
        ]);
    }
}
