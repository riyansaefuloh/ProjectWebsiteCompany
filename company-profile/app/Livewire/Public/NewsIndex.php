<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\News;
use App\Models\NewsCategory;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class NewsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $sort = 'newest';

    /** Nilai `sort` yang diterima. Nilai di luar daftar ini diabaikan. */
    public const SORT_OPTIONS = ['newest', 'oldest', 'title_asc', 'title_desc'];

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => ''],
        'sort'     => ['except' => 'newest'],
    ];

    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('News & Updates - ' . $appName);
        SEOMeta::setDescription('Stay up to date with the latest news, industry insights, and company updates from ' . $appName . '.');
        SEOMeta::setCanonical(route('news.index'));

        OpenGraph::setTitle('News & Updates - ' . $appName);
        OpenGraph::setDescription('Latest news and updates from ' . $appName . '.');
        OpenGraph::setUrl(route('news.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('News & Updates - ' . $appName);
        TwitterCard::setDescription('Latest news and updates from ' . $appName . '.');
    }

    public function updatingSearch()
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
        $categories = NewsCategory::withCount(['news' => fn ($q) => $q->where('status', 'published')->where('published_at', '<=', now())])
            ->orderBy('name')
            ->get();

        $hasFilters = filled($this->search) || filled($this->category);

        // ── Artikel sorotan ──────────────────────────────────────────────
        $featured = $hasFilters
            ? null
            : News::where('status', 'published')
                ->where('published_at', '<=', now())
                ->with(['translations', 'media', 'category'])
                ->orderByDesc('published_at')
                ->first();

        $sort = in_array($this->sort, self::SORT_OPTIONS, true) ? $this->sort : 'newest';

        $query = News::where('news.status', 'published')
            ->where('news.published_at', '<=', now())
            ->when($featured, fn ($q) => $q->where('news.id', '!=', $featured->id))
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->category, function ($q) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $this->category));
            })
            ->with(['translations', 'media', 'author', 'category']);

        if (in_array($sort, ['title_asc', 'title_desc'], true)) {
            $query->select('news.*')
                ->leftJoin('news_translations as nt', function ($join) {
                    $join->on('nt.news_id', '=', 'news.id')
                         ->where('nt.locale', '=', app()->getLocale());
                })
                ->orderBy('nt.title', $sort === 'title_asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('published_at', $sort === 'oldest' ? 'asc' : 'desc');
        }

        return view('livewire.public.news-index', [
            /* Isi kepala halaman ini bisa disunting dari menu Halaman;
               yang kosong jatuh ke teks bawaan di berkas bahasa. */
            'isi' => \App\Support\IsiHalaman::untuk('news'),

            'news'       => $query->paginate(9),
            'categories' => $categories,
            'featured'   => $featured,
            'hasFilters' => $hasFilters,
        ]);
    }
}
