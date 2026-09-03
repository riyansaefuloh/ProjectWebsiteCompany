<div>
    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            <div class="max-w-[46rem]">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.certifications') }}</p>
                <h1 class="display mt-5 max-w-[18ch] text-site-h1">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_certifications')) !!}
                </h1>
                <p class="lede mt-6 max-w-[52ch]">{{ $isi('body', 'site.page_certifications_sub') }}</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         KISI SERTIFIKAT
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($certifications->isNotEmpty())
                <ul class="grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($certifications as $cert)
                        @php
                            $logo = $cert->getFirstMediaUrl('logos', 'thumb')
                                 ?: $cert->getFirstMediaUrl('logos');
                            $pdf  = $cert->getFirstMediaUrl('pdfs');

                            $isExpired = $cert->expires_at && $cert->expires_at->isPast();
                        @endphp

                        <li class="card flex h-full flex-col p-7">

                            {{-- ── Logo, atau monogram sebagai gantinya ────────── --}}
                            <div class="flex h-14 items-center">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="" aria-hidden="true" loading="lazy"
                                         class="h-14 w-auto max-w-[130px] object-contain object-left">
                                @else
                                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-corner bg-brand/10 font-display font-semibold tracking-[-0.02em] text-brand text-site-lede"
                                          aria-hidden="true">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($cert->translated_name, 0, 2)) }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="mt-6 font-display font-semibold leading-snug tracking-[-0.01em] text-ink text-site-lede">
                                {{ $cert->translated_name }}
                            </h2>

                            @if($cert->issuer)
                                <p class="mt-2 text-ink-faint text-site-small">
                                    {{ __('site.issued_by') }} {{ $cert->issuer }}
                                </p>
                            @endif

                            @if($cert->translated_description)
                                <p class="mt-4 leading-relaxed text-ink-muted text-site-small">
                                    {{ $cert->translated_description }}
                                </p>
                            @endif

                            <dl class="mt-auto space-y-2 border-t border-line pt-5 leading-relaxed text-site-micro">
                                @if($cert->certificate_number)
                                    <div class="flex items-baseline justify-between gap-4">
                                        <dt class="shrink-0 text-ink-faint">{{ __('site.certificate_number') }}</dt>
                                        <dd class="text-right font-semibold text-ink">{{ $cert->certificate_number }}</dd>
                                    </div>
                                @endif

                                @if($cert->expires_at)
                                    <div class="flex items-baseline justify-between gap-4">
                                        <dt class="shrink-0 text-ink-faint">
                                            {{ $isExpired ? __('site.expired_on') : __('site.valid_until') }}
                                        </dt>
                                        <dd @class([
                                            'text-right font-semibold',
                                            'text-danger' => $isExpired,
                                            'text-ink' => ! $isExpired,
                                        ])>
                                            <time datetime="{{ $cert->expires_at->toDateString() }}">
                                                {{ $cert->expires_at->translatedFormat('d M Y') }}
                                            </time>
                                        </dd>
                                    </div>
                                @endif
                            </dl>

                            @if($pdf)
                                <a href="{{ $pdf }}" target="_blank" rel="noopener noreferrer"
                                   class="link-arrow mt-5">
                                    {{ __('site.download_certificate') }}
                                    <span>
                                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 3v8m0 0L4.8 7.8M8 11l3.2-3.2M3 13h10" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_certifications') }}
                </p>
            @endif
        </div>
    </section>
</div>
