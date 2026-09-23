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

    <article class="pb-20 pt-6 md:pt-8 lg:pb-24 lg:pt-10">
        <div class="shell">

            {{-- Tautan kembali --}}
            <a href="{{ route('news.index') }}"
               class="inline-flex h-10 w-max items-center gap-2.5 rounded-full pl-3 pr-5
                      bg-site-paper text-site-forest ring-1 ring-line-strong
                      font-site-body text-site-small font-semibold whitespace-nowrap
                      transition-colors duration-300 hover:bg-site-line">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                             bg-site-canvas text-site-forest">
                    <svg class="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('site.page_news') }}
            </a>

            {{-- Lajur baca artikel --}}
            <div class="mx-auto mt-8 max-w-[46rem] lg:mt-10">
                @if($news->category)
                    <p class="eyebrow">{{ $news->category->name }}</p>
                @endif

                <h1 class="display mt-4 text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($news->translated_title) !!}
                </h1>

                {{-- Penulis & Tanggal Terbit --}}
                <div class="mt-6 flex flex-wrap items-center justify-between gap-x-5 gap-y-3
                            border-b border-line pb-6">
                    @if($news->author?->name)
                        <p class="flex items-center gap-2.5 text-ink-muted text-site-small">
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center
                                         rounded-full bg-site-paper font-site-accent text-site-micro
                                         font-semibold text-site-forest"
                                  aria-hidden="true">
                                {{ \App\Support\Monogram::inisial($news->author->name) ?: '·' }}
                            </span>
                            {{ $news->author->name }}
                        </p>
                    @else
                        <span></span>
                    @endif

                    @if($news->published_at)
                        <time datetime="{{ $news->published_at->toDateString() }}"
                              class="shrink-0 text-ink-muted text-site-small">
                            {{ $news->published_at->translatedFormat('d F Y') }}
                        </time>
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
                    {{-- Tag Artikel --}}
                    <div class="mt-10 flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-line pt-8">
                        <p class="eyebrow shrink-0">{{ __('site.tags') }}</p>

                        <ul class="flex flex-wrap gap-2">
                            @foreach($news->tags as $tag)
                                <li class="inline-flex h-9 items-center rounded-full border border-line px-4
                                           font-site-body text-site-small font-semibold text-ink-muted">
                                    {{ $tag->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="border-t border-line py-16 lg:py-20">
            <div class="shell">
                <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4">
                    <h2 class="display max-w-[20ch] text-site-h3 text-site-forest">
                        {!! \App\Support\Judul::sorot(__('site.related_articles')) !!}
                    </h2>

                    <a href="{{ route('news.index') }}"
                       class="group inline-flex shrink-0 items-center gap-2 font-site-body text-site-small
                              font-semibold text-site-forest transition-colors hover:text-site-gilt-deep">
                        {{ __('site.cta_see_more_news') }}
                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                             viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

                <ul class="mt-8 grid auto-rows-fr gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($related as $article)
                        @php
                            $relatedCover = $article->getFirstMediaUrl('covers', 'thumb')
                                         ?: $article->getFirstMediaUrl('covers', 'webp');
                        @endphp

                        <li class="flex">
                            <a href="{{ route('news.show', $article->slug) }}"
                               class="group flex h-full w-full flex-col">

                                <span class="relative block aspect-[5/3] w-full shrink-0 overflow-hidden
                                             rounded-panel bg-site-paper">
                                    @if($relatedCover)
                                        <img src="{{ $relatedCover }}" alt="" aria-hidden="true" loading="lazy"
                                             class="absolute inset-0 h-full w-full object-cover
                                                    transition-transform duration-500 group-hover:scale-[1.03]">
                                    @else
                                        <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-10 w-10" />
                                    @endif
                                </span>

                                <span class="flex flex-1 flex-col pt-5">
                                    <span class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1.5">
                                        @if($article->category)
                                            <span class="eyebrow">{{ $article->category->name }}</span>
                                        @else
                                            <span></span>
                                        @endif

                                        @if($article->published_at)
                                            <time datetime="{{ $article->published_at->toDateString() }}"
                                                  class="shrink-0 text-ink-muted text-site-micro">
                                                {{ $article->published_at->translatedFormat('d M Y') }}
                                            </time>
                                        @endif
                                    </span>

                                    <span class="mt-3 line-clamp-2 min-h-[2.75em] font-site-display
                                                 text-site-title font-bold leading-snug tracking-[-0.01em]
                                                 text-site-forest transition-colors group-hover:text-site-gilt-deep">
                                        {{ $article->translated_title }}
                                    </span>

                                    @if($article->translated_excerpt)
                                        <span class="mt-2.5 line-clamp-3 block min-h-[4.875em] leading-relaxed
                                                     text-ink-muted text-site-small">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 130) }}
                                        </span>
                                    @endif

                                    <span class="mt-auto inline-flex items-center gap-2 pt-5 font-site-body
                                                 text-site-small font-semibold text-site-forest
                                                 transition-colors group-hover:text-site-gilt-deep">
                                        {{ __('site.read_article') }}
                                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                             viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
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
