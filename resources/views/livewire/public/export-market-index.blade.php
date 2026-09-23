<div>
    
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            @php $pasarBody = $isi('body', 'site.page_export_markets_sub'); @endphp

            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.home_section_export_markets') }}</p>

                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_export_markets')) !!}
                </h1>

                @if($pasarBody !== strip_tags($pasarBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $pasarBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $pasarBody }}</p>
                @endif
            </div>
        </div>
    </section>

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
