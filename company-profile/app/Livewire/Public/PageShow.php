<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class PageShow extends Component
{
    public $page;

    public function mount($slug): void
    {
        $this->page = Page::where('slug', $slug)->firstOrFail();

        $appName  = config('app.name');
        $locale   = app()->getLocale();
        $title    = $this->page->translated_title ?? $this->page->slug;
        $content  = strip_tags($this->page->translated_content ?? '');
        $shortDesc = mb_substr($content, 0, 160) ?: 'Read more about us on ' . $appName;

        SEOMeta::setTitle($title . ' - ' . $appName);
        SEOMeta::setDescription($shortDesc);
        SEOMeta::setCanonical(route('page.show', $slug));

        OpenGraph::setTitle($title . ' - ' . $appName);
        OpenGraph::setDescription($shortDesc);
        OpenGraph::setUrl(route('page.show', $slug));
        OpenGraph::setType('website');

        TwitterCard::setTitle($title . ' - ' . $appName);
        TwitterCard::setDescription($shortDesc);
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.public.page-show');
    }
}
