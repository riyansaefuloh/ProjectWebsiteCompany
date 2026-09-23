@php
    
@endphp

<span class="relative inline-flex shrink-0 items-center">
    <input type="checkbox" {{ $attributes->class(['peer sr-only']) }}>

    <span class="flex h-[18px] w-[18px] items-center justify-center rounded border
                 border-line-strong bg-canvas text-transparent transition-colors
                 peer-checked:border-brand peer-checked:bg-brand peer-checked:text-white
                 peer-focus-visible:ring-2 peer-focus-visible:ring-brand/30
                 peer-focus-visible:ring-offset-1"
          aria-hidden="true">
        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none">
            <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</span>
