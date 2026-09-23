<div>
    @php
        $pakaiDaftar = count($daftarIsi) > 1;
        $labelHalaman = trim((string) $page->translated_label);
    @endphp

    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">

            <div class="mx-auto max-w-[44rem] text-center">
                @if($labelHalaman !== '')
                    <p class="eyebrow">{{ $labelHalaman }}</p>
                @endif

                <h1 @class([
                        'display mx-auto max-w-[20ch] text-site-h2 text-site-forest',
                        'mt-5' => $labelHalaman !== '',
                    ])>
                    {!! \App\Support\Judul::sorot($page->translated_title) !!}
                </h1>

                {{-- Tanggal Terakhir Diperbarui --}}
                @if($page->updated_at)
                    <p class="mt-7 inline-flex items-center gap-2.5 rounded-full border border-line
                              bg-site-paper px-4 py-2 text-ink-muted text-site-small">
                        <svg class="h-3.5 w-3.5 shrink-0 text-site-gilt-deep" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6.1" stroke="currentColor" stroke-width="1.3"/>
                            <path d="M8 4.6V8l2.3 1.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('site.last_updated') }}
                        <time datetime="{{ $page->updated_at->toDateString() }}" class="font-semibold text-site-forest">
                            {{ $page->updated_at->translatedFormat('d F Y') }}
                        </time>
                    </p>
                @endif
            </div>
        </div>
    </section>

    <section class="pb-20 lg:pb-24">
        <div class="shell">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">

                {{-- ── Daftar isi ───────────────────────────────────────── --}}
                @if($pakaiDaftar)
                    <nav class="lg:col-span-3" aria-label="{{ __('site.on_this_page') }}">
                        <div class="lg:sticky lg:top-28">
                            <p class="eyebrow">{{ __('site.on_this_page') }}</p>

                            <ul class="mt-4 space-y-3 border-l border-line pl-5">
                                @foreach($daftarIsi as $butir)
                                    <li>
                                        <a href="#{{ $butir['id'] }}"
                                           class="block leading-snug text-ink-muted text-site-small
                                                  transition-colors hover:text-site-gilt-deep">
                                            {{ $butir['teks'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                @endif

                <div class="{{ $pakaiDaftar ? 'lg:col-span-9' : 'mx-auto w-full max-w-[48rem] lg:col-span-12' }}">

                    {{-- Isi Dokumen --}}
                    <div class="rich rounded-panel border border-line bg-site-paper p-7 sm:p-9 lg:p-11
                                [&_h2]:scroll-mt-28 [&_h2]:font-site-display [&_h2]:font-bold
                                [&_h2]:tracking-[-0.01em] [&_h2]:text-site-forest
                                [&_h2:first-child]:mt-0
                                [&_h3]:font-site-display [&_h3]:font-bold [&_h3]:text-site-forest">
                        {!! $isi !!}
                    </div>

                    {{-- ── Halaman lain ─────────────────────────────────── --}}
                    @if($otherPages->isNotEmpty())
                        <div class="mt-12 border-t border-line pt-9">
                            <p class="eyebrow">{{ __('site.other_pages') }}</p>

                            <ul class="mt-5 grid gap-4 sm:grid-cols-2">
                                @foreach($otherPages as $other)
                                    <li>
                                        <a href="{{ route('page.show', $other->slug) }}"
                                           class="group flex h-full items-center justify-between gap-4
                                                  rounded-corner border border-line bg-site-paper px-5 py-4
                                                  transition-colors duration-300 hover:border-line-strong">
                                            <span class="font-site-display text-site-title font-bold leading-snug text-site-forest">
                                                {{ $other->translated_title }}
                                            </span>

                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                                         bg-site-forest text-site-gilt transition-transform duration-200
                                                         group-hover:translate-x-0.5">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
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
        </div>
    </section>
</div>
