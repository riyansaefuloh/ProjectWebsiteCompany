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
        $kode = strtoupper($market->country_code);
        $point = $coordinates[$kode] ?? null;

        if (! $point) {
            continue;
        }

        [$lat, $lng] = $point;

        $lngDelta = $lng - $centralMeridian;
        while ($lngDelta < -180) { $lngDelta += 360; }
        while ($lngDelta > 180) { $lngDelta -= 360; }

        $x = $R * $lngDelta * (M_PI / 180);
        $y = -$R * log(tan((45 + 0.4 * $lat) * (M_PI / 180))) / 0.8;

        $titikKartu = config('country-callouts.' . $kode);

        $markers[] = [
            'name'   => $market->translated_name,
            'region' => $market->region,
            'code'   => $kode,
            'left'   => round((($x - $bboxX0) * $scale) / $mapW * 100, 3),
            'top'    => round((($y - $bboxY0) * $scale) / $mapH * 100, 3),
            'petaX'  => round((($x - $bboxX0) * $scale), 3),
            'petaY'  => round((($y - $bboxY0) * $scale), 3),
            'kartu'  => $titikKartu ? [
                'x'       => $titikKartu['x'],
                'y'       => $titikKartu['y'],
                'kiri'    => round($titikKartu['x'] / $mapW * 100, 3),
                'atas'    => round($titikKartu['y'] / $mapH * 100, 3),
            ] : null,
        ];
    }

    $bentukNegara = collect(config('country-shapes', []))
        ->only(collect($markers)->pluck('code'))
        ->all();

    $byRegion = $markets->groupBy('region');
@endphp
<div x-data="{
        tunjuk: null,
        kunci: null,
        get aktif() { return this.kunci ?? this.tunjuk },
        tekan(kode) { this.kunci = this.kunci === kode ? null : kode },
     }">

    <div class="relative aspect-[900/441] w-full {{ $showList ? '' : 'hidden md:block' }}">
        <img src="{{ asset('images/world-map.svg') }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 h-full w-full select-none">

        <svg viewBox="0 0 900 440.70631074413296" aria-hidden="true"
             class="pointer-events-none absolute inset-0 h-full w-full select-none">
            
            <defs>
                <pattern id="sorot-titik" width="5" height="5" patternUnits="userSpaceOnUse">
                    <circle cx="2.5" cy="2.5" r="1.98" fill="#332619"/>
                </pattern>
            </defs>

            @foreach($bentukNegara as $kode => $bentuk)
                <path d="{{ $bentuk }}" fill="url(#sorot-titik)"
                      stroke="#332619" stroke-width="1.9" stroke-linecap="round"
                      stroke-dasharray="0.01 5" opacity="0"
                      class="transition-opacity duration-200"
                      x-bind:opacity="aktif === '{{ $kode }}' ? '1' : '0'"/>
            @endforeach

            @foreach($markers as $marker)
                @if($marker['kartu'])
                    <line x1="{{ $marker['petaX'] }}" y1="{{ $marker['petaY'] }}"
                          x2="{{ $marker['kartu']['x'] }}" y2="{{ $marker['kartu']['y'] }}"
                          stroke="#332619" stroke-width="0.9" stroke-linecap="round" opacity="0"
                          class="transition-opacity duration-200"
                          x-bind:opacity="aktif === '{{ $marker['code'] }}' ? '0.75' : '0'"/>
                @endif
            @endforeach
        </svg>

        @foreach($markers as $marker)
            <div class="group absolute -translate-x-1/2 -translate-y-1/2 {{ $showList ? '' : 'hidden md:block' }}"
                 style="left: {{ $marker['left'] }}%; top: {{ $marker['top'] }}%;"
                 x-on:mouseenter="tunjuk = '{{ $marker['code'] }}'"
                 x-on:mouseleave="tunjuk = null">

                <button type="button"
                        x-on:click="tekan('{{ $marker['code'] }}')"
                        x-on:focus="tunjuk = '{{ $marker['code'] }}'"
                        x-on:blur="tunjuk = null"
                        x-bind:aria-pressed="kunci === '{{ $marker['code'] }}' ? 'true' : 'false'"
                        class="relative block h-6 w-6 focus:outline-none">
                    <span class="sr-only">{{ $marker['name'] }} — {{ $marker['region'] }}</span>

                    <span aria-hidden="true"
                          x-bind:style="aktif === '{{ $marker['code'] }}'
                              ? 'background-color:#e2a862'
                              : ''"
                          class="absolute left-1/2 top-1/2 block h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2
                                 rounded-full bg-site-forest ring-[3px] ring-site-gilt-deep/30
                                 transition-transform duration-200
                                 group-hover:scale-110 group-focus-within:scale-110
                                 sm:h-3 sm:w-3 sm:ring-4"></span>
                </button>

            </div>
        @endforeach

        @foreach($markers as $marker)
            @if($marker['kartu'])

                <div class="pointer-events-none absolute z-10 hidden w-[14.2%] min-w-[8rem]
                            -translate-x-1/2 -translate-y-1/2 rounded-control
                            bg-site-forest px-3.5 py-3 text-center lg:block
                            shadow-[0_20px_44px_-16px_rgba(51,38,25,0.55)]
                            transition-opacity duration-200"
                     style="left: {{ $marker['kartu']['kiri'] }}%; top: {{ $marker['kartu']['atas'] }}%; opacity: 0"
                     
                     x-bind:style="{ opacity: aktif === '{{ $marker['code'] }}' ? 1 : 0 }"
                     aria-hidden="true">
                    <span class="block font-site-display font-bold leading-snug text-white text-site-small">
                        {{ $marker['name'] }}
                    </span>

                    <span class="mt-1 block font-site-accent text-site-micro font-medium uppercase
                                 leading-tight tracking-[0.12em] text-site-gilt">
                        {{ $marker['region'] }}
                    </span>

                </div>
            @endif
        @endforeach
    </div>

    @if($showList)
        @if($detailed)

            <div class="mt-10 grid gap-5 sm:grid-cols-2 md:mt-14 lg:grid-cols-3">
                @foreach($byRegion as $region => $countries)
                    <div class="flex flex-col rounded-corner border border-line bg-site-canvas p-5">

                        <div>
                            <h3 class="display text-site-title text-site-forest">{{ $region }}</h3>

                            <p class="eyebrow mt-1">
                                {{ trans_choice('site.countries_count', $countries->count(), ['count' => $countries->count()]) }}
                            </p>
                        </div>

                        <div class="mt-4 border-t border-line pt-4">
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach($countries as $country)
                                    @php $k = $country->country_code; @endphp

                                    <li>
                                        
                                        <button type="button"
                                                x-on:mouseenter="tunjuk = '{{ $k }}'"
                                                x-on:mouseleave="tunjuk = null"
                                                x-on:focus="tunjuk = '{{ $k }}'"
                                                x-on:blur="tunjuk = null"
                                                x-on:click="tekan('{{ $k }}')"
                                                x-bind:aria-pressed="kunci === '{{ $k }}' ? 'true' : 'false'"
                                                x-bind:style="aktif === '{{ $k }}'
                                                    ? { backgroundColor: '#332619', borderColor: '#332619' }
                                                    : { backgroundColor: '', borderColor: '' }"
                                                class="inline-flex items-center gap-2 rounded-full border border-line
                                                       bg-site-canvas py-1 pl-2 pr-3
                                                       transition-colors duration-200 focus:outline-none
                                                       focus-visible:ring-2 focus-visible:ring-site-gilt-deep/60
                                                       focus-visible:ring-offset-2 focus-visible:ring-offset-site-canvas">
                                            <span x-bind:style="aktif === '{{ $k }}'
                                                      ? { color: '#e2a862', borderColor: 'rgba(255,255,255,0.28)' }
                                                      : { color: '', borderColor: '' }"
                                                  class="shrink-0 border-r border-line pr-2 font-site-accent
                                                         text-site-micro font-semibold uppercase tracking-[0.08em]
                                                         text-site-gilt-deep transition-colors duration-200"
                                                  aria-hidden="true">
                                                {{ $k }}
                                            </span>

                                            <span x-bind:style="aktif === '{{ $k }}' ? { color: '#ffffff' } : { color: '' }"
                                                  class="font-site-body text-site-small font-semibold text-site-forest
                                                         transition-colors duration-200">
                                                {{ $country->translated_name }}
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid gap-x-8 gap-y-10 sm:grid-cols-2 md:mt-14 lg:grid-cols-4">
                @foreach($byRegion as $region => $countries)
                    <div>
                        <p class="eyebrow">{{ $region }}</p>

                        <ul class="mt-4 space-y-2.5">
                            @foreach($countries as $country)
                                <li class="text-ink-muted text-site-small">{{ $country->translated_name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
