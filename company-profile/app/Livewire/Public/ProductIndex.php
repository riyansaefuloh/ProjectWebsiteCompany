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

    public $sort = 'featured';

    /** Nilai `sort` yang diterima. Nilai di luar daftar ini diabaikan. */
    public const SORT_OPTIONS = ['featured', 'newest', 'name_asc', 'name_desc'];

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => ''],
        'sort'     => ['except' => 'featured'],
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

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function selectCategory(string $slug = ''): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category']);
        $this->resetPage();
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $categories = Category::where('status', 'active')
            ->withCount(['products' => fn ($q) => $q->where('status', 'published')])
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $sort = in_array($this->sort, self::SORT_OPTIONS, true) ? $this->sort : 'featured';

        $query = Product::where('status', 'published')
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->category, function ($q) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $this->category));
            })
            ->with(['translations', 'media', 'category.translations']);

        if (in_array($sort, ['name_asc', 'name_desc'], true)) {
            $query->select('products.*')
                ->leftJoin('product_translations as pt', function ($join) {
                    $join->on('pt.product_id', '=', 'products.id')
                         ->where('pt.locale', '=', app()->getLocale());
                })
                ->orderBy('pt.name', $sort === 'name_asc' ? 'asc' : 'desc');
        } elseif ($sort === 'newest') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('is_featured')->orderBy('sort_order');
        }

        return view('livewire.public.product-index', [
            'categories' => $categories,
            'products'   => $query->paginate(12),
        ]);
    }
}
