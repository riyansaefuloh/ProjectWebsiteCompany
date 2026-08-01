<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields
    public string $name_en = '';
    public string $name_id = '';
    public string $description_en = '';
    public string $description_id = '';
    public ?string $icon = null;
    public int $sort_order = 0;
    public string $status = 'active';

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

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Category saved successfully!');
    }

    public function delete(string $id): void
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Category deleted successfully!');
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
    }

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
