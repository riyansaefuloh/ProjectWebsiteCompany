@props([
    'album',
    'ratio' => 'aspect-[4/3]',
    'priority' => false,
])

@php
    $isVideoOnly = $album->count === 0 && filled($album->video);
@endphp

@if($isVideoOnly)
    <a href="{{ $album->video }}" target="_blank" rel="noopener noreferrer"
       class="group relative block overflow-hidden rounded-corner bg-mist-deep">
        @if($album->cover)
            <img src="{{ $album->cover }}" alt="{{ $album->name }}" loading="lazy"
                 class="{{ $ratio }} w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
        @else
            <span class="placeholder block {{ $ratio }} w-full"></span>
        @endif

        <x-site.gallery-badge :label="$album->name" :is-video="true" />
    </a>
@else
    <button type="button"
            x-on:click="open(@js($album->images), @js($album->name))"
            class="group relative block w-full overflow-hidden rounded-corner bg-mist-deep">

        <img src="{{ $album->cover }}" alt="{{ $album->name }}"
             @if($priority) fetchpriority="high" @else loading="lazy" @endif
             class="{{ $ratio }} w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">

        <x-site.gallery-badge :label="$album->name" :count="$album->count" />
    </button>
@endif
