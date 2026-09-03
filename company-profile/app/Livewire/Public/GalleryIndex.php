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
                /*
                 * ->toBase() di dua tempat di bawah bukan hiasan — tanpanya
                 * halaman ini mati dengan 500 untuk SETIAP album yang punya
                 * foto tapi tidak punya video, yaitu keadaan yang biasa.
                 *
                 * Eloquent\Collection::map() cuma turun ke koleksi biasa kalau
                 * HASILNYA mengandung sesuatu yang bukan Model. Untuk hasil
                 * kosong pemeriksaan itu false, jadi kelasnya tetap Eloquent.
                 * Album tanpa video menghasilkan $videos yang kosong tapi
                 * masih ber-kelas Eloquent — dan Eloquent\Collection::merge()
                 * memanggil getKey() pada tiap isi yang dioper kepadanya.
                 * Isi $photos adalah string URL, bukan Model.
                 *
                 * Keduanya memang berisi string, jadi keduanya diturunkan ke
                 * koleksi biasa sejak awal, bukan cuma yang kebetulan kosong.
                 */

                // [PERUBAHAN: YouTube Support] — Pisahkan foto dan video
                $photos = $gallery->items
                    ->filter(fn ($item) => $item->type !== 'video')
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'webp')
                                     ?: $item->getFirstMediaUrl('gallery'))
                    ->toBase()
                    ->filter()
                    ->values();

                // [PERUBAHAN: YouTube Support] — Ambil video URL, beri prefix 'youtube:'
                // agar frontend bisa membedakan antara gambar biasa dan video YouTube
                $videos = $gallery->items
                    ->filter(fn ($item) => $item->type === 'video' && filled($item->video_url))
                    ->map(fn ($item) => 'youtube:' . $item->video_url)
                    ->toBase()
                    ->values();

                // [PERUBAHAN: YouTube Support] — Gabungkan: video tampil pertama, foto setelahnya
                $items = $videos->merge($photos)->values();

                $thumb = $gallery->items
                    ->map(fn ($item) => $item->getFirstMediaUrl('gallery', 'thumb'))
                    ->filter()
                    ->first();

                // [PERUBAHAN: YouTube Support] — Fallback cover ke foto jika tidak ada thumbnail
                $firstPhoto = $photos->first();
                $hasVideo   = $videos->isNotEmpty();

                return (object) [
                    'id'     => $gallery->id,
                    'name'   => $gallery->name,
                    'images' => $items,   // [PERUBAHAN] dulu hanya $images (foto), sekarang gabungan foto+video
                    'count'  => $items->count(),
                    'cover'  => $thumb ?: $firstPhoto,
                    'video'  => $hasVideo ? $videos->first() : null, // [PERUBAHAN] dulu tidak dipakai jika ada foto
                ];
            })
            ->filter(fn ($album) => $album->count > 0)
            ->values();

        return view('livewire.public.gallery-index', [
            /* Isi kepala halaman ini bisa disunting dari menu Halaman;
               yang kosong jatuh ke teks bawaan di berkas bahasa. */
            'isi' => \App\Support\IsiHalaman::untuk('gallery'),

            'featured' => $albums->first(),
            'albums'   => $albums->skip(1)->values(),
        ]);
    }
}
