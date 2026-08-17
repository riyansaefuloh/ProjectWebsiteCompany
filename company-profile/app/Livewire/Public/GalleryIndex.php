<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Gallery;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class GalleryIndex extends Component
{
    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Gallery - ' . $appName);
        SEOMeta::setDescription('View our photo gallery showcasing our coffee processing facilities, product photos, export activities, and company events.');
        SEOMeta::setCanonical(route('gallery.index'));

        OpenGraph::setTitle('Gallery - ' . $appName);
        OpenGraph::setDescription('Photo gallery of ' . $appName . ' — coffee processing, products, and export activities.');
        OpenGraph::setUrl(route('gallery.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Gallery - ' . $appName);
        TwitterCard::setDescription('Photo gallery of our coffee export company.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $albums = Gallery::with('items.media')->get()
            ->map(function ($gallery) {
                $images = $gallery->items
                    ->filter(fn ($item) => $item->type !== 'video')
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'webp')
                                     ?: $item->getFirstMediaUrl('gallery'))
                    ->filter()
                    ->values();

                $thumb = $gallery->items
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'thumb'))
                    ->filter()
                    ->first();

                $video = $gallery->items
                    ->filter(fn ($item) => $item->type === 'video')
                    ->pluck('video_url')
                    ->filter()
                    ->first();

                return (object) [
                    'id'     => $gallery->id,
                    'name'   => $gallery->name,
                    'images' => $images,
                    'count'  => $images->count(),
                    'cover'  => $thumb ?: $images->first(),
                    'video'  => $video,
                ];
            })
            ->filter(fn ($album) => $album->count > 0 || filled($album->video))
            ->values();

        return view('livewire.public.gallery-index', [
            'featured' => $albums->first(),
            'albums'   => $albums->skip(1)->values(),
        ]);
    }
}
