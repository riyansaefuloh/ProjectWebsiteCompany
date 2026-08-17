@props([
    'label' => '',
    'isVideo' => false,
    'count' => 0,
])

<span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink/75 via-ink/10 to-transparent"
      aria-hidden="true"></span>

<span class="pointer-events-none absolute inset-x-0 bottom-0 flex items-center gap-2 p-5 text-left">
    @if($isVideo)
        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-ink"
              aria-hidden="true">
            <svg class="ml-0.5 h-3.5 w-3.5" viewBox="0 0 16 16" fill="currentColor">
                <path d="M4.5 2.8 13 8l-8.5 5.2V2.8Z"/>
            </svg>
        </span>
    @endif

    <span class="text-[13px] font-bold text-white">{{ $label }}</span>

    @if($count > 1)
        <span class="rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
            {{ $count }}
        </span>
    @endif
</span>
