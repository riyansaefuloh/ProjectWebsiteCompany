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

    {{-- Konsistensi: sasaran --}}
    @case('consistency')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8.8" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="12" cy="12" r="4.8" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="12" cy="12" r="1.3" fill="currentColor"/>
        </svg>
        @break

    {{-- Keberlanjutan: daun --}}
    @case('sustainability')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20.4 3.6c0 9.4-4.6 14.2-11 14.2-1.7 0-3.2-.4-4.4-1.1C5 8.5 10.4 3.6 17.6 3.6c1 0 1.9.1 2.8 0Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M3.6 20.4C6.2 14.2 10.2 9.8 16 7.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    {{-- Kemitraan: dua cincin bertaut --}}
    @case('partnership')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="8.6" cy="12" r="5.6" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="15.4" cy="12" r="5.6" stroke="currentColor" stroke-width="1.7"/>
        </svg>
        @break

@endswitch
