<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\News;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class NewsShow extends Component
{
    public $news;

    public function mount($slug): void
    {
        $this->news = News::where('slug', $slug)
            ->where('status', 'published')
            ->with(['translations', 'media', 'author', 'category', 'tags'])
            ->firstOrFail();

        $appName   = config('app.name');
        $locale    = app()->getLocale();
        $title     = $this->news->getTranslation('title', $locale) ?? $this->news->getTranslation('title', 'en');

        $body      = strip_tags($this->news->getTranslation('content', $locale) ?? $this->news->getTranslation('content', 'en') ?? '');
        $shortDesc = mb_substr($body, 0, 160);
        $imageUrl  = $this->news->getFirstMediaUrl('covers', 'webp')
                  ?: ($this->news->getFirstMediaUrl('covers') ?: null);

        // Meta
        SEOMeta::setTitle($title . ' - ' . $appName);
        SEOMeta::setDescription($shortDesc ?: 'Read the latest news from ' . $appName);
        SEOMeta::setCanonical(route('news.show', $this->news->slug));

        // Open Graph
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($shortDesc ?: 'Read the latest news from ' . $appName);
        OpenGraph::setUrl(route('news.show', $this->news->slug));
        OpenGraph::setType('article');
        if ($imageUrl) {
            OpenGraph::addImage($imageUrl);
        }

        // Twitter
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($shortDesc ?: 'Read the latest news from ' . $appName);
        if ($imageUrl) {
            TwitterCard::setImage($imageUrl);
        }
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $related = News::where('status', 'published')
            ->where('id', '!=', $this->news->id)
            ->with(['translations', 'media', 'category'])
            ->when($this->news->news_category_id, fn ($q) => $q->orderByRaw(
                'CASE WHEN news_category_id = ? THEN 0 ELSE 1 END',
                [$this->news->news_category_id]
            ))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('livewire.public.news-show', [
            'related' => $related,
        ]);
    }
}
