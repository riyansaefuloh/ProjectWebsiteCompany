<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\News;

class NewsIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

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
