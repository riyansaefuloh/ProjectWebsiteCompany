@props([
    'name' => '',
    'size' => 'h-[18px] w-[18px]',
])

@switch($name)

    @case('linkedin')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.47-.9 1.63-1.85 3.36-1.85 3.59 0 4.26 2.36 4.26 5.44v6.3ZM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13Zm1.78 13.02H3.55V9h3.57v11.45Z"
                  fill="currentColor"/>
        </svg>
        @break

    @case('instagram')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5.2" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="4.1" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="17.4" cy="6.6" r="1.2" fill="currentColor"/>
        </svg>
        @break

    @case('facebook')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M13.95 21v-8h2.71l.4-3.1h-3.11V7.9c0-.9.25-1.5 1.54-1.5h1.66V3.63c-.29-.04-1.28-.12-2.43-.12-2.4 0-4.05 1.47-4.05 4.15v2.24H7.95V13h2.72v8h3.28Z"
                  fill="currentColor"/>
        </svg>
        @break

@endswitch
