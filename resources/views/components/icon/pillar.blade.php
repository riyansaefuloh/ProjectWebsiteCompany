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

    {{-- Asal: tunas berdaun dua — kebun, bukan gudang --}}
    @case('origin')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 21v-9.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M12 13.4c0-3.6 2.7-6.5 6.2-6.5 0 3.6-2.7 6.5-6.2 6.5Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M12 17.2c-3.1 0-5.6-2.5-5.6-5.6 3.1 0 5.6 2.5 5.6 5.6Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Standar: berkas spesifikasi bertanda centang --}}
    @case('standard')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.2 2.8h7.4l4.2 4.4v14H6.2v-18.4Z" stroke="currentColor" stroke-width="1.7"
                  stroke-linejoin="round"/>
            <path d="M13.6 2.8v4.4h4.2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m9.2 14.6 2.1 2.1 3.6-4" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    {{-- Dukungan: gelembung percakapan --}}
    @case('support')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 11.6c0 4-4 7.2-9 7.2-1 0-2-.1-2.9-.4L3.6 20.6l1.6-4.3a6.6 6.6 0 0 1-2.2-4.7c0-4 4-7.2 9-7.2s9 3.2 9 7.2Z"
                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M8.6 11.6h.01M12 11.6h.01M15.4 11.6h.01" stroke="currentColor" stroke-width="2.3"
                  stroke-linecap="round"/>
        </svg>
        @break

@endswitch
