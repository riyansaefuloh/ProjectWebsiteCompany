<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\News;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class NewsIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => '']
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

    #[Layout('components.layouts.public')]
    public function render()
    {
        $news = News::where('status', 'published')
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->with(['translations', 'media', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('livewire.public.news-index', [
            'news' => $news,
        ]);
    }
}
