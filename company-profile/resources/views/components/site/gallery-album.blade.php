@props([
    'album',
    'ratio' => 'aspect-[4/3]',
    'priority' => false,
])

{{-- Semua album tampil sebagai <button> pembuka lightbox, apa pun isinya.
     Video YouTube ikut masuk ke array images berawalan 'youtube:' di
     GalleryIndex.php, jadi lightbox yang mengurus penggambarannya — album
     berisi foto DAN video tidak lagi kehilangan videonya. --}}
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

    {{-- Lencana video muncul kalau album punya setidaknya satu video. --}}
    <x-site.gallery-badge :label="$album->name" :count="$album->count" :is-video="filled($album->video)" />
</button>
