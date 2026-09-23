@props([
    'album',
    'ratio' => 'aspect-[4/3]',
    'priority' => false,
])

<button type="button"
        x-on:click="open(@js($album->images->toArray()), @js($album->name))"
        class="group relative block w-full overflow-hidden rounded-panel bg-site-paper
               transition-shadow duration-300 hover:shadow-[0_24px_48px_-24px_rgba(51,38,25,0.45)]">

    @if($album->cover)
        <img src="{{ $album->cover }}" alt="{{ $album->name }}"
             @if($priority) fetchpriority="high" @else loading="lazy" @endif
             class="{{ $ratio }} w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">

    @else
        <span class="placeholder block {{ $ratio }} w-full"></span>
    @endif

    <x-site.gallery-badge :label="$album->name" :count="$album->count" :is-video="filled($album->video)" />
</button>
