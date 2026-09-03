<div>
    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            <div class="max-w-[46rem]">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.home_section_export_markets') }}</p>
                <h1 class="display mt-5 max-w-[18ch] text-site-h1">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_export_markets')) !!}
                </h1>
                <p class="lede mt-6 max-w-[52ch]">{{ $isi('body', 'site.page_export_markets_sub') }}</p>
            </div>

            {{-- ── Statistik ──────────────────────────────────────────────── --}}
            @if(!empty($stats))
                <dl class="mt-12 grid gap-px overflow-hidden rounded-panel border border-line bg-line sm:grid-cols-3 lg:mt-14">
                    @foreach($stats as $stat)
                        <div class="flex flex-col bg-mist px-6 py-8 sm:px-8 sm:py-10">
                            <dt class="order-2 mt-3 font-semibold uppercase tracking-[0.1em] text-ink-muted text-site-micro">
                                {{ $stat['label'] }}
                            </dt>

                            <dd class="order-1 whitespace-nowrap font-display font-semibold leading-none tracking-[-0.03em] text-brand text-site-metric-sm">
                                {{ $stat['value'] }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         PETA & DAFTAR NEGARA
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($exportMarkets->isNotEmpty())
                <x-site.export-map :markets="$exportMarkets" :detailed="true" />
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_export_markets') }}
                </p>
            @endif
        </div>
    </section>
</div>
