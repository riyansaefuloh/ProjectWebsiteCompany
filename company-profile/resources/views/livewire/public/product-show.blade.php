@php
    $image = $product->getFirstMediaUrl('gallery', 'webp')
          ?: $product->getFirstMediaUrl('gallery');
@endphp

<div>
    <section class="pb-20 pt-12 md:pt-16 lg:pb-24 lg:pt-20">
        <div class="shell">

            <a href="{{ route('products.index') }}" class="link-arrow">
                <span class="rotate-180">
                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('site.page_products') }}
            </a>

            {{-- ══════════════════════════════════════════════════════════ --}}
            <div class="mt-8 grid items-start gap-10 lg:mt-10 lg:grid-cols-12 lg:gap-14">

                {{-- ── KIRI: foto ──────────────────────────────────────────── --}}
                <div class="lg:sticky lg:top-[92px] lg:col-span-6">
                    <div class="overflow-hidden rounded-panel bg-mist">

                        @if($product->certifications->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2.5 px-6 pt-6 sm:px-8 sm:pt-8">
                                @foreach($product->certifications as $cert)
                                    <a href="{{ route('certifications.index') }}"
                                       class="chip transition-colors hover:border-brand hover:text-brand">
                                        <span class="mr-2 h-1.5 w-1.5 rounded-full bg-brand" aria-hidden="true"></span>
                                        {{ $cert->translated_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-center px-6 py-10 sm:px-10 lg:py-12">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $product->translated_name }}" fetchpriority="high"
                                     class="max-h-[320px] w-auto max-w-full object-contain sm:max-h-[400px] lg:max-h-[460px]">
                            @else
                                <x-site.image-placeholder icon="h-16 w-16"
                                    class="h-[320px] w-full bg-transparent sm:h-[400px] lg:h-[460px]" />
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── KANAN: nama, deskripsi, spesifikasi, ajakan ──────────── --}}
                <div class="lg:col-span-6">

                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                           class="eyebrow transition-colors hover:text-brand-deep">
                            {{ $product->category->translated_name }}
                        </a>
                    @endif

                    <h1 class="display mt-5 max-w-[18ch] text-[30px] sm:text-[36px] lg:text-[42px]">
                        {{ $product->translated_name }}
                    </h1>

                    @if($product->translated_description)
                        <div class="rich mt-6 max-w-[54ch]">{!! $product->translated_description !!}</div>
                    @endif

                    {{-- ── Lembar spesifikasi ──────────────────────────────── --}}
                    @if($facts->isNotEmpty())
                        <div class="mt-9">
                            <p class="eyebrow">{{ __('site.specifications') }}</p>

                            <dl class="mt-4 divide-y divide-line border-y border-line">
                                @foreach($facts as $fact)
                                    <div class="flex items-baseline justify-between gap-6 py-3.5">
                                        <dt class="shrink-0 text-[13px] text-ink-muted">{{ $fact['label'] }}</dt>
                                        <dd class="text-right text-[14px] font-bold text-ink">{{ $fact['value'] }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <a href="{{ route('inquiry.index', ['product' => $product->id]) }}"
                           class="btn-pill btn-pill-brand">
                            {{ __('site.cta_request_quote') }}
                            <span>
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>

                        @if($waLink)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                                <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                {{ __('site.cta_whatsapp') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
