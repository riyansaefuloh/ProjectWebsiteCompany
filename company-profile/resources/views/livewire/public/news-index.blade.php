<div>
    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            <div class="max-w-[46rem]">
                <p class="eyebrow">{{ __('site.news_eyebrow') }}</p>
                <h1 class="display mt-5 max-w-[16ch] text-[32px] sm:text-[38px] lg:text-[46px]">
                    {{ __('site.page_news') }}
                </h1>
                <p class="lede mt-6 max-w-[52ch]">{{ __('site.page_news_sub') }}</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($featured)
        @php
            $featuredCover = $featured->getFirstMediaUrl('covers', 'webp')
                          ?: $featured->getFirstMediaUrl('covers', 'thumb');
        @endphp

        <section class="pb-12 lg:pb-16">
            <div class="shell">
                <article class="card grid overflow-hidden lg:grid-cols-2">

                    <a href="{{ route('news.show', $featured->slug) }}"
                       class="relative block h-[240px] bg-mist-deep sm:h-[320px] lg:h-auto lg:min-h-[380px]"
                       tabindex="-1" aria-hidden="true">
                        @if($featuredCover)
                            <img src="{{ $featuredCover }}" alt="" fetchpriority="high"
                                 class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                        @endif
                    </a>

                    <div class="flex flex-col justify-center p-7 sm:p-10">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 rounded-full bg-brand/10 px-3.5 py-1.5
                                         text-[11px] font-bold uppercase tracking-[0.1em] text-brand">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand" aria-hidden="true"></span>
                                {{ __('site.featured_article') }}
                            </span>

                            @if($featured->category)
                                <span class="chip">{{ $featured->category->name }}</span>
                            @endif
                        </div>

                        @if($featured->published_at)
                            <p class="mt-5 flex items-center gap-2 text-[13px] text-ink-faint">
                                <svg class="h-3.5 w-3.5 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <rect x="2.2" y="3.4" width="11.6" height="10.4" rx="1.6" stroke="currentColor" stroke-width="1.3"/>
                                    <path d="M2.2 6.6h11.6M5.6 2.2v2.4M10.4 2.2v2.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                </svg>
                                <time datetime="{{ $featured->published_at->toDateString() }}">
                                    {{ $featured->published_at->translatedFormat('d F Y') }}
                                </time>
                            </p>
                        @endif

                        <h2 class="mt-4 font-display text-[22px] font-extrabold leading-snug tracking-[-0.02em] text-ink sm:text-[26px]">
                            <a href="{{ route('news.show', $featured->slug) }}" class="transition-colors hover:text-brand">
                                {{ $featured->translated_title }}
                            </a>
                        </h2>

                        @if($featured->translated_excerpt)
                            <p class="mt-4 max-w-[52ch] text-[14px] leading-relaxed text-ink-muted">
                                {{ \Illuminate\Support\Str::limit(strip_tags($featured->translated_excerpt), 200) }}
                            </p>
                        @endif

                        <a href="{{ route('news.show', $featured->slug) }}" class="link-arrow mt-7">
                            {{ __('site.read_article') }}
                            <span>
                                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section class="sticky top-[76px] z-30 border-y border-line bg-canvas/95 py-4 backdrop-blur">
        <div class="shell">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <label for="news-search" class="sr-only">{{ __('site.page_news') }}</label>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="7.2" cy="7.2" r="4.8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="m10.8 10.8 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>

                    <input id="news-search" type="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="{{ __('site.search_news_placeholder') }}"
                           class="field mt-0 rounded-full pl-11 pr-4">
                </div>

                <div class="shrink-0">
                    <label for="news-sort" class="sr-only">{{ __('site.sort_by') }}</label>
                    <select id="news-sort" wire:model.live="sort"
                            class="field field-select mt-0 w-full rounded-full sm:w-[13rem]">
                        <option value="newest">{{ __('site.sort_newest') }}</option>
                        <option value="oldest">{{ __('site.sort_oldest') }}</option>
                        <option value="title_asc">{{ __('site.sort_title_asc') }}</option>
                        <option value="title_desc">{{ __('site.sort_title_desc') }}</option>
                    </select>
                </div>
            </div>

            @if($categories->isNotEmpty())
                <fieldset class="mt-3 flex flex-wrap items-center gap-2">
                    <legend class="sr-only">{{ __('site.category') }}</legend>

                    <button type="button" wire:click="selectCategory('')"
                            aria-pressed="{{ $category === '' ? 'true' : 'false' }}"
                            @class([
                                'inline-flex items-center gap-1.5 rounded-full border px-4 py-2 text-[13px] font-bold transition-colors',
                                'border-brand bg-brand text-white' => $category === '',
                                'border-line text-ink-muted hover:border-line-strong hover:text-ink' => $category !== '',
                            ])>
                        {{ __('site.all_categories') }}
                    </button>

                    @foreach($categories as $cat)
                        @php $isActive = $category === $cat->slug; @endphp

                        <button type="button" wire:click="selectCategory('{{ $cat->slug }}')"
                                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                @class([
                                    'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-[13px] font-bold transition-colors',
                                    'border-brand bg-brand text-white' => $isActive,
                                    'border-line text-ink-muted hover:border-line-strong hover:text-ink' => ! $isActive,
                                ])>
                            {{ $cat->name }}
                            <span @class([
                                'text-[12px] font-semibold',
                                'text-white/60' => $isActive,
                                'text-ink-faint' => ! $isActive,
                            ])>{{ $cat->news_count }}</span>
                        </button>
                    @endforeach
                </fieldset>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         DAFTAR ARTIKEL
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 pt-8 lg:pb-24">
        <div class="shell">

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-[14px] text-ink-muted" aria-live="polite">
                    {{ trans_choice('site.news_count', $news->total(), ['count' => $news->total()]) }}
                </p>

                @if($hasFilters)
                    <button type="button" wire:click="resetFilters"
                            class="inline-flex items-center gap-2 text-[13px] font-bold text-brand transition-colors hover:text-brand-deep">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        {{ __('site.reset_filters') }}
                    </button>
                @endif
            </div>

            <div wire:loading.class="opacity-40" class="transition-opacity duration-200">
                @if($news->isNotEmpty())
                    <ul class="mt-8 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($news as $article)
                            @php
                                $cover = $article->getFirstMediaUrl('covers', 'thumb')
                                      ?: $article->getFirstMediaUrl('covers', 'webp');
                            @endphp

                            <li class="flex" wire:key="news-{{ $article->id }}">
                                <article class="card flex h-full w-full flex-col">
                                    <a href="{{ route('news.show', $article->slug) }}"
                                       class="relative block h-[190px] shrink-0 bg-mist-deep"
                                       tabindex="-1" aria-hidden="true">
                                        @if($cover)
                                            <img src="{{ $cover }}" alt="" loading="lazy"
                                                 class="absolute inset-0 h-full w-full object-cover">
                                        @else
                                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                                        @endif
                                    </a>

                                    <div class="flex flex-1 flex-col p-6">
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                            @if($article->category)
                                                <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-brand">
                                                    {{ $article->category->name }}
                                                </span>
                                            @endif

                                            @if($article->published_at)
                                                <time datetime="{{ $article->published_at->toDateString() }}"
                                                      class="text-[12px] text-ink-faint">
                                                    {{ $article->published_at->translatedFormat('d M Y') }}
                                                </time>
                                            @endif
                                        </div>

                                        <h2 class="mt-3 font-display text-[16px] font-extrabold leading-snug tracking-[-0.01em] text-ink">
                                            <a href="{{ route('news.show', $article->slug) }}" class="transition-colors hover:text-brand">
                                                {{ $article->translated_title }}
                                            </a>
                                        </h2>

                                        @if($article->translated_excerpt)
                                            <p class="mt-2.5 line-clamp-3 text-[13px] leading-relaxed text-ink-muted">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 130) }}
                                            </p>
                                        @endif

                                        <a href="{{ route('news.show', $article->slug) }}" class="link-arrow mt-auto pt-6">
                                            {{ __('site.read_article') }}
                                            <span>
                                                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
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
                    <div class="mt-8 rounded-corner border border-dashed border-line px-6 py-20 text-center">
                        <p class="font-display text-[18px] font-extrabold text-ink">
                            {{ __('site.no_news_found') }}
                        </p>

                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters" class="btn btn-outline mt-6">
                                {{ __('site.reset_filters') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
