<div>
    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            <div class="max-w-[46rem]">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.nav_downloads') }}</p>
                <h1 class="display mt-5 max-w-[16ch] text-[32px] sm:text-[38px] lg:text-[46px]">
                    {{ $isi('title', 'site.page_downloads') }}
                </h1>
                <p class="lede mt-6 max-w-[52ch]">{{ $isi('body', 'site.page_downloads_sub') }}</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         KISI BERKAS
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($downloads->isNotEmpty())
                <ul class="grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($downloads as $download)
                        <li class="flex" wire:key="download-{{ $download->id }}">
                            <div class="card flex h-full w-full flex-col p-7">

                                <div class="flex items-start justify-between gap-4">
                                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-corner bg-brand/10 text-brand"
                                          aria-hidden="true">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                            <path d="M13.4 2.8H6.8a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.6l-5.8-5.8Z"
                                                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                            <path d="M13.4 2.8v5.8h5.8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        </svg>
                                    </span>

                                    @if($download->require_email)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand/10 px-3 py-1.5
                                                     text-[11px] font-bold text-brand">
                                            <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <rect x="3.2" y="7" width="9.6" height="6.8" rx="1.6" stroke="currentColor" stroke-width="1.4"/>
                                                <path d="M5.6 7V5.2a2.4 2.4 0 0 1 4.8 0V7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            </svg>
                                            {{ __('site.enter_email') }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="mt-6 font-display text-[16px] font-extrabold leading-snug tracking-[-0.01em] text-ink">
                                    {{ $download->title }}
                                </h2>

                                <p class="mt-2 flex flex-wrap items-center gap-x-2.5 text-[12px] text-ink-faint">
                                    <span class="uppercase tracking-[0.08em]">PDF</span>
                                    <span aria-hidden="true">·</span>
                                    <span>{{ __('site.downloads_count', ['count' => number_format($download->download_count)]) }}</span>
                                </p>

                                {{-- ── Kolom email, hanya untuk berkas yang sedang diminta ── --}}
                                @if($selectedDownloadId === $download->id)
                                    <div class="mt-6 rounded-corner border border-line bg-mist p-4">
                                        <label for="dl-email-{{ $download->id }}" class="field-label">
                                            {{ __('site.field_email') }} *
                                        </label>
                                        <p class="mt-1.5 text-[12px] leading-relaxed text-ink-muted">
                                            {{ $isi('gated_note', 'site.download_gated_note') }}
                                        </p>

                                        <input id="dl-email-{{ $download->id }}" type="email"
                                               wire:model="email"
                                               wire:keydown.enter="download('{{ $download->id }}')"
                                               placeholder="{{ __('site.field_email_ph') }}"
                                               class="field @error('email') border-danger @enderror">

                                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <div class="mt-auto pt-6">
                                    <button type="button" wire:click="download('{{ $download->id }}')"
                                            wire:loading.attr="disabled" wire:target="download('{{ $download->id }}')"
                                            class="btn btn-outline btn-arrow w-full">
                                        {{ __('site.download_pdf_btn') }}
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 3v8m0 0L4.8 7.8M8 11l3.2-3.2M3 13h10" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_downloads') }}
                </p>
            @endif
        </div>
    </section>
</div>
