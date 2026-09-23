<div>
    
    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            @php $beritaBody = $isi('body', 'site.page_news_sub'); @endphp

            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.news_eyebrow') }}</p>

                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_news')) !!}
                </h1>

                @if($beritaBody !== strip_tags($beritaBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $beritaBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $beritaBody }}</p>
                @endif
            </div>
        </div>
    </section>

    @if($featured)
        <section class="pb-6">
            <div class="shell">
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.latest_news')) !!}
                </h2>
            </div>
        </section>
    @endif

    @if($featured)
        @php
            $featuredCover = $featured->getFirstMediaUrl('covers', 'webp')
                          ?: $featured->getFirstMediaUrl('covers', 'thumb');
        @endphp

        <section class="pb-14 lg:pb-16">
            <div class="shell">
                <article class="group grid gap-8 lg:grid-cols-2 lg:gap-12">

                    <a href="{{ route('news.show', $featured->slug) }}"
                       class="relative block aspect-[4/3] w-full overflow-hidden rounded-panel
                              bg-site-paper lg:aspect-auto lg:h-full lg:min-h-[340px]"
                       tabindex="-1" aria-hidden="true">
                        @if($featuredCover)
                            <img src="{{ $featuredCover }}" alt="" fetchpriority="high"
                                 class="absolute inset-0 h-full w-full object-cover
                                        transition-transform duration-500 group-hover:scale-[1.03]">
                        @else
                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                        @endif
                    </a>

                    <div class="flex flex-col justify-center">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
                            @if($featured->category)
                                <p class="eyebrow">{{ $featured->category->name }}</p>
                            @else
                                <span></span>
                            @endif

                            @if($featured->published_at)
                                <time datetime="{{ $featured->published_at->toDateString() }}"
                                      class="shrink-0 text-ink-muted text-site-small">
                                    {{ $featured->published_at->translatedFormat('d F Y') }}
                                </time>
                            @endif
                        </div>

                        <h3 class="mt-3 font-site-display text-site-h3 font-bold leading-snug
                                   tracking-[-0.02em] text-site-forest">
                            <a href="{{ route('news.show', $featured->slug) }}"
                               class="transition-colors hover:text-site-gilt-deep">
                                {{ $featured->translated_title }}
                            </a>
                        </h3>

                        @if($featured->translated_excerpt)
                            <p class="mt-4 max-w-[52ch] leading-relaxed text-ink-muted text-site-body">
                                {{ \Illuminate\Support\Str::limit(strip_tags($featured->translated_excerpt), 210) }}
                            </p>
                        @endif

                        <a href="{{ route('news.show', $featured->slug) }}"
                           class="mt-7 inline-flex w-max items-center gap-2 font-site-body text-site-small
                                  font-semibold text-site-forest transition-colors hover:text-site-gilt-deep">
                            {{ $isi('read_label', 'site.read_article') }}
                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </article>
            </div>
        </section>
    @endif

    <section class="pb-10">
        <div class="shell">

            <div class="pt-2 lg:pt-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between lg:gap-6">

                    @if($categories->isNotEmpty())
                        {{-- Daftar Kategori --}}
                        <fieldset class="-mx-6 min-w-0 overflow-x-auto px-6 sm:-mx-8 sm:px-8
                                         lg:mx-0 lg:overflow-visible lg:px-0
                                         [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            <legend class="sr-only">{{ __('site.category') }}</legend>

                            <div class="flex w-max items-center gap-2 lg:w-auto lg:flex-wrap">
                                @php
                                    $pilDiam  = 'border-line text-ink-muted hover:border-line-strong hover:text-ink';
                                    $pilAktif = 'border-site-forest bg-site-forest text-white';
                                @endphp

                                <button type="button" wire:click="selectCategory('')"
                                        aria-pressed="{{ $category === '' ? 'true' : 'false' }}"
                                        @class([
                                            'inline-flex h-9 shrink-0 items-center whitespace-nowrap rounded-full border px-4',
                                            'font-site-body text-site-small font-semibold transition-colors',
                                            $pilAktif => $category === '',
                                            $pilDiam  => $category !== '',
                                        ])>
                                    {{ __('site.all_categories') }}
                                </button>

                                @foreach($categories as $cat)
                                    @php $isActive = $category === $cat->slug; @endphp

                                    <button type="button" wire:click="selectCategory('{{ $cat->slug }}')"
                                            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                            @class([
                                                'inline-flex h-9 shrink-0 items-center whitespace-nowrap rounded-full border px-4',
                                                'font-site-body text-site-small font-semibold transition-colors',
                                                $pilAktif => $isActive,
                                                $pilDiam  => ! $isActive,
                                            ])>
                                        {{ $cat->name }}

                                        <span @class([
                                            'ml-1.5 text-site-micro font-semibold tabular-nums',
                                            'text-white/60' => $isActive,
                                            'text-ink-faint' => ! $isActive,
                                        ])>{{ $cat->news_count }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </fieldset>
                    @else
                        <span></span>
                    @endif

                    <div class="flex shrink-0 items-center gap-2">
                        {{-- Form Cari Berita --}}
                        <div class="relative flex-1 lg:flex-none">
                            <label for="news-search" class="sr-only">{{ __('site.search_news') }}</label>

                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="7.2" cy="7.2" r="4.8" stroke="currentColor" stroke-width="1.5"/>
                                <path d="m10.8 10.8 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>

                            <input id="news-search" type="search"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="{{ __('site.search_news_placeholder') }}"
                                   class="h-9 w-full rounded-full border border-line bg-transparent pl-10 pr-4
                                          font-site-body text-site-small text-ink placeholder:text-ink-faint
                                          transition-colors focus:border-site-forest focus:outline-none
                                          lg:w-[15rem]">
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="pb-20 lg:pb-24">
        <div class="shell">
            {{-- Baris Keterangan & Filter --}}
            <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
                <p class="font-site-accent text-site-micro font-medium uppercase
                          tracking-[0.14em] text-ink-muted"
                   aria-live="polite">
                    {{ trans_choice('site.news_count', $news->total(), ['count' => $news->total()]) }}
                </p>

                @if($hasFilters)
                    <div class="flex flex-wrap items-center gap-2">
                        @if(filled($search))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-site-paper py-1 pl-3 pr-1.5
                                         text-site-small text-ink-muted">
                                {{ __('site.filter_search') }}:
                                <span class="font-semibold text-site-forest">{{ $search }}</span>

                                <button type="button" wire:click="$set('search', '')"
                                        aria-label="{{ __('site.filter_search') }}: {{ $search }} — {{ __('site.reset_filters') }}"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                               text-ink-faint transition-colors hover:bg-site-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if(filled($category))
                            @php $namaKategori = $categories->firstWhere('slug', $category)?->name ?? $category; @endphp

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-site-paper py-1 pl-3 pr-1.5
                                         text-site-small text-ink-muted">
                                {{ __('site.filter_category') }}:
                                <span class="font-semibold text-site-forest">{{ $namaKategori }}</span>

                                <button type="button" wire:click="selectCategory('')"
                                        aria-label="{{ __('site.filter_category') }}: {{ $namaKategori }} — {{ __('site.reset_filters') }}"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                               text-ink-faint transition-colors hover:bg-site-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        <button type="button" wire:click="resetFilters"
                                class="font-site-body text-site-small font-semibold text-site-forest
                                       underline decoration-line-strong underline-offset-4
                                       transition-colors hover:decoration-site-forest">
                            {{ __('site.clear_all_filters') }}
                        </button>
                    </div>
                @endif
            </div>

            <div wire:loading.class="opacity-40" class="mt-6 transition-opacity duration-200">
                @if($news->isNotEmpty())
                    <ul class="grid auto-rows-fr gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($news as $article)
                            @php
                                $cover = $article->getFirstMediaUrl('covers', 'thumb')
                                      ?: $article->getFirstMediaUrl('covers', 'webp');
                            @endphp

                            <li class="flex" wire:key="news-{{ $article->id }}">
                                <article class="group flex h-full w-full flex-col">
                                    <a href="{{ route('news.show', $article->slug) }}"
                                       class="relative block aspect-[5/3] w-full shrink-0 overflow-hidden
                                              rounded-panel bg-site-paper"
                                       tabindex="-1" aria-hidden="true">
                                        @if($cover)
                                            <img src="{{ $cover }}" alt="" loading="lazy"
                                                 class="absolute inset-0 h-full w-full object-cover
                                                        transition-transform duration-500 group-hover:scale-[1.03]">
                                        @else
                                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                                        @endif
                                    </a>

                                    <div class="flex flex-1 flex-col pt-5">
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

                                        <h3 class="mt-3 line-clamp-2 min-h-[2.75em] font-site-display
                                                   text-site-title font-bold leading-snug tracking-[-0.01em]
                                                   text-site-forest">
                                            <a href="{{ route('news.show', $article->slug) }}"
                                               class="transition-colors hover:text-site-gilt-deep">
                                                {{ $article->translated_title }}
                                            </a>
                                        </h3>

                                        @if($article->translated_excerpt)
                                            <p class="mt-2.5 line-clamp-3 min-h-[4.875em] leading-relaxed
                                                      text-ink-muted text-site-small">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 130) }}
                                            </p>
                                        @endif

                                        <a href="{{ route('news.show', $article->slug) }}"
                                           class="mt-auto inline-flex w-max items-center gap-2 pt-5
                                                  font-site-body text-site-small font-semibold
                                                  text-site-forest transition-colors hover:text-site-gilt-deep">
                                            {{ $isi('read_label', 'site.read_article') }}
                                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </article>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-12">
                        {{ $news->links('vendor.pagination.site') }}
                    </div>
                @else
                    <div class="rounded-corner border border-dashed border-line px-6 py-20 text-center">
                        <p class="font-site-display text-site-title font-bold text-site-forest">
                            {{ $isi('empty', 'site.no_news_found') }}
                        </p>

                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters"
                                    class="mt-6 inline-flex h-10 items-center rounded-full bg-site-forest px-6
                                           font-site-body text-site-small font-semibold text-white
                                           transition-colors duration-300 hover:bg-site-brand-deep">
                                {{ __('site.reset_filters') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

                    <div class="mt-12">
                        {{ $news->links('vendor.pagination.site') }}
                    </div>
                @else
                    <div class="rounded-corner border border-dashed border-line px-6 py-20 text-center">
                        <p class="font-site-display text-site-title font-bold text-site-forest">
                            {{ $isi('empty', 'site.no_news_found') }}
                        </p>

                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters"
                                    class="mt-6 inline-flex h-10 items-center rounded-full bg-site-forest px-6
                                           font-site-body text-site-small font-semibold text-white
                                           transition-colors duration-300 hover:bg-site-brand-deep">
                                {{ __('site.reset_filters') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
