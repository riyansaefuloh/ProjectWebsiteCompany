<div>
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <article class="pb-20 pt-12 md:pt-16 lg:pb-24">
        <div class="shell">
            <div class="mx-auto max-w-[46rem]">

                <a href="{{ route('home') }}" class="link-arrow">
                    <span class="rotate-180">
                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('site.nav_home') }}
                </a>

                <h1 class="display mt-8 text-[30px] leading-[1.25] sm:text-[36px] lg:text-[42px]">
                    {{ $page->translated_title }}
                </h1>

                @if($page->updated_at)
                    <p class="mt-5 border-b border-line pb-6 text-[13px] text-ink-muted">
                        {{ __('site.last_updated') }}
                        <time datetime="{{ $page->updated_at->toDateString() }}" class="font-bold text-ink">
                            {{ $page->updated_at->translatedFormat('d F Y') }}
                        </time>
                    </p>
                @endif

                <div class="rich mt-10">
                    {!! $page->translated_content !!}
                </div>

                @if($otherPages->isNotEmpty())
                    <div class="mt-14 border-t border-line pt-8">
                        <p class="eyebrow">{{ __('site.other_pages') }}</p>

                        <ul class="mt-5 space-y-3">
                            @foreach($otherPages as $other)
                                <li>
                                    <a href="{{ route('page.show', $other->slug) }}" class="link-arrow">
                                        {{ $other->translated_title }}
                                        <span>
                                            <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </article>
</div>
