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
                $photos = $gallery->items
                    ->filter(fn ($item) => $item->type !== 'video')
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'webp')
                                     ?: $item->getFirstMediaUrl('gallery'))
                    ->toBase()
                    ->filter()
                    ->values();

                $videos = $gallery->items
                    ->filter(fn ($item) => $item->type === 'video' && filled($item->video_url))
                    ->map(fn ($item) => 'youtube:' . $item->video_url)
                    ->toBase()
                    ->values();

                $items = $videos->merge($photos)->values();

                $thumb = $gallery->items
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'thumb'))
                    ->filter()
                    ->first();

                $firstPhoto = $photos->first();
                $hasVideo   = $videos->isNotEmpty();

                return (object) [
                    'id'     => $gallery->id,
                    'name'   => $gallery->name,
                    'images' => $items,
                    'count'  => $items->count(),
                    'cover'  => $thumb ?: $firstPhoto,
                    'video'  => $hasVideo ? $videos->first() : null,
                ];
            })
            ->filter(fn ($album) => $album->count > 0)
            ->values();

        return view('livewire.public.gallery-index', [
            'isi'          => \App\Support\IsiHalaman::untuk('gallery'),
            'albums'       => $albums,
            'videoSematan' => \App\Support\Youtube::sematan(
                \App\Support\IsiHalaman::opsi('gallery')['video_url'] ?? null
            ),
        ]);
    }
}
