@props([
    'label' => '',
    'isVideo' => false,
    'count' => 0,
])

<span class="pointer-events-none absolute inset-x-0 bottom-0 h-[55%]
             bg-gradient-to-t from-site-shade/85 via-site-shade/45 to-transparent"
      aria-hidden="true"></span>

<span class="pointer-events-none absolute inset-x-0 bottom-0 p-5 text-left">
    
    @if($count > 0)
        <span class="flex items-center gap-2 font-site-accent text-site-micro font-medium
                     uppercase tracking-[0.16em] text-site-gilt">
            @if($isVideo)
                
                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                             bg-site-gilt text-site-forest" aria-hidden="true">
                    <svg class="ml-px h-2.5 w-2.5" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M4.5 2.8 13 8l-8.5 5.2V2.8Z"/>
                    </svg>
                </span>
            @endif

            {{ trans_choice('site.album_count', $count, ['count' => $count]) }}
        </span>
    @endif

    <span class="mt-1.5 block font-site-display text-site-title font-bold leading-snug
                 tracking-[-0.01em] text-white">
        {{ $label }}
    </span>
</span>
