@props([
    'name' => '',
    'size' => 'h-6 w-6',
])

@switch($name)

    {{-- Integritas: neraca --}}
    @case('integrity')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3.4v17.2M7.4 20.6h9.2M4.2 7.2l15.6-1.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M4.2 7.2 1.8 13.4a2.6 2.6 0 0 0 4.8 0L4.2 7.2ZM19.8 5.4l-2.4 6.2a2.6 2.6 0 0 0 4.8 0l-2.4-6.2Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        </svg>
        @break

    @case('quality')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="9.6" r="6.6" stroke="currentColor" stroke-width="1.7"/>
            <path d="m9.3 9.6 2 2 3.4-3.7" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8.4 15.3 7 21.6l5-2.4 5 2.4-1.4-6.3" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Kemitraan: dua cincin bertaut --}}
    @case('partnership')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="8.6" cy="12" r="5.6" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="15.4" cy="12" r="5.6" stroke="currentColor" stroke-width="1.7"/>
        </svg>
        @break

    @case('responsibility')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3.4 14.6c2.4-1.4 4.6-1.4 6.6 0h3.4a1.7 1.7 0 0 1 0 3.4h-3"
                  stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18h4.8l5.1-2.3a1.7 1.7 0 0 1 1.5 3l-6.6 3.4c-1 .5-2.1.6-3.2.3l-8.2-2.2"
                  stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.4 10.6c-2.6 0-4.6-2-4.6-4.6 2.6 0 4.6 2 4.6 4.6ZM13.4 10.6c0-3 2.4-5.4 5.4-5.4 0 3-2.4 5.4-5.4 5.4Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        </svg>
        @break

@endswitch
