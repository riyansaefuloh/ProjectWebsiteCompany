<div>
    @if(empty($homeSections))
        <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; margin: 30px;">
            <p>{{ __('site.no_home_sections') }}</p>
        </div>
    @endif

    @foreach($homeSections as $section)
        @switch($section['id'])
            @case('hero')
                @php
                    $heroImage = !empty($settings['hero_image'])
                        ? \Illuminate\Support\Facades\Storage::url($settings['hero_image'])
                        : null;
                @endphp

                {{-- ── HERO ────────────────────────────────────────────────── --}}
                <section class="pb-14 pt-14 md:pb-16 md:pt-20 lg:pb-20 lg:pt-24">
                    <div class="shell">

                        <h1 class="display max-w-[19ch] text-[34px] leading-[1.15] sm:text-[42px] md:text-[48px] lg:text-[56px]">
                            {{ $heroPage?->translated_title ?: __('site.hero_title') }}
                        </h1>

                        @if($heroPage?->translated_content)
                            <div class="rich mt-7 max-w-[54ch]">{!! $heroPage->translated_content !!}</div>
                        @else
                            <p class="lede mt-7 max-w-[54ch]">{{ __('site.hero_body') }}</p>
                        @endif

                        <div class="mt-10 flex flex-col gap-8 lg:mt-14 lg:flex-row lg:items-end lg:justify-between lg:gap-10">

                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('inquiry.index') }}" class="btn-pill btn-pill-brand">
                                    {{ __('site.cta_request_quote') }}
                                    <span>
                                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>

                                <a href="{{ route('products.index') }}" class="btn btn-outline btn-arrow">
                                    {{ __('site.cta_explore_products') }}
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>

                            <p class="flex items-start gap-4 lg:max-w-[26ch]">
                                <svg class="mt-1 h-6 w-6 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 5.5 12 11l7-5.5M5 13l7 5.5 7-5.5"
                                          stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="font-display text-[18px] font-extrabold leading-snug tracking-[-0.02em] text-ink sm:text-[20px]">
                                    {{ __('site.hero_descriptor') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </section>

                {{-- ── Band foto melintang penuh ───────────────────────────── --}}
                <div class="relative">
                    @if($heroImage)
                        <img src="{{ $heroImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="h-[300px] w-full object-cover sm:h-[400px] lg:h-[520px]">
                    @else
                        {{-- ── Keadaan "foto belum diunggah" ──────────────────── --}}
                        <x-site.image-placeholder class="h-[300px] w-full sm:h-[400px] lg:h-[520px]" icon="h-14 w-14" />
                    @endif

                    {{-- ── Angka pengalaman, pojok kiri bawah ──────────────── --}}
                    <div class="pointer-events-none absolute inset-x-0 bottom-0">
                        <div class="shell">
                            <div class="mb-6 inline-flex flex-col rounded-panel bg-canvas/95 px-7 py-6 backdrop-blur
                                        shadow-[0_18px_44px_-24px_rgba(26,29,27,0.5)] sm:mb-8 sm:px-9 sm:py-7">
                                <span class="font-display text-[40px] font-extrabold leading-none tracking-[-0.03em] text-brand sm:text-[52px]">
                                    {{ $yearsOfExperience }}+
                                </span>
                                <span class="mt-3 text-[13px] font-bold leading-none text-ink sm:text-[15px]">
                                    {{ __('site.hero_years') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 CERTIFICATIONS BAR
                 ══════════════════════════════════════════════════════════ --}}
            @case('certifications')
                @if($certifications->isNotEmpty())
                    <section class="py-10 md:py-14">

                        <div class="group relative overflow-hidden
                                    [-webkit-mask-image:linear-gradient(to_right,transparent,#000_7%,#000_93%,transparent)]
                                    [mask-image:linear-gradient(to_right,transparent,#000_7%,#000_93%,transparent)]">

                            <ul class="flex w-max animate-marquee items-center group-hover:[animation-play-state:paused]"
                                aria-hidden="true">
                                @for($copy = 0; $copy < 4; $copy++)
                                    @foreach($certifications as $cert)
                                        @php
                                            $certLogo = $cert->getFirstMediaUrl('logos', 'thumb')
                                                     ?: $cert->getFirstMediaUrl('logos');
                                        @endphp

                                        <li class="flex h-12 shrink-0 items-center justify-center px-8 sm:px-12">
                                            @if($certLogo)
                                                <img src="{{ $certLogo }}" alt="" loading="lazy"
                                                     class="h-10 w-auto max-w-[160px] object-contain opacity-60 grayscale
                                                            transition duration-200 group-hover:opacity-100 group-hover:grayscale-0">
                                            @else
                                                <span class="flex flex-col justify-center text-center">
                                                    <span class="whitespace-nowrap font-display text-[15px] font-extrabold tracking-[-0.01em] text-ink">
                                                        {{ $cert->translated_name }}
                                                    </span>
                                                    @if($cert->issuer)
                                                        <span class="mt-1 whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.12em] text-ink-faint">
                                                            {{ $cert->issuer }}
                                                        </span>
                                                    @endif
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                @endfor
                            </ul>
                        </div>

                        <ul class="sr-only">
                            @foreach($certifications as $cert)
                                <li>{{ $cert->translated_name }}@if($cert->issuer) — {{ $cert->issuer }}@endif</li>
                            @endforeach
                        </ul>
                    </section>
                @endif
                @break

            @case('products')
                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-7">
                                <p class="eyebrow">{{ __('site.home_section_products') }}</p>
                                <h2 class="display mt-5 max-w-[18ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                                    {{ __('site.products_title') }}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:self-end">
                                <p class="lede max-w-[46ch]">{{ __('site.products_body') }}</p>

                                <a href="{{ route('products.index') }}" class="btn btn-outline btn-arrow mt-7">
                                    {{ __('site.cta_explore_products') }}
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        @if($featuredProducts->isNotEmpty())
                            <ul class="mt-12 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3">
                                @foreach($featuredProducts as $product)
                                    <li class="flex">
                                        <x-site.product-card :product="$product" class="w-full" />
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ __('site.no_featured_products') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            @case('export_markets')
                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="mx-auto max-w-[46rem] text-center">
                            <p class="eyebrow">{{ __('site.home_section_export_markets') }}</p>
                            <h2 class="display mx-auto mt-5 max-w-[20ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                                {{ __('site.markets_title', ['count' => $exportMarkets->count()]) }}
                            </h2>
                            <p class="lede mx-auto mt-5 max-w-[52ch]">{{ __('site.markets_body') }}</p>
                        </div>

                        @if($exportMarkets->isNotEmpty())
                            <div class="mt-12 lg:mt-14">
                                <x-site.export-map :markets="$exportMarkets" :show-list="false" />
                            </div>

                            <div class="mt-10 flex justify-center">
                                <a href="{{ route('export-markets.index') }}" class="btn btn-outline btn-arrow">
                                    {{ __('site.cta_explore_markets') }}
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ __('site.no_export_markets') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 WHY CHOOSE US
                 ══════════════════════════════════════════════════════════ --}}
            @case('about')
                @php
                    $pillars = [
                        ['icon' => 'quality',    'title' => __('site.pillar_1_title'), 'body' => __('site.pillar_1_body')],
                        ['icon' => 'capacity',   'title' => __('site.pillar_2_title'), 'body' => __('site.pillar_2_body')],
                        ['icon' => 'compliance', 'title' => __('site.pillar_3_title'), 'body' => __('site.pillar_3_body')],
                        ['icon' => 'logistics',  'title' => __('site.pillar_4_title'), 'body' => __('site.pillar_4_body')],
                    ];
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-6">
                                <p class="eyebrow">{{ __('site.pillars_eyebrow') }}</p>
                                <h2 class="display mt-5 max-w-[16ch] text-[28px] sm:text-[34px] lg:text-[40px]">
                                    {{ __('site.pillars_title') }}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                                <p class="lede max-w-[46ch]">{{ __('site.pillars_body') }}</p>
                            </div>
                        </div>

                        <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                                   [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8 lg:mt-14">
                            @foreach($pillars as $pillar)
                                <li class="w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)] lg:w-[calc(25%-0.9375rem)]">
                                    <div class="card group flex h-full min-h-[260px] flex-col justify-between p-6
                                                transition-colors duration-300 hover:border-forest hover:bg-forest sm:min-h-[290px]">

                                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full
                                                     bg-brand text-white transition-colors duration-300
                                                     group-hover:bg-canvas group-hover:text-forest">
                                            <x-icon.pillar :name="$pillar['icon']" />
                                        </span>

                                        <div class="mt-10">
                                            <h3 class="font-display text-[16px] font-extrabold leading-snug tracking-[-0.01em] text-ink
                                                       transition-colors duration-300 group-hover:text-white">
                                                {{ $pillar['title'] }}
                                            </h3>
                                            <p class="mt-2.5 text-[13px] leading-relaxed text-ink-muted
                                                      transition-colors duration-300 group-hover:text-white/70">
                                                {{ $pillar['body'] }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
                @break

            @case('news')
                <section class="section bg-forest">
                    <div class="shell">
                        <p class="eyebrow eyebrow-invert">{{ __('site.news_eyebrow') }}</p>
                        <h2 class="mt-5 max-w-[20ch] font-display text-[28px] font-extrabold leading-[1.22] tracking-[-0.02em] text-white sm:text-[34px] lg:text-[40px]">
                            {{ __('site.news_title') }}
                        </h2>

                        @if($latestNews->isNotEmpty())
                            <div class="mt-12 grid items-stretch gap-6 lg:mt-14 lg:grid-cols-12">

                                <div class="flex flex-col gap-6 lg:col-span-7">
                                    @foreach($latestNews->take(2) as $article)
                                        @php
                                            $cover = $article->getFirstMediaUrl('covers', 'thumb')
                                                  ?: $article->getFirstMediaUrl('covers', 'webp');
                                        @endphp

                                        <article class="flex flex-1 flex-col overflow-hidden rounded-corner sm:flex-row">
                                            <a href="{{ route('news.show', $article->slug) }}"
                                               class="relative block h-[190px] shrink-0 overflow-hidden bg-forest-line sm:h-auto sm:w-[38%]"
                                               tabindex="-1" aria-hidden="true">
                                                @if($cover)
                                                    <img src="{{ $cover }}" alt="" loading="lazy"
                                                         class="absolute inset-0 h-full w-full object-cover">
                                                @endif
                                            </a>

                                            <div class="flex min-w-0 flex-1 flex-col bg-forest-line/40 px-6 py-6">
                                                @if($article->published_at)
                                                    <p class="flex items-center gap-2 text-[12px] text-white/50">
                                                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-soft" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                            <rect x="2.2" y="3.4" width="11.6" height="10.4" rx="1.6" stroke="currentColor" stroke-width="1.3"/>
                                                            <path d="M2.2 6.6h11.6M5.6 2.2v2.4M10.4 2.2v2.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                                        </svg>
                                                        <time datetime="{{ $article->published_at->toDateString() }}">
                                                            {{ $article->published_at->translatedFormat('d F Y') }}
                                                        </time>
                                                    </p>
                                                @endif

                                                <h3 class="mt-3 font-display text-[17px] font-extrabold leading-snug tracking-[-0.01em] text-white">
                                                    <a href="{{ route('news.show', $article->slug) }}" class="transition-colors hover:text-brand-soft">
                                                        {{ $article->translated_title }}
                                                    </a>
                                                </h3>

                                                @if($article->translated_excerpt)
                                                    <p class="mt-2.5 line-clamp-2 text-[13px] leading-relaxed text-white/60">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 120) }}
                                                    </p>
                                                @endif

                                                <a href="{{ route('news.show', $article->slug) }}" class="link-arrow link-arrow-invert mt-auto pt-6">
                                                    {{ __('site.read_article') }}
                                                    <span>
                                                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>

                                @php
                                    $promo = $latestNews->get(2) ?? $latestNews->first();
                                    $promoCover = $promo?->getFirstMediaUrl('covers', 'webp')
                                               ?: $promo?->getFirstMediaUrl('covers', 'thumb');
                                @endphp

                                <a href="{{ route('news.index') }}"
                                   class="group relative isolate flex min-h-[320px] flex-col justify-end overflow-hidden rounded-corner bg-forest-line p-7 lg:col-span-5">
                                    @if($promoCover)
                                        <img src="{{ $promoCover }}" alt="" aria-hidden="true" loading="lazy"
                                             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
                                    @endif

                                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest via-forest/70 to-forest/20" aria-hidden="true"></div>

                                    <p class="max-w-[16ch] font-display text-[22px] font-extrabold leading-snug tracking-[-0.02em] text-white sm:text-[25px]">
                                        {{ __('site.news_promo_title') }}
                                    </p>

                                    <span class="btn-pill btn-pill-invert mt-7 self-start">
                                        {{ __('site.cta_see_more_news') }}
                                        <span>
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        @else
                            <p class="mt-12 rounded-corner border border-dashed border-forest-line px-6 py-14 text-center text-[15px] text-white/60">
                                {{ __('site.no_news_found') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 CLOSING CTA BANNER
                 ══════════════════════════════════════════════════════════ --}}
            @case('contact')
                @php
                    $whatsapp = $settings['whatsapp_number'] ?? '';
                    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;

                    $ctaImage = !empty($settings['cta_image'])
                        ? \Illuminate\Support\Facades\Storage::url($settings['cta_image'])
                        : null;
                @endphp

                <section class="pb-16 pt-16 md:pb-20 md:pt-20 lg:pb-24 lg:pt-24">
                    <div class="shell">
                        <div class="relative isolate overflow-hidden rounded-panel bg-forest px-6 py-16 text-center sm:px-10 md:py-20 lg:py-24">

                            @if($ctaImage)
                                <img src="{{ $ctaImage }}" alt="" aria-hidden="true" loading="lazy"
                                     class="absolute inset-0 -z-10 h-full w-full object-cover">
                                <div class="absolute inset-0 -z-10 bg-forest/85" aria-hidden="true"></div>
                            @endif

                            <h2 class="mx-auto max-w-[18ch] font-display text-[28px] font-extrabold leading-[1.2] tracking-[-0.02em] text-white sm:text-[34px] lg:text-[42px]">
                                {{ __('site.cta_title') }}
                            </h2>

                            <p class="mx-auto mt-6 max-w-[56ch] text-[15px] leading-relaxed text-white/70 sm:text-[16px]">
                                {{ __('site.cta_body') }}
                            </p>

                            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('inquiry.index') }}" class="btn-pill">
                                    {{ __('site.cta_request_quote') }}
                                    <span>
                                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>

                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-outline-invert">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                        {{ __('site.cta_whatsapp') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
                @break

            @default
                <!-- OTHER SECTIONS -->
                <div style="margin-bottom: 50px; padding: 20px; border: 1px dashed #94a3b8; background: #f1f5f9;">
                    <h2>[{{ __('site.home_section_' . str_replace('-', '_', $section['id'])) }}]</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Buat UI untuk seksi {{ __('site.home_section_' . str_replace('-', '_', $section['id'])) }} di sini.]
                    </div>
                </div>
                @break
        @endswitch
    @endforeach
</div>
