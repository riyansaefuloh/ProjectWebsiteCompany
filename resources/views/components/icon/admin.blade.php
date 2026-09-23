@props([
    'name',
    'size' => 'h-[18px] w-[18px]',
])

@switch($name)

    @case('dashboard')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.2 8.6 10 3.2l6.8 5.4v7.2a1.2 1.2 0 0 1-1.2 1.2H4.4a1.2 1.2 0 0 1-1.2-1.2z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7.8 17V11h4.4v6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('inquiry')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3 5.6a1.6 1.6 0 0 1 1.6-1.6h10.8A1.6 1.6 0 0 1 17 5.6v6.8a1.6 1.6 0 0 1-1.6 1.6H7.6L4 17z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('product')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 2.8 16.6 6v8L10 17.2 3.4 14V6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M3.4 6 10 9.4 16.6 6M10 9.4v7.8" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('category')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="10.8" y="3" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="3" y="10.8" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
            <rect x="10.8" y="10.8" width="6.2" height="6.2" rx="1.6" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('certification')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="8" r="4.6" stroke="currentColor" stroke-width="1.5"/>
            <path d="M7 12.2 6 17.4l4-2 4 2-1-5.2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('market')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3 10h14M10 3c1.9 2 2.9 4.4 2.9 7s-1 5-2.9 7c-1.9-2-2.9-4.4-2.9-7s1-5 2.9-7Z"
                  stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('news')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M4 4.6A1.6 1.6 0 0 1 5.6 3h8.8A1.6 1.6 0 0 1 16 4.6v10.8a1.6 1.6 0 0 1-1.6 1.6H5.6A1.6 1.6 0 0 1 4 15.4z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7 7h6M7 10h6M7 13h3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('gallery')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.8" y="4.2" width="14.4" height="11.6" rx="1.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="m4 13.4 3.6-3.4 3 2.6 2.6-2.4L16 12.8" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="7.4" cy="7.8" r="1.1" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        @break

    @case('page')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.4 2.8H5.8A1.6 1.6 0 0 0 4.2 4.4v11.2a1.6 1.6 0 0 0 1.6 1.6h8.4a1.6 1.6 0 0 0 1.6-1.6V7z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M11.4 2.8V7h4.4" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('download')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 3.4v8.2m0 0L6.8 8.4M10 11.6l3.2-3.2" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3.6 13v2.2a1.6 1.6 0 0 0 1.6 1.6h9.6a1.6 1.6 0 0 0 1.6-1.6V13"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('settings')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="10" r="2.6" stroke="currentColor" stroke-width="1.5"/>
            <path d="M15.9 12.2a1.3 1.3 0 0 0 .26 1.44l.05.05a1.6 1.6 0 1 1-2.26 2.26l-.05-.05a1.3 1.3 0 0 0-1.44-.26 1.3 1.3 0 0 0-.79 1.19v.13a1.6 1.6 0 0 1-3.2 0v-.07a1.3 1.3 0 0 0-.85-1.19 1.3 1.3 0 0 0-1.44.26l-.05.05a1.6 1.6 0 1 1-2.26-2.26l.05-.05a1.3 1.3 0 0 0 .26-1.44 1.3 1.3 0 0 0-1.19-.79H2.8a1.6 1.6 0 0 1 0-3.2h.07a1.3 1.3 0 0 0 1.19-.85 1.3 1.3 0 0 0-.26-1.44l-.05-.05A1.6 1.6 0 1 1 6.01 3.7l.5.05a1.3 1.3 0 0 0 1.44.26h.06a1.3 1.3 0 0 0 .79-1.19V2.8a1.6 1.6 0 0 1 3.2 0v.07a1.3 1.3 0 0 0 .79 1.19 1.3 1.3 0 0 0 1.44-.26l.05-.05a1.6 1.6 0 1 1 2.26 2.26l-.5.05a1.3 1.3 0 0 0-.26 1.44v.06a1.3 1.3 0 0 0 1.19.79h.13a1.6 1.6 0 0 1 0 3.2h-.07a1.3 1.3 0 0 0-1.19.79Z"
                  stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
        </svg>
        @break

    @case('user')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="10" cy="6.8" r="3.2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3.8 16.8c0-3.1 2.8-5 6.2-5s6.2 1.9 6.2 5" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('users')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2.6 16.4c0-2.8 2.4-4.6 5.4-4.6s5.4 1.8 5.4 4.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 4.4a3 3 0 0 1 0 5.4M15.4 11.9c1.4.6 2.4 1.9 2.4 3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('chart')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 2.8v12.4a1.6 1.6 0 0 0 1.6 1.6h11.6" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
            <path d="m6.2 12.4 3-3.4 2.6 2 3.6-4.6" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('bell')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M5.4 8.4a4.6 4.6 0 0 1 9.2 0c0 3.4 1.2 4.6 1.2 4.6H4.2s1.2-1.2 1.2-4.6Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M8.4 15.4a1.8 1.8 0 0 0 3.2 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('logout')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M8 17H5a1.6 1.6 0 0 1-1.6-1.6V4.6A1.6 1.6 0 0 1 5 3h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M12.6 13.4 16 10l-3.4-3.4M16 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('external')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11 3.6h5.4V9M16.4 3.6 9.2 10.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.2 12v3.4a1.6 1.6 0 0 1-1.6 1.6H4.6A1.6 1.6 0 0 1 3 15.4V6.4a1.6 1.6 0 0 1 1.6-1.6H8"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('search')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <circle cx="8.8" cy="8.8" r="5.2" stroke="currentColor" stroke-width="1.5"/>
            <path d="m12.6 12.6 3.8 3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('filter')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 4.4h13.2l-5 5.8v5l-3.2 1.6v-6.6z" stroke="currentColor"
                  stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('manage')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.4 6.8h4.2M11.4 6.8h5.2M3.4 13.2h5.2M12.4 13.2h4.2"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="9.5" cy="6.8" r="1.9" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="10.5" cy="13.2" r="1.9" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        @break

    @case('edit')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M13.2 3.6a1.7 1.7 0 0 1 2.4 2.4l-8 8-3.2.8.8-3.2z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
        </svg>
        @break

    @case('trash')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3.6 5.6h12.8M8 5.6V4.2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1.4"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M5.4 5.6l.7 9.4a1.6 1.6 0 0 0 1.6 1.5h4.6a1.6 1.6 0 0 0 1.6-1.5l.7-9.4"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M8.4 8.6v4.8M11.6 8.6v4.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        @break

    @case('star')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/>
        </svg>
        @break

    @case('pdf')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M11.4 2.8H6a1.6 1.6 0 0 0-1.6 1.6v11.2A1.6 1.6 0 0 0 6 17.2h8a1.6 1.6 0 0 0 1.6-1.6V7z"
                  stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M11.4 2.8V7h4.2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M7.4 11.4h5.2M7.4 14h3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        @break

    @case('mail')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.6" y="4.4" width="14.8" height="11.2" rx="1.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="m3.4 5.8 5.7 4.3a1.5 1.5 0 0 0 1.8 0l5.7-4.3" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('send')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M17.4 2.6 2.6 8.4l6.3 2.5 2.7 6.5z" stroke="currentColor"
                  stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M17.4 2.6 8.9 10.9" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('whatsapp')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/>
        </svg>
        @break

    @case('close')
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="m5.6 5.6 8.8 8.8M14.4 5.6l-8.8 8.8" stroke="currentColor"
                  stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @break

    @case('panel')
        
        <svg {{ $attributes->merge(['class' => $size]) }} viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <rect x="2.8" y="3.6" width="14.4" height="12.8" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8 3.6v12.8" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2.8 5.6a2 2 0 0 1 2-2H8v12.8H4.8a2 2 0 0 1-2-2z" fill="currentColor" opacity="0.28"/>
        </svg>
        @break

@endswitch
