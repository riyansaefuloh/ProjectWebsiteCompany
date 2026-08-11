<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Our Export Products - ' . $appName);
        SEOMeta::setDescription('Browse our full catalog of premium Indonesian coffee export products including Arabica, Robusta, and specialty grades. Available for wholesale FOB/CIF.');
        SEOMeta::setCanonical(route('products.index'));

        OpenGraph::setTitle('Our Export Products - ' . $appName);
        OpenGraph::setDescription('Premium Indonesian coffee export products. Find the right grade, MOQ, and packaging for your import needs.');
        OpenGraph::setUrl(route('products.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Our Export Products - ' . $appName);
        TwitterCard::setDescription('Browse our premium Indonesian coffee export products.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $categories = Category::where('status', 'active')
            ->with('translations')
            ->get();

        $products = Product::where('status', 'published')
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->category, function ($query) {
                $query->whereHas('category', function($q) {
                    $q->where('slug', $this->category);
                });
            })
            ->with(['translations', 'media', 'category.translations'])
            ->paginate(12);

        return view('livewire.public.product-index', [
            'categories' => $categories,
            'products'   => $products,
        ]);
    }
}
