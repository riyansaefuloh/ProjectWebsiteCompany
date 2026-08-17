@props(['product'])

@php
    $image = $product->getFirstMediaUrl('gallery', 'medium')
          ?: $product->getFirstMediaUrl('gallery');

    $caption = $product->category?->translated_name
            ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->translated_description), 90);

    // Warna teks mengikuti ada-tidaknya foto. Dengan foto, teks berdiri di atas
    // peredam gelap sehingga harus putih; tanpa foto, latarnya bidang terang dan
    // teks putih akan hilang sama sekali.
    $titleColor   = $image ? 'text-white' : 'text-ink';
    $captionColor = $image ? 'text-white/75' : 'text-ink-muted';
@endphp

{{-- ── KARTU PRODUK BERLATAR FOTO ──────────────────────────────────────── --}}
<a href="{{ route('products.show', $product->slug) }}"
   {{ $attributes->merge(['class' => 'group relative isolate flex min-h-[440px] flex-col justify-end overflow-hidden rounded-corner bg-mist-deep p-6 sm:min-h-[480px]']) }}>

    @if($image)
        <img src="{{ $image }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">

        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink/90 via-ink/55 to-transparent" aria-hidden="true"></div>
    @else
        {{-- Tanpa foto: slot berikon kamera, dan peredam gelap TIDAK dipasang.
             Peredam itu gunanya menahan teks putih agar terbaca di atas foto;
             di atas bidang terang tanpa foto ia justru menggelapkan slotnya
             sampai ikonnya nyaris hilang. Karena itu teks kartu ikut berganti
             ke warna ink pada keadaan ini. --}}
        <x-site.image-placeholder class="absolute inset-0 -z-10 h-full w-full" icon="h-12 w-12" />
    @endif

    @if($product->is_featured)
        <span class="absolute left-6 top-6 inline-flex items-center gap-2 rounded-full bg-canvas px-3.5 py-1.5
                     text-[11px] font-bold uppercase tracking-[0.1em] text-ink">
            <span class="h-1.5 w-1.5 rounded-full bg-brand" aria-hidden="true"></span>
            {{ __('site.featured') }}
        </span>
    @endif

    <h3 class="font-display text-[22px] font-extrabold leading-tight tracking-[-0.02em] {{ $titleColor }} sm:text-[24px]">
        {{ $product->translated_name }}
    </h3>

    @if($caption)
        <p class="mt-2.5 line-clamp-2 text-[13px] leading-relaxed {{ $captionColor }}">
            {{ $caption }}
        </p>
    @endif

    <span class="btn btn-arrow mt-6 w-full border border-line bg-canvas text-brand transition-colors group-hover:bg-mist">
        {{ __('site.view_details') }}
        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</a>
