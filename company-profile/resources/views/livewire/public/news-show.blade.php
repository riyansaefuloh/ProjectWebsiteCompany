@php
    $cover = $news->getFirstMediaUrl('covers', 'webp')
          ?: $news->getFirstMediaUrl('covers', 'thumb');
@endphp

<div>
    @push('seo')
        <script type="application/ld+json">
        {!! json_encode(\App\Services\JsonLdService::articleSchema($news), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endpush

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA ARTIKEL
         ══════════════════════════════════════════════════════════════════ --}}
    <article class="pb-20 pt-12 md:pt-16 lg:pb-24">
        <div class="shell">
            <div class="mx-auto max-w-[46rem]">

                <a href="{{ route('news.index') }}" class="link-arrow">
                    <span class="rotate-180">
                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('site.page_news') }}
                </a>

                @if($news->category)
                    <p class="eyebrow mt-8">{{ $news->category->name }}</p>
                @endif

                <h1 class="display mt-5 text-[28px] leading-[1.25] sm:text-[34px] lg:text-[40px]">
                    {{ $news->translated_title }}
                </h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-line pb-6 text-[13px] text-ink-muted">
                    @if($news->published_at)
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <rect x="2.2" y="3.4" width="11.6" height="10.4" rx="1.6" stroke="currentColor" stroke-width="1.3"/>
                                <path d="M2.2 6.6h11.6M5.6 2.2v2.4M10.4 2.2v2.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                            </svg>
                            <time datetime="{{ $news->published_at->toDateString() }}">
                                {{ $news->published_at->translatedFormat('d F Y') }}
                            </time>
                        </span>
                    @endif

                    @if($news->author?->name)
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="5.6" r="2.8" stroke="currentColor" stroke-width="1.3"/>
                                <path d="M2.8 13.6c0-2.4 2.3-4 5.2-4s5.2 1.6 5.2 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                            </svg>
                            {{ $news->author->name }}
                        </span>
                    @endif
                </div>

                @if($cover)
                    <img src="{{ $cover }}" alt="" aria-hidden="true" fetchpriority="high"
                         class="mt-8 aspect-[16/9] w-full rounded-panel object-cover">
                @endif

                <div class="rich mt-10">
                    {!! $news->translated_content !!}
                </div>

                @if($news->tags->isNotEmpty())
                    <ul class="mt-10 flex flex-wrap gap-2 border-t border-line pt-8">
                        @foreach($news->tags as $tag)
                            <li class="chip">{{ $tag->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </article>

    {{-- ══════════════════════════════════════════════════════════════════
         ARTIKEL LAINNYA
         ══════════════════════════════════════════════════════════════════ --}}
    @if($related->isNotEmpty())
        <section class="section border-t border-line">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="display max-w-[20ch] text-[24px] sm:text-[28px] lg:text-[32px]">
                        {{ __('site.related_articles') }}
                    </h2>

                    <a href="{{ route('news.index') }}" class="btn btn-outline btn-arrow">
                        {{ __('site.cta_see_more_news') }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

                <ul class="mt-10 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($related as $article)
                        @php
                            $relatedCover = $article->getFirstMediaUrl('covers', 'thumb')
                                         ?: $article->getFirstMediaUrl('covers', 'webp');
                        @endphp

                        <li class="flex">
                            <a href="{{ route('news.show', $article->slug) }}"
                               class="card group flex h-full w-full flex-col transition-colors hover:border-line-strong">

                                <span class="relative block h-[170px] shrink-0 overflow-hidden bg-mist-deep">
                                    @if($relatedCover)
                                        <img src="{{ $relatedCover }}" alt="" aria-hidden="true" loading="lazy"
                                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
                                    @else
                                        <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-10 w-10" />
                                    @endif
                                </span>

                                <span class="flex flex-1 flex-col p-6">
                                    @if($article->published_at)
                                        <time datetime="{{ $article->published_at->toDateString() }}"
                                              class="text-[12px] text-ink-faint">
                                            {{ $article->published_at->translatedFormat('d M Y') }}
                                        </time>
                                    @endif

                                    <span class="mt-2.5 font-display text-[16px] font-extrabold leading-snug tracking-[-0.01em] text-ink transition-colors group-hover:text-brand">
                                        {{ $article->translated_title }}
                                    </span>

                                    <span class="link-arrow mt-auto pt-6">
                                        {{ __('site.read_article') }}
                                        <span>
                                            <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</div>
