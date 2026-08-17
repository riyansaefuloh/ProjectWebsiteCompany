@props([
    'icon' => 'h-14 w-14',
])

{{-- ── SLOT FOTO KOSONG ──────────────────────────────────────────────────── --}}
<div {{ $attributes->merge(['class' => 'placeholder']) }} aria-hidden="true">
    <svg class="{{ $icon }} text-ink-faint/50" viewBox="0 0 48 48" fill="none">
        <rect x="5" y="10" width="38" height="28" rx="4" stroke="currentColor" stroke-width="2"/>
        <circle cx="16.5" cy="20" r="3.5" stroke="currentColor" stroke-width="2"/>
        <path d="M6 33.5 17 24l7.5 6.5L32 22l10 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</div>
