@props([
    'product',
    'label' => null,
])

@php
    $image = $product->getFirstMediaUrl('gallery', 'medium')
          ?: $product->getFirstMediaUrl('gallery');

    $caption = $product->category?->translated_name
            ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->translated_description), 90);

    $titleColor   = $image ? 'text-white' : 'text-ink';
    $captionColor = $image ? 'text-white/85' : 'text-ink-muted';
@endphp

{{-- ── KARTU PRODUK BERLATAR FOTO ──────────────────────────────────────── --}}
<a href="{{ route('products.show', $product->slug) }}"
   {{ $attributes->merge(['class' => 'group relative isolate flex min-h-[440px] flex-col justify-end overflow-hidden rounded-corner bg-mist-deep p-6 sm:min-h-[480px]']) }}>

    @if($image)
        <img src="{{ $image }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">

        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink/90 via-ink/55 to-transparent" aria-hidden="true"></div>
    @else
        
        <x-site.image-placeholder class="absolute inset-0 -z-10 h-full w-full" icon="h-12 w-12" />
    @endif

    {{-- ── Pil Unggulan ── --}}
    @if($product->is_featured)
        <span class="absolute left-6 top-6 inline-flex items-center rounded-full
                     bg-site-gilt px-3.5 py-1.5 font-site-accent text-site-micro
                     font-medium uppercase tracking-[0.14em] text-site-forest">
            {{ __('site.featured') }}
        </span>
    @endif

    {{-- ── Kategori & Nama ── --}}
    @if($caption)
        <p class="line-clamp-2 leading-relaxed {{ $captionColor }} text-site-lede">
            {{ $caption }}
        </p>
    @endif

    <h3 class="mt-1.5 font-site-display font-bold leading-tight tracking-[-0.015em] {{ $titleColor }} text-site-h3">
        {{ $product->translated_name }}
    </h3>

    {{-- ── Tombol rincian ── --}}
    <span class="mt-6 inline-flex h-10 w-max items-center gap-3 self-end rounded-full pl-5 pr-1.5
                 bg-site-shade/45 text-white ring-1 ring-white/25
                 font-site-body text-site-small font-semibold whitespace-nowrap
                 transition-colors duration-300 group-hover:bg-site-shade/65">
        {{ $label ?: __('site.view_details') }}
        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                     bg-site-gilt text-site-forest transition-transform duration-200
                     group-hover:translate-x-0.5">
            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    </span>
</a>
