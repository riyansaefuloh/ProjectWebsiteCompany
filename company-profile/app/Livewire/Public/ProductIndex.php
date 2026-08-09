<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::where('status', 'active')
            ->with('translations')
            ->get();

        $products = Product::where('status', 'published')
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->with(['translations', 'media', 'category.translations'])
            ->paginate(12);

        return view('livewire.public.product-index', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
