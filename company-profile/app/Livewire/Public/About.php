<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class About extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        $page    = Page::where('slug', 'about-us')->first();
        $appName = config('app.name');

        $desc = $page
            ? mb_substr(strip_tags($page->translated_content ?? ''), 0, 160)
            : 'Learn about our company, our mission, and why we are a trusted coffee exporter from Indonesia.';

        SEOMeta::setTitle('About Us - ' . $appName);
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical(route('about'));

        OpenGraph::setTitle('About Us - ' . $appName);
        OpenGraph::setDescription($desc);
        OpenGraph::setUrl(route('about'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('About Us - ' . $appName);
        TwitterCard::setDescription($desc);

        return view('livewire.public.about', ['page' => $page]);
    }
}
