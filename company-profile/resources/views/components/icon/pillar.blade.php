@props([
    'name' => '',
    'size' => 'h-5 w-5',
])

@switch($name)

    {{-- Mutu: biji kopi dengan tanda centang --}}
    @case('quality')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M13.5 3.2c4.1 0 7.3 3.9 7.3 8.7s-3.2 8.7-7.3 8.7-7.3-3.9-7.3-8.7 3.2-8.7 7.3-8.7Z"
                  stroke="currentColor" stroke-width="1.7"/>
            <path d="M13.5 4.8c-2.1 2.1-2.1 4.8 0 7.5s2.1 5.4 0 7.5"
                  stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M2.6 16.4 5 18.8l4.2-4.6" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Kapasitas: karung bertumpuk --}}
    @case('capacity')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 7.2 12 3l9 4.2-9 4.2-9-4.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m3 12 9 4.2 9-4.2M3 16.8 12 21l9-4.2" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Kepatuhan: perisai bertanda centang --}}
    @case('compliance')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 2.8 4.6 5.6v6c0 4.3 3 8.3 7.4 9.6 4.4-1.3 7.4-5.3 7.4-9.6v-6L12 2.8Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m8.8 11.8 2.3 2.3 4.1-4.4" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Logistik: kapal peti kemas --}}
    @case('logistics')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3.2 14.4h17.6l-2.1 5.1a1.6 1.6 0 0 1-1.5 1H6.8a1.6 1.6 0 0 1-1.5-1l-2.1-5.1Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M5.8 14.4V9.2h12.4v5.2M12 9.2V4.4M8.6 4.4h6.8" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

@endswitch
