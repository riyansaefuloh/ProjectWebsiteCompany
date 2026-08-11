<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields
    public string $name_en = '';
    public string $name_id = '';
    public string $description_en = '';
    public string $description_id = '';
    public ?string $icon = null; // Still kept for CSS classes fallback if needed
    public int $sort_order = 0;
    public string $status = 'active';

    // Image Upload & UI State
    public $imageFile;
    public ?string $existingImage = null;
    public string $activeTab = 'en';

    protected function rules(): array
    {
        return [
            'name_en'        => 'required|string|max:100',
            'name_id'        => 'required|string|max:100',
            'description_en' => 'nullable|string|max:500',
            'description_id' => 'nullable|string|max:500',
            'icon'           => 'nullable|string|max:50',
            'sort_order'     => 'integer|min:0',
            'status'         => 'required|in:active,inactive',
            'imageFile'      => 'nullable|image|max:3072',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $category = Category::with('translations')->findOrFail($id);
        $this->editingId = $category->id;
        $this->name_en = $category->getTranslation('name', 'en') ?? '';
        $this->name_id = $category->getTranslation('name', 'id') ?? '';
        $this->description_en = $category->getTranslation('description', 'en') ?? '';
        $this->description_id = $category->getTranslation('description', 'id') ?? '';
        $this->icon = $category->icon;
        $this->sort_order = $category->sort_order;
        $this->status = $category->status;

        $this->existingImage = $category->getFirstMediaUrl('icon');
        $this->activeTab = 'en';

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $category = $this->editingId 
            ? Category::findOrFail($this->editingId)
            : new Category();

        $category->slug = Str::slug($this->name_en);
        $category->icon = $this->icon;
        $category->sort_order = $this->sort_order;
        $category->status = $this->status;
        $category->save();

        // Simpan Terjemahan (EN & ID)
        $category->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $this->name_en, 'description' => $this->description_en]
        );
        $category->translations()->updateOrCreate(
            ['locale' => 'id'],
            ['name' => $this->name_id, 'description' => $this->description_id]
        );

        // Simpan Gambar Kategori (Spatie MediaLibrary)
        if ($this->imageFile) {
            $category->clearMediaCollection('icon'); // Hapus gambar lama
            $category->addMedia($this->imageFile->getRealPath())->toMediaCollection('icon');
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Category saved successfully!');
    }

    public function delete(string $id): void
    {
        $category = Category::findOrFail($id);
        $category->clearMediaCollection('icon');
        $category->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function deleteImage(): void
    {
        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            $category->clearMediaCollection('icon');
            $this->existingImage = null;
            session()->flash('message', 'Image deleted successfully!');
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name_en = '';
        $this->name_id = '';
        $this->description_en = '';
        $this->description_id = '';
        $this->icon = null;
        $this->sort_order = 0;
        $this->status = 'active';
        $this->imageFile = null;
        $this->existingImage = null;
        $this->activeTab = 'en';
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $categories = Category::with('translations')
            ->when($this->search, function ($q) {
                $q->whereHas('translations', function ($trans) {
                    $trans->where('name', 'LIKE', "%{$this->search}%");
                });
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.category-index', [
            'categories' => $categories,
        ]);
    }
}
