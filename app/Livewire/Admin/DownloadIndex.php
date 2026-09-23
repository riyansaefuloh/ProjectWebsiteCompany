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
    public string $selectedGate = '';

    public bool $showModal = false;
    public ?string $editingId = null;

    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedGate'], true)) {
            $this->resetPage();
        }
    }

    public string $title = '';
    public string $akses = 'gated';
    public int $sort_order = 0;
    public $pdfFile;
    public ?string $existingFilePath = null;

    protected function rules(): array
    {
        return [
            'title'      => 'required|string|max:150',
            'akses'      => 'required|in:gated,open',
            'sort_order' => 'integer|min:0',
            'pdfFile'    => ($this->editingId ? 'nullable' : 'required') . '|mimes:pdf|max:10240',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $this->resetValidation();

        $download = Download::findOrFail($id);
        $this->editingId = $download->id;
        $this->title = $download->title;
        $this->akses = $download->require_email ? 'gated' : 'open';
        $this->sort_order = $download->sort_order;
        $this->existingFilePath = $download->file_path;
        $this->pdfFile = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $download = $this->editingId
            ? Download::findOrFail($this->editingId)
            : new Download();

        $download->title = $this->title;
        $download->require_email = $this->akses === 'gated';
        $download->sort_order = $this->sort_order;

        if ($this->pdfFile) {
            if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
                Storage::disk('public')->delete($download->file_path);
            }

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
        $this->akses = 'gated';
        $this->sort_order = 0;
        $this->pdfFile = null;
        $this->existingFilePath = null;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $downloads = Download::query()
            ->when($this->search, function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%");
            })
            ->when($this->selectedGate !== '', function ($q) {
                $q->where('require_email', $this->selectedGate === '1');
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.download-index', [
            'downloads' => $downloads,
        ]);
    }
}
