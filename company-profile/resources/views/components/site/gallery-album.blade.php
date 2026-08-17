@props([
    'album',
    'ratio' => 'aspect-[4/3]',
    'priority' => false,
])

{{--
    [PERUBAHAN: YouTube Support]
    Dulu: ada dua kondisi terpisah —
      1. $isVideoOnly = album HANYA berisi video, tampil sebagai <a href="..."> ke YouTube
      2. Album foto = tampil sebagai <button> yang membuka lightbox
    Masalahnya: jika album punya foto + video, video diabaikan sama sekali.

    Sekarang: semua album selalu tampil sebagai <button> lightbox.
    Video YouTube sudah dimasukkan ke dalam array images (dengan prefix 'youtube:')
    di GalleryIndex.php, sehingga lightbox yang akan mengurus rendernya.
--}}
<button type="button"
        x-on:click="open(@js($album->images->toArray()), @js($album->name))"
        class="group relative block w-full overflow-hidden rounded-corner bg-mist-deep">

    @if($album->cover)
        <img src="{{ $album->cover }}" alt="{{ $album->name }}"
             @if($priority) fetchpriority="high" @else loading="lazy" @endif
             class="{{ $ratio }} w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
    @else
        <span class="placeholder block {{ $ratio }} w-full"></span>
    @endif

    {{-- [PERUBAHAN: YouTube Support] — Badge video muncul jika album punya setidaknya 1 video --}}
    <x-site.gallery-badge :label="$album->name" :count="$album->count" :is-video="filled($album->video)" />
</button>
