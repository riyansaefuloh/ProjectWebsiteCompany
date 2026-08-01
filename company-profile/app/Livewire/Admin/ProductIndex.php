<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Certification;
use Illuminate\Support\Str;

class ProductIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Filter & Search State
    public string $search = '';
    public string $selectedCategory = '';

    // Modal & Edit State
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields (PRD Bab 9.1)
    public string $category_id = '';
    public string $name_en = '';
    public string $name_id = '';
    public string $description_en = '';
    public string $description_id = '';
    public string $hs_code = '';
    public string $moq = '';
    public string $supply_capacity = '';
    public string $packaging = '';
    public string $origin = 'Indonesia';
    public ?float $indicative_price = null;
    public string $currency = 'USD';
    public string $incoterms = 'FOB,CIF';
    public bool $is_featured = false;
    public string $status = 'published';

    // Pivot & Dynamic Specifications (Key-Value Repeater)
    public array $selectedCertifications = [];
    public array $specifications = []; // [['key' => 'Moisture', 'value' => '12%']]

    public $imageFiles = [];

    protected function rules(): array
    {
        return [
            'category_id'      => 'required|exists:categories,id',
            'name_en'          => 'required|string|max:150',
            'name_id'          => 'required|string|max:150',
            'description_en'   => 'nullable|string',
            'description_id'   => 'nullable|string',
            'hs_code'          => 'required|string|max:50',
            'moq'              => 'required|string|max:100',
            'supply_capacity'  => 'required|string|max:100',
            'packaging'        => 'required|string|max:100',
            'origin'           => 'required|string|max:100',
            'indicative_price' => 'nullable|numeric|min:0',
            'currency'         => 'required|string|size:3',
            'incoterms'        => 'required|string|max:100',
            'is_featured'      => 'boolean',
            'status'           => 'required|in:draft,published',
            'imageFiles.*'     => 'nullable|image|max:3072', // Max 3MB
        ];
    }

    public function addSpecification(): void
    {
        $this->specifications[] = ['key' => '', 'value' => ''];
    }

    public function removeSpecification(int $index): void
    {
        unset($this->specifications[$index]);
        $this->specifications = array_values($this->specifications);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $product = Product::with(['translations', 'specifications', 'certifications'])->findOrFail($id);
        $this->editingId = $product->id;
        $this->category_id = $product->category_id;
        $this->name_en = $product->getTranslation('name', 'en') ?? '';
        $this->name_id = $product->getTranslation('name', 'id') ?? '';
        $this->description_en = $product->getTranslation('description', 'en') ?? '';
        $this->description_id = $product->getTranslation('description', 'id') ?? '';
        $this->hs_code = $product->hs_code;
        $this->moq = $product->moq;
        $this->supply_capacity = $product->supply_capacity;
        $this->packaging = $product->packaging;
        $this->origin = $product->origin;
        $this->indicative_price = $product->indicative_price;
        $this->currency = $product->currency;
        $this->incoterms = $product->incoterms;
        $this->is_featured = $product->is_featured;
        $this->status = $product->status;

        $this->selectedCertifications = $product->certifications->pluck('id')->toArray();

        $this->specifications = $product->specifications->map(function ($spec) {
            return ['key' => $spec->spec_key, 'value' => $spec->spec_value];
        })->toArray();

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $product = $this->editingId 
            ? Product::findOrFail($this->editingId)
            : new Product();

        $product->category_id = $this->category_id;
        $product->slug = Str::slug($this->name_en);
        $product->hs_code = $this->hs_code;
        $product->moq = $this->moq;
        $product->supply_capacity = $this->supply_capacity;
        $product->packaging = $this->packaging;
        $product->origin = $this->origin;
        $product->indicative_price = $this->indicative_price;
        $product->currency = $this->currency;
        $product->incoterms = $this->incoterms;
        $product->is_featured = $this->is_featured;
        $product->status = $this->status;
        $product->save();

        // 1. Save Translations (EN & ID)
        $product->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $this->name_en, 'description' => $this->description_en]
        );
        $product->translations()->updateOrCreate(
            ['locale' => 'id'],
            ['name' => $this->name_id, 'description' => $this->description_id]
        );

        // 2. Sync Certifications Pivot
        $product->certifications()->sync($this->selectedCertifications);

        // 3. Save Dynamic Specifications
        $product->specifications()->delete();
        foreach ($this->specifications as $spec) {
            if (!empty($spec['key']) && !empty($spec['value'])) {
                $product->specifications()->create([
                    'spec_key'   => $spec['key'],
                    'spec_value' => $spec['value'],
                    'locale'     => 'en',
                ]);
            }
        }

        // 4. Save Media Images via Spatie MediaLibrary
        if (!empty($this->imageFiles)) {
            foreach ($this->imageFiles as $file) {
                $product->addMedia($file->getRealPath())->toMediaCollection('gallery');
            }
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Export Product saved successfully!');
    }

    public function delete(string $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('message', 'Product deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->category_id = '';
        $this->name_en = '';
        $this->name_id = '';
        $this->description_en = '';
        $this->description_id = '';
        $this->hs_code = '';
        $this->moq = '';
        $this->supply_capacity = '';
        $this->packaging = '';
        $this->origin = 'Indonesia';
        $this->indicative_price = null;
        $this->currency = 'USD';
        $this->incoterms = 'FOB,CIF';
        $this->is_featured = false;
        $this->status = 'published';
        $this->selectedCertifications = [];
        $this->specifications = [];
        $this->imageFiles = [];
    }

    public function render()
    {
        $products = Product::with(['category.translations', 'translations', 'certifications'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('category_id', $this->selectedCategory);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.product-index', [
            'products'       => $products,
            'categories'     => Category::with('translations')->get(),
            'certifications' => Certification::with('translations')->get(),
        ]);
    }
}
