@props([
    'name' => '',
    'size' => 'h-4 w-4',
])

@switch($name)

    {{-- Lokasi: penanda peta --}}
    @case('location')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M8 1.6c2.5 0 4.5 2 4.5 4.5 0 3.2-4.5 8.3-4.5 8.3S3.5 9.3 3.5 6.1c0-2.5 2-4.5 4.5-4.5Z"
                  stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
            <circle cx="8" cy="6.1" r="1.7" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        @break

    {{-- Email: amplop --}}
    @case('email')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <rect x="1.8" y="3.4" width="12.4" height="9.2" rx="1.6" stroke="currentColor" stroke-width="1.4"/>
            <path d="m2.4 4.6 5.6 3.9 5.6-3.9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Jam operasional: jam dinding --}}
    @case('clock')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.4"/>
            <path d="M8 4.4V8l2.4 1.6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Telepon: gagang --}}
    @case('phone')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M5.2 2.4 6.6 5 5.4 6.5c.6 1.4 1.7 2.5 3.1 3.1L10 8.4l2.6 1.4v2.4c0 .6-.5 1.1-1.1 1a10 10 0 0 1-8.7-8.7c-.1-.6.4-1.1 1-1.1h2.4Z"
                  stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
        @break

@endswitch
