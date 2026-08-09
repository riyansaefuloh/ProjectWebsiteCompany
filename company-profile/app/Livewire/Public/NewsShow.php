<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\News;

class NewsShow extends Component
{
    public $news;

    public function mount($slug)
    {
        $this->news = News::where('slug', $slug)
            ->where('status', 'published')
            ->with(['translations', 'media', 'author'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.news-show');
    }
}
