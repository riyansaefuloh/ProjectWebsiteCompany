<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;

class DownloadIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields (PRD Bab 9.1)
    public string $title = '';
    public bool $require_email = true;
    public int $sort_order = 0;
    public $pdfFile;

    protected function rules(): array
    {
        return [
            'title'         => 'required|string|max:150',
            'require_email' => 'boolean',
            'sort_order'    => 'integer|min:0',
            'pdfFile'       => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $download = Download::findOrFail($id);
        $this->editingId = $download->id;
        $this->title = $download->title;
        $this->require_email = $download->require_email;
        $this->sort_order = $download->sort_order;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $download = $this->editingId 
            ? Download::findOrFail($this->editingId)
            : new Download();

        $download->title = $this->title;
        $download->require_email = $this->require_email;
        $download->sort_order = $this->sort_order;

        if ($this->pdfFile) {
            // Hapus file lama jika ada
            if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
                Storage::disk('public')->delete($download->file_path);
            }

            // Simpan file PDF baru di disk public/brochures
            $path = $this->pdfFile->store('brochures', 'public');
            $download->file_path = $path;
        }

        $download->save();

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Download brochure saved successfully!');
    }

    public function delete(string $id): void
    {
        $download = Download::findOrFail($id);
        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            Storage::disk('public')->delete($download->file_path);
        }
        $download->delete();

        session()->flash('message', 'Brochure deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->require_email = true;
        $this->sort_order = 0;
        $this->pdfFile = null;
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $downloads = Download::when($this->search, function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%");
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.download-index', [
            'downloads' => $downloads,
        ]);
    }
}
