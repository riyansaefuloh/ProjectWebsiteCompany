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
                    $heroBody = $isi('hero', 'body', 'site.hero_body');
                    $heroAlamat = $gambarBagian['hero'] ?? ($settings['hero_image'] ?? null);

                    $heroImage = !empty($heroAlamat)
                        ? \Illuminate\Support\Facades\Storage::url($heroAlamat)
                        : null;
                @endphp

                {{-- ── HERO SECTION ────────────────────────────────────────────── --}}
                <section class="relative isolate flex h-[100svh] min-h-[540px] items-center
                                overflow-hidden bg-site-shade pt-[76px]">

                    @if($heroImage)
                        <img src="{{ $heroImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="absolute inset-0 -z-20 h-full w-full object-cover object-[32%_center] lg:object-center">
                    @else
                        <div class="absolute inset-0 -z-20 bg-[radial-gradient(1200px_500px_at_15%_-10%,#3d2f1f_0%,transparent_60%),radial-gradient(900px_420px_at_85%_10%,#2b2015_0%,transparent_62%)]"></div>
                    @endif

                    {{-- Overlay Gradients --}}
                    <div aria-hidden="true"
                         class="absolute inset-0 -z-10 bg-site-shade/58 lg:hidden"></div>
                    <div aria-hidden="true"
                         class="absolute inset-0 -z-10 hidden bg-gradient-to-r
                                from-site-shade/62 via-site-shade/58 via-58% to-site-shade/8 lg:block"></div>

                    <div aria-hidden="true"
                         class="absolute inset-x-0 bottom-0 -z-10 h-[38%] bg-gradient-to-t
                                from-site-shade/45 to-transparent"></div>

                    <div class="shell w-full py-14 sm:py-16 lg:py-20">
                        <div class="max-w-[44rem]">

                            {{-- Label & Judul --}}
                            <p class="eyebrow eyebrow-invert">
                                {{ $isi('hero', 'descriptor', 'site.hero_descriptor') }}
                            </p>

                            <h1 class="display display-invert mt-6 max-w-[19ch] text-site-hero">
                                {!! \App\Support\Judul::sorot($isi('hero', 'title', 'site.hero_title')) !!}
                            </h1>

                            @if($heroBody !== strip_tags($heroBody))
                                <div class="rich rich-invert mt-6 max-w-[52ch]">{!! $heroBody !!}</div>
                            @else
                                <p class="lede mt-6 max-w-[52ch] text-white/70">{{ $heroBody }}</p>
                            @endif

                            {{-- Tombol CTA Hero --}}
                            <div class="mt-9 flex flex-wrap items-center gap-3 lg:mt-11">
                                <a href="{{ route('inquiry.index') }}"
                                   class="inline-flex h-10 items-center rounded-full bg-site-gilt px-6
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          text-site-forest ring-1 ring-site-gilt-deep/70
                                          shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                                          transition-colors duration-300 hover:bg-site-gilt-soft">
                                    {{ $isi('hero', 'cta_primary', 'site.cta_request_quote') }}
                                </a>

                                <a href="{{ route('products.index') }}"
                                   class="group inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          bg-site-shade/45 text-white ring-1 ring-white/25
                                          transition-colors duration-300 hover:bg-site-shade/65">
                                    {{ $isi('hero', 'cta_secondary', 'site.cta_explore_products') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- ── SECTION: CERTIFICATIONS BAR ──────────────────────────── --}}
            @case('certifications')
                @if($certifications->isNotEmpty())
                    @php
                        $ulangan = (int) ceil(8 / max($certifications->count(), 1));
                        $deret = collect(range(1, $ulangan))
                            ->flatMap(fn () => $certifications)
                            ->all();
                    @endphp

                    <section class="bg-site-canvas">
                        
                        <div class="shell py-7 lg:py-9">
                            <div class="marquee-fade overflow-hidden">

                            <div class="group flex w-max animate-marquee items-center
                                        hover:[animation-play-state:paused]">
                                @foreach([false, true] as $salinan)
                                    
                                    <ul class="flex shrink-0 items-center gap-x-12 pr-12 lg:gap-x-16 lg:pr-16"
                                        @if($salinan) aria-hidden="true" @endif>
                                @foreach($deret as $cert)
                                    @php
                                        $logoCert = $cert->getFirstMediaUrl('logos', 'thumb')
                                                 ?: $cert->getFirstMediaUrl('logos');
                                    @endphp

                                    <li class="flex h-9 w-[108px] shrink-0 items-center justify-center
                                               lg:h-10 lg:w-[128px]">
                                        @if($logoCert)
                                            
                                            <img src="{{ $logoCert }}" alt="{{ $cert->translated_name }}"
                                                 loading="lazy"
                                                 class="max-h-full max-w-full object-contain grayscale
                                                        transition-[filter] duration-300
                                                        group-hover:grayscale-0">
                                        @else
                                            
                                            <span class="text-center font-site-display text-site-small
                                                         font-bold leading-tight text-ink">
                                                {{ $cert->translated_name }}
                                            </span>
                                        @endif
                                    </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('products')
                @php
                    $produkBody = $isi('products', 'body', 'site.products_body');
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-7">
                                <p class="eyebrow">{{ $isi('products', 'eyebrow', 'site.home_section_products') }}</p>

                                <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                                    {!! \App\Support\Judul::sorot($isi('products', 'title', 'site.products_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:self-end">
                                @if($produkBody !== strip_tags($produkBody))
                                    <div class="rich max-w-[46ch]">{!! $produkBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $produkBody }}</p>
                                @endif

                                <a href="{{ route('products.index') }}"
                                   class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('products', 'cta', 'site.cta_explore_products') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        @if($featuredProducts->isNotEmpty())
                            <ul class="mt-12 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3">
                                @foreach($featuredProducts as $product)
                                    <li class="flex">
                                        <x-site.product-card :product="$product"
                                                     :label="$isi('products', 'view_label', 'site.view_details')"
                                                     class="w-full" />
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ $isi('products', 'empty', 'site.no_featured_products') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ── SECTION: EXPORT MARKETS ───────────────────────────────── --}}
            @case('export_markets')
                @php
                    $pasarBody = $isi('export-markets', 'body', 'site.markets_body');

                    $pasarJudul = str_replace(
                        ':count',
                        (string) $exportMarkets->count(),
                        $isi('export-markets', 'title', 'site.markets_title')
                    );
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="mx-auto max-w-[46rem] text-center">
                            <p class="eyebrow">{{ $isi('export-markets', 'eyebrow', 'site.home_section_export_markets') }}</p>
                            <h2 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                                {!! \App\Support\Judul::sorot($pasarJudul) !!}
                            </h2>

                            @if($pasarBody !== strip_tags($pasarBody))
                                <div class="rich mx-auto mt-5 max-w-[52ch]">{!! $pasarBody !!}</div>
                            @else
                                <p class="lede mx-auto mt-5 max-w-[52ch]">{{ $pasarBody }}</p>
                            @endif
                        </div>

                        @if($exportMarkets->isNotEmpty())
                            <div class="mt-12 lg:mt-14">
                                <x-site.export-map :markets="$exportMarkets" :show-list="false" />
                            </div>

                            <div class="mt-10 flex justify-center">
                                <a href="{{ route('export-markets.index') }}"
                                   class="group inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('export-markets', 'cta', 'site.cta_explore_markets') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ $isi('export-markets', 'empty', 'site.no_export_markets') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ── SECTION: WHY CHOOSE US ───────────────────────────────── --}}
            @case('about')
                @php
                    $pillars = collect(['quality', 'origin', 'standard', 'support'])
                        ->map(fn ($ikon, $i) => [
                            'icon'  => $ikon,
                            'title' => $isi('about', 'pillar_' . ($i + 1) . '_title', 'site.pillar_' . ($i + 1) . '_title'),
                            'body'  => $isi('about', 'pillar_' . ($i + 1) . '_body',  'site.pillar_' . ($i + 1) . '_body'),
                        ])
                        ->all();

                    $tentangBody = $isi('about', 'body', 'site.pillars_body');
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-6">
                                <p class="eyebrow">{{ $isi('about', 'eyebrow', 'site.pillars_eyebrow') }}</p>
                                <h2 class="display mt-5 max-w-[16ch] text-site-h2 text-site-forest">
                                    {!! \App\Support\Judul::sorot($isi('about', 'title', 'site.pillars_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                                @if($tentangBody !== strip_tags($tentangBody))
                                    <div class="rich max-w-[46ch]">{!! $tentangBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $tentangBody }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Empat Pilar --}}
                        <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                                   [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8
                                   lg:mx-0 lg:mt-14 lg:overflow-visible lg:px-0">
                            @foreach($pillars as $pillar)
                                <li class="group w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)]
                                           lg:w-auto lg:min-w-0 lg:shrink lg:basis-0 lg:grow
                                           lg:transition-[flex-grow] lg:duration-500 lg:ease-out
                                           lg:hover:grow-[2] lg:focus-within:grow-[2]">
                                    <div class="card relative flex h-full min-h-[256px] flex-col justify-end p-6
                                                transition-colors duration-300 hover:border-forest hover:bg-forest
                                                sm:min-h-[288px] lg:min-h-[332px]">

                                        <span aria-hidden="true" data-hias
                                              class="pointer-events-none absolute left-6 top-3 select-none
                                                     font-site-display text-[76px] font-bold leading-none tracking-[-0.04em]
                                                     text-ink/[0.07] transition-colors duration-300
                                                     group-hover:text-white/[0.13]">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                                        </span>

                                        <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center
                                                     rounded-full bg-site-forest text-site-gilt
                                                     transition-colors duration-300
                                                     group-hover:bg-site-gilt group-hover:text-site-forest">
                                            <x-icon.pillar :name="$pillar['icon']" />
                                        </span>

                                        <h3 class="relative mt-5 font-site-display font-bold leading-snug tracking-[-0.01em] text-site-forest transition-colors duration-300 group-hover:text-white text-site-title">
                                            {{ $pillar['title'] }}
                                        </h3>

                                        <div class="relative grid grid-rows-[1fr] transition-all duration-500 ease-out
                                                    lg:grid-rows-[0fr] lg:opacity-0
                                                    lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                                    lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                            <p class="overflow-hidden leading-relaxed text-ink-muted transition-colors duration-300 group-hover:text-white/70 text-site-body">
                                                <span class="mt-2.5 block">{{ $pillar['body'] }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
                @break

            {{-- ── SECTION: NEWS ─────────────────────────────────────────── --}}
            @case('news')
                @php
                    $beritaBody = $isi('news', 'body', 'site.news_body');
                    $beritaUtama = $latestNews->first();
                    $beritaSisa  = $latestNews->slice(1)->take(2);
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-7">
                                <p class="eyebrow">{{ $isi('news', 'eyebrow', 'site.news_eyebrow') }}</p>
                                <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                                    {!! \App\Support\Judul::sorot($isi('news', 'title', 'site.news_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:self-end">
                                @if($beritaBody !== strip_tags($beritaBody))
                                    <div class="rich max-w-[46ch]">{!! $beritaBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $beritaBody }}</p>
                                @endif

                                <a href="{{ route('news.index') }}"
                                   class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('news', 'cta', 'site.cta_see_more_news') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        @if($beritaUtama)
                            <div class="mt-10 grid items-stretch gap-x-8 gap-y-8 lg:mt-12 lg:grid-cols-12">

                                @php
                                    $sampulUtama = $beritaUtama->getFirstMediaUrl('covers', 'webp')
                                                ?: $beritaUtama->getFirstMediaUrl('covers', 'thumb');
                                @endphp
                                <article class="group flex flex-col lg:col-span-7">
                                    <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                       class="relative block min-h-[260px] flex-1 overflow-hidden
                                              rounded-panel bg-site-paper"
                                       tabindex="-1" aria-hidden="true">
                                        @if($sampulUtama)
                                            <img src="{{ $sampulUtama }}" alt="" loading="lazy"
                                                 class="absolute inset-0 h-full w-full object-cover
                                                        transition-transform duration-500 group-hover:scale-[1.03]">
                                        @else
                                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                                        @endif
                                    </a>

                                    <div class="flex flex-col pt-6">
                                        <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1.5">
                                            @if($beritaUtama->category)
                                                <p class="eyebrow">{{ $beritaUtama->category->name }}</p>
                                            @else
                                                <span></span>
                                            @endif

                                            @if($beritaUtama->published_at)
                                                <time datetime="{{ $beritaUtama->published_at->toDateString() }}"
                                                      class="shrink-0 text-ink-muted text-site-micro">
                                                    {{ $beritaUtama->published_at->translatedFormat('d M Y') }}
                                                </time>
                                            @endif
                                        </div>

                                        <h3 class="mt-5 min-h-[2.75em] font-site-display text-site-h3 font-bold
                                                   leading-snug tracking-[-0.015em] text-site-forest">
                                            <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                               class="transition-colors hover:text-site-gilt-deep">
                                                {{ $beritaUtama->translated_title }}
                                            </a>
                                        </h3>

                                        @if($beritaUtama->translated_excerpt)
                                            <p class="mt-5 line-clamp-5 min-h-[8.125em] leading-relaxed
                                                      text-ink-muted text-site-lede">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($beritaUtama->translated_excerpt), 380) }}
                                            </p>
                                        @endif

                                        <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                           class="mt-10 inline-flex w-max items-center gap-2
                                                  font-site-body text-site-small font-semibold
                                                  text-site-forest transition-colors hover:text-site-gilt-deep">
                                            {{ $isi('news', 'read_label', 'site.read_article') }}
                                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </article>

                                <div class="flex flex-col gap-8 lg:col-span-5">
                                    @foreach($beritaSisa as $article)
                                        @php
                                            $sampul = $article->getFirstMediaUrl('covers', 'webp')
                                                   ?: $article->getFirstMediaUrl('covers', 'thumb');
                                        @endphp

                                        <article class="group flex flex-col">
                                            <a href="{{ route('news.show', $article->slug) }}"
                                               class="relative block aspect-[5/2] w-full shrink-0 overflow-hidden
                                                      rounded-panel bg-site-paper"
                                               tabindex="-1" aria-hidden="true">
                                                @if($sampul)
                                                    <img src="{{ $sampul }}" alt="" loading="lazy"
                                                         class="absolute inset-0 h-full w-full object-cover
                                                                transition-transform duration-500 group-hover:scale-[1.03]">
                                                @else
                                                    <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-9 w-9" />
                                                @endif
                                            </a>

                                            <div class="flex flex-1 flex-col pt-4">
                                                <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1.5">
                                                    @if($article->category)
                                                        <p class="eyebrow">{{ $article->category->name }}</p>
                                                    @else
                                                        <span></span>
                                                    @endif

                                                    @if($article->published_at)
                                                        <time datetime="{{ $article->published_at->toDateString() }}"
                                                              class="shrink-0 text-ink-muted text-site-micro">
                                                            {{ $article->published_at->translatedFormat('d M Y') }}
                                                        </time>
                                                    @endif
                                                </div>

                                                <h3 class="mt-2.5 line-clamp-2 min-h-[2.75em] font-site-display
                                                           text-site-title font-bold leading-snug
                                                           tracking-[-0.01em] text-site-forest">
                                                    <a href="{{ route('news.show', $article->slug) }}"
                                                       class="transition-colors hover:text-site-gilt-deep">
                                                        {{ $article->translated_title }}
                                                    </a>
                                                </h3>

                                                @if($article->translated_excerpt)
                                                    <p class="mt-2 line-clamp-2 min-h-[3.25em] leading-relaxed
                                                              text-ink-muted text-site-small">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 120) }}
                                                    </p>
                                                @endif

                                                <a href="{{ route('news.show', $article->slug) }}"
                                                   class="mt-auto inline-flex w-max items-center gap-2 pt-4
                                                          font-site-body text-site-small font-semibold
                                                          text-site-forest transition-colors hover:text-site-gilt-deep">
                                                    {{ $isi('news', 'read_label', 'site.read_article') }}
                                                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ $isi('news', 'empty', 'site.no_news_found') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ── SECTION: CLOSING CTA BANNER ─────────────────────────── --}}
            @case('contact')
                @php
                    $whatsapp = $settings['whatsapp_number'] ?? '';
                    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;

                    $ctaAlamat = $gambarBagian['contact'] ?? ($settings['cta_image'] ?? null);
                    $ctaImage = !empty($ctaAlamat)
                        ? \Illuminate\Support\Facades\Storage::url($ctaAlamat)
                        : null;

                    $kontakBody = $isi('contact', 'body', 'site.cta_body');
                @endphp

                <section class="pb-16 pt-16 md:pb-20 md:pt-20 lg:pb-24 lg:pt-24">
                    <div class="shell">
                        <div class="relative isolate overflow-hidden rounded-panel bg-site-forest px-6 py-16 text-center sm:px-10 md:py-20 lg:py-24">

                            @if($ctaImage)
                                <img src="{{ $ctaImage }}" alt="" aria-hidden="true" loading="lazy"
                                     class="absolute inset-0 -z-10 h-full w-full object-cover">

                                <div class="absolute inset-0 -z-10 bg-site-forest/75" aria-hidden="true"></div>
                            @endif

                            <h2 class="display display-invert mx-auto max-w-[18ch] text-site-h2">
                                {!! \App\Support\Judul::sorot($isi('contact', 'title', 'site.cta_title')) !!}
                            </h2>

                            @if($kontakBody !== strip_tags($kontakBody))
                                <div class="rich rich-invert mx-auto mt-6 max-w-[56ch] [&_p]:text-white [&_ul]:text-white [&_ol]:text-white">{!! $kontakBody !!}</div>
                            @else
                                <p class="mx-auto mt-6 max-w-[56ch] leading-relaxed text-white text-site-body">
                                    {{ $kontakBody }}
                                </p>
                            @endif

                            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('inquiry.index') }}"
                                   class="inline-flex h-10 items-center rounded-full bg-site-gilt px-6
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          text-site-forest ring-1 ring-site-gilt-deep/70
                                          shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                                          transition-colors duration-300 hover:bg-site-gilt-soft">
                                    {{ $isi('contact', 'cta_primary', 'site.cta_request_quote') }}
                                </a>

                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex h-10 items-center gap-2.5 rounded-full px-5
                                              font-site-body text-site-small font-semibold whitespace-nowrap
                                              bg-site-shade/45 text-white ring-1 ring-white/25
                                              transition-colors duration-300 hover:bg-site-shade/65">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                        {{ $isi('contact', 'cta_whatsapp', 'site.cta_whatsapp') }}
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
