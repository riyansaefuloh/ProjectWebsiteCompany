@props([
    'markets',

    'detailed' => false,

    'showList' => true,
])

@php
    // ── PROYEKSI ─────────────────────────────────────────────────────────
    $R = 6381372.0;
    $centralMeridian = 11.5;
    $bboxX0 = -20004297.151525836;
    $bboxY0 = -12671671.123330014;
    $mapW = 900.0;
    $mapH = 440.70631074413296;
    $scale = $mapW / (20026572.39474939 - $bboxX0);

    $coordinates = config('country-coordinates', []);

    $markers = [];
    foreach ($markets as $market) {
        $point = $coordinates[strtoupper($market->country_code)] ?? null;

        if (! $point) {
            continue;
        }

        [$lat, $lng] = $point;

        $lngDelta = $lng - $centralMeridian;
        while ($lngDelta < -180) { $lngDelta += 360; }
        while ($lngDelta > 180) { $lngDelta -= 360; }

        $x = $R * $lngDelta * (M_PI / 180);
        $y = -$R * log(tan((45 + 0.4 * $lat) * (M_PI / 180))) / 0.8;

        $markers[] = [
            'name'   => $market->translated_name,
            'region' => $market->region,
            'code'   => strtoupper($market->country_code),
            'left'   => round((($x - $bboxX0) * $scale) / $mapW * 100, 3),
            'top'    => round((($y - $bboxY0) * $scale) / $mapH * 100, 3),
        ];
    }

    $byRegion = $markets->groupBy('region');
@endphp

<div>
    {{-- ── PETA ────────────────────────────────────────────────────────── --}}
    <div class="relative aspect-[900/441] w-full {{ $showList ? 'hidden md:block' : '' }}">
        <img src="{{ asset('images/world-dotted.svg') }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 h-full w-full select-none">

        @foreach($markers as $marker)
            <div class="group absolute -translate-x-1/2 -translate-y-1/2 {{ $showList ? '' : 'hidden md:block' }}"
                 style="left: {{ $marker['left'] }}%; top: {{ $marker['top'] }}%;">

                <button type="button"
                        class="relative block h-3 w-3 rounded-full bg-brand ring-4 ring-brand/20
                               transition-transform duration-200 hover:scale-125 focus:outline-none
                               focus-visible:ring-brand/50 group-focus-within:scale-125">
                    <span class="sr-only">{{ $marker['name'] }} — {{ $marker['region'] }}</span>
                </button>

                <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-3 w-max max-w-[220px]
                            -translate-x-1/2 scale-95 rounded-control bg-forest px-4 py-3 text-center
                            opacity-0 shadow-[0_18px_40px_-18px_rgba(16,53,42,0.7)] transition
                            duration-200 group-hover:scale-100 group-hover:opacity-100
                            group-focus-within:scale-100 group-focus-within:opacity-100"
                     aria-hidden="true">
                    <span class="block font-display text-[14px] font-extrabold text-white">{{ $marker['name'] }}</span>
                    <span class="mt-1 block text-[12px] text-white/60">{{ $marker['region'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── DAFTAR NEGARA PER KAWASAN ─────────────────────────────────── --}}
    @if($showList)
    <div @class([
        'grid gap-x-8 gap-y-10 md:mt-14',
        'sm:grid-cols-2 lg:grid-cols-4' => ! $detailed,
        'lg:grid-cols-2' => $detailed,
    ])>
        @foreach($byRegion as $region => $countries)
            <div>
                <p class="eyebrow">{{ $region }}</p>

                @if($detailed)
                    <ul class="mt-4 divide-y divide-line border-t border-line">
                        @foreach($countries as $country)
                            <li class="py-3.5">
                                <div class="flex items-baseline justify-between gap-4">
                                    <span class="text-[14px] font-bold text-ink">{{ $country->translated_name }}</span>

                                    <span class="chip shrink-0 px-2.5 py-1 text-[10px]">{{ $country->country_code }}</span>
                                </div>

                                @if($country->translated_note)
                                    <p class="mt-1.5 max-w-[46ch] text-[13px] leading-relaxed text-ink-muted">
                                        {{ $country->translated_note }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <ul class="mt-4 space-y-2.5">
                        @foreach($countries as $country)
                            <li class="text-[14px] text-ink-muted">{{ $country->translated_name }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
    @endif
</div>
