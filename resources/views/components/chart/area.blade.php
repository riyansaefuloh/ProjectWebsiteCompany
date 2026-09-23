@props([
    'labels',            // ['Sep 2025', 'Okt 2025', …] — dipakai keterangan & tabel
    'values',            // [8, 3, 12, …]

    // Sebutan yang ditulis di sumbu X. Dibiarkan kosong berarti memakai
    // $labels apa adanya. Dipisah karena sumbu butuh sebutan sependek mungkin
    // agar semuanya muat, sedangkan keterangan melayang justru harus lengkap —
    // "Feb" saja tidak memberi tahu tahun berapa.
    'axisLabels' => null,
    'seriesLabel' => 'Nilai',

    // Nama ikon untuk lencana di keterangan melayang — salah satu nama yang
    // dikenal komponen icon.admin. Dibiarkan kosong berarti lencananya berisi
    // titik polos; grafik ini dipakai bermacam deret dan tidak semuanya punya
    // ikon yang pantas.
    //
    // Namanya ditulis TANPA kurung sudut. Penyusun tag Blade memindai teks
    // mentah berkas ini dan tidak mengenal komentar, jadi tag komponen yang
    // ditulis lengkap di sini akan benar-benar disusun jadi pemanggilan —
    // di tengah daftar prop, tempat $component belum ada.
    'seriesIcon' => null,

    // Tinggi minimum, bukan tinggi tetap: grafiknya memenuhi ruang yang
    // diberikan induknya, dan angka ini hanya menahan agar ia tidak pernah
    // memampat sampai tak terbaca.
    'minHeight' => 'min-h-[220px]',
])

@php
    $titik      = array_values($values);
    $sebut      = array_values($labels);
    $sebutSumbu = array_values($axisLabels ?? $labels);
    $jumlah     = count($titik);

    $tertinggi = $jumlah ? max($titik) : 0;
    $langkah   = 1;

    if ($tertinggi > 0) {
        $pangkat = 10 ** floor(log10($tertinggi));

        foreach ([1, 2, 5, 10] as $kali) {
            $langkah = $kali * $pangkat;
            if (ceil($tertinggi / $langkah) <= 5) {
                break;
            }
        }
    }

    $batas  = max($langkah, (int) (ceil($tertinggi / $langkah) * $langkah));
    $garis  = range(0, $batas, $langkah);

    // Lebar kanvas dalam satuan viewBox. Tingginya dipatok 300 supaya
    // perhitungannya bulat; SVG-nya sendiri diregangkan lewat CSS.
    $W = 1000;
    $H = 300;

    $koordinat = [];

    foreach ($titik as $i => $nilai) {
        $koordinat[] = [
            'x' => ($i + 0.5) / max($jumlah, 1) * $W,
            'y' => $H - ($batas > 0 ? $nilai / $batas : 0) * $H,
        ];
    }

    $jalur = '';

    if ($jumlah === 1) {
        $jalur = "M {$koordinat[0]['x']} {$koordinat[0]['y']}";
    } elseif ($jumlah > 1) {
        // Kemiringan tiap ruas, dalam satuan viewBox.
        $sekan = [];

        for ($i = 0; $i < $jumlah - 1; $i++) {
            $lebar = $koordinat[$i + 1]['x'] - $koordinat[$i]['x'];

            $sekan[$i] = $lebar != 0.0
                ? ($koordinat[$i + 1]['y'] - $koordinat[$i]['y']) / $lebar
                : 0.0;
        }

        // Tebakan awal: rata-rata dua ruas yang mengapit tiap titik. Titik
        // ujung hanya punya satu tetangga, jadi ia memakai ruasnya sendiri.
        $miring = [0 => $sekan[0]];

        for ($i = 1; $i < $jumlah - 1; $i++) {
            $miring[$i] = ($sekan[$i - 1] + $sekan[$i]) / 2;
        }

        $miring[$jumlah - 1] = $sekan[$jumlah - 2];

        for ($i = 0; $i < $jumlah - 1; $i++) {
            if ($sekan[$i] == 0.0) {
                $miring[$i]     = 0.0;
                $miring[$i + 1] = 0.0;

                continue;
            }

            $a = $miring[$i] / $sekan[$i];
            $b = $miring[$i + 1] / $sekan[$i];

            if ($a < 0) {
                $miring[$i] = 0.0;
                $a = 0.0;
            }

            if ($b < 0) {
                $miring[$i + 1] = 0.0;
                $b = 0.0;
            }

            $jarak = $a * $a + $b * $b;

            if ($jarak > 9) {
                $tarik = 3 / sqrt($jarak);

                $miring[$i]     = $tarik * $a * $sekan[$i];
                $miring[$i + 1] = $tarik * $b * $sekan[$i];
            }
        }

        // Kemiringan diubah jadi titik kendali Bezier, sepertiga lebar ruas
        // dari masing-masing ujungnya.
        $jalur = 'M ' . round($koordinat[0]['x'], 2) . ' ' . round($koordinat[0]['y'], 2);

        for ($i = 0; $i < $jumlah - 1; $i++) {
            $p1 = $koordinat[$i];
            $p2 = $koordinat[$i + 1];

            $sepertiga = ($p2['x'] - $p1['x']) / 3;

            $jalur .= ' C ' . round($p1['x'] + $sepertiga, 2)
                    . ' ' . round($p1['y'] + $miring[$i] * $sepertiga, 2)
                    . ', ' . round($p2['x'] - $sepertiga, 2)
                    . ' ' . round($p2['y'] - $miring[$i + 1] * $sepertiga, 2)
                    . ', ' . round($p2['x'], 2) . ' ' . round($p2['y'], 2);
        }
    }

    // Bidang arsir: jalur yang sama, ditutup ke dasar sumbu.
    $arsir = $jalur !== ''
        ? $jalur . ' L ' . round(end($koordinat)['x'], 2) . " {$H} L " . round($koordinat[0]['x'], 2) . " {$H} Z"
        : '';

    $dataAlpine = [];

    foreach ($titik as $i => $nilai) {
        $kiri = round(($i + 0.5) / max($jumlah, 1) * 100, 4);
        $atas = round($batas > 0 ? (1 - $nilai / $batas) * 100 : 100, 4);

        $dataAlpine[] = [
            'label' => $sebut[$i] ?? '',
            'sumbu' => $sebutSumbu[$i] ?? ($sebut[$i] ?? ''),
            'nilai' => $nilai,
            'kiri'  => $kiri,
            'atas'  => $atas,
            'bawah' => $atas < 45,
            'sisi'  => $kiri < 12 ? 'kiri' : ($kiri > 88 ? 'kanan' : 'tengah'),
        ];
    }

    $loncat = $jumlah > 14 ? 2 : 1;

    foreach ($dataAlpine as $i => $d) {
        $dataAlpine[$i]['tampil'] = ($jumlah - 1 - $i) % $loncat === 0;
    }

    $idGradien = 'grad-' . \Illuminate\Support\Str::random(6);
@endphp

<div x-data="{ aktif: null }" {{ $attributes->class('flex w-full flex-col') }}>

    <div class="flex min-h-0 flex-1 gap-3 {{ $minHeight }}">

        <div class="flex shrink-0 flex-col-reverse justify-between text-right text-admin-caption tabular-nums text-ink-faint"
             aria-hidden="true">
            @foreach($garis as $nilai)
                <span class="leading-none">{{ $nilai }}</span>
            @endforeach
        </div>

        <div class="relative min-w-0 flex-1">
            <svg viewBox="0 0 {{ $W }} {{ $H }}" preserveAspectRatio="none"
                 class="h-full w-full overflow-visible" aria-hidden="true">
                <defs>
                    <linearGradient id="{{ $idGradien }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="var(--color-brand)" stop-opacity="0.18"/>
                        <stop offset="100%" stop-color="var(--color-brand)" stop-opacity="0"/>
                    </linearGradient>
                </defs>

                @foreach($garis as $nilai)
                    @php $y = $H - ($batas > 0 ? $nilai / $batas : 0) * $H; @endphp
                    <line x1="0" y1="{{ $y }}" x2="{{ $W }}" y2="{{ $y }}"
                          stroke="var(--color-line)" stroke-width="1"
                          vector-effect="non-scaling-stroke"/>
                @endforeach

                @if($arsir !== '')
                    <path d="{{ $arsir }}" fill="url(#{{ $idGradien }})"/>
                @endif

                @if($jalur !== '')
                    
                    <path d="{{ $jalur }}" fill="none" stroke="var(--color-brand)"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          vector-effect="non-scaling-stroke"/>
                @endif
            </svg>

            @foreach($dataAlpine as $i => $d)
                <button type="button"
                        x-on:mouseenter="aktif = {{ $i }}" x-on:focus="aktif = {{ $i }}"
                        x-on:mouseleave="aktif = null" x-on:blur="aktif = null"
                        class="absolute top-0 h-full focus:outline-none"
                        style="left: {{ round($i / max($jumlah, 1) * 100, 4) }}%; width: {{ round(100 / max($jumlah, 1), 4) }}%;">
                    <span class="sr-only">{{ $d['label'] }}: {{ $d['nilai'] }}</span>
                </button>
            @endforeach

            @foreach($dataAlpine as $i => $d)
                @php
                    
                    $geser = match ($d['sisi']) {
                        'kiri'  => 'translateX(-18px)',
                        'kanan' => 'translateX(calc(-100% + 18px))',
                        default => 'translateX(-50%)',
                    };

                    // 18px: jarak dari titik ke sisi kartu. Ujung ekornya
                    // berhenti ±11px dari titik — persis di luar tepi penanda,
                    // tanpa menyentuhnya.
                    $tegak = $d['bawah'] ? 'top: 18px;' : 'bottom: 18px;';
                @endphp

                <template x-if="aktif === {{ $i }}">
                    <div class="pointer-events-none absolute inset-y-0 z-10" style="left: {{ $d['kiri'] }}%;">

                        <div class="absolute inset-y-0 w-px border-l border-dashed border-line-strong"></div>

                        <div class="absolute" style="top: {{ $d['atas'] }}%;">

                            <span class="absolute flex h-[15px] w-[15px] -translate-x-1/2 -translate-y-1/2
                                         items-center justify-center rounded-full bg-canvas
                                         shadow-[0_1px_4px_rgba(26,29,27,0.28)]">
                                <span class="h-[7px] w-[7px] rounded-full bg-brand"></span>
                            </span>

                            <div class="absolute whitespace-nowrap rounded-[16px] bg-canvas p-3
                                        shadow-[0_18px_38px_-14px_rgba(26,29,27,0.30),0_3px_10px_-3px_rgba(26,29,27,0.10)]"
                                 style="{{ $tegak }} left: 0; transform: {{ $geser }};">

                                <p class="text-admin-label text-ink-muted">{{ $d['label'] }}</p>

                                <div class="mt-2 flex items-center gap-2.5 rounded-[11px] bg-mist px-2.5 py-2">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center
                                                 rounded-full bg-brand text-canvas">
                                        @if($seriesIcon)
                                            <x-icon.admin :name="$seriesIcon" size="h-[13px] w-[13px]" />
                                        @else
                                            <span class="h-[6px] w-[6px] rounded-full bg-canvas"></span>
                                        @endif
                                    </span>

                                    <span class="text-admin-strong text-ink-muted">{{ $seriesLabel }}</span>

                                    <span class="text-admin-title tabular-nums text-ink">{{ $d['nilai'] }}</span>
                                </div>
                            </div>

                            <span class="absolute h-2.5 w-2.5 rounded-[2px] bg-canvas"
                                  style="{{ $tegak }} left: 0;
                                         transform: translate(-50%, {{ $d['bawah'] ? '-50%' : '50%' }}) rotate(45deg);"></span>
                        </div>
                    </div>
                </template>
            @endforeach
        </div>
    </div>

    {{-- ── Sumbu X ──────────────────────────────────────────────────────── --}}
    <div class="mt-3 flex" aria-hidden="true">
        
        <div class="shrink-0 pr-3 text-right text-admin-caption tabular-nums text-transparent">{{ $batas }}</div>

        <div class="flex min-w-0 flex-1">
            @foreach($dataAlpine as $i => $d)
                
                <span class="min-w-0 flex-1 truncate text-center text-admin-caption text-ink-faint"
                      style="width: {{ round(100 / max($jumlah, 1), 4) }}%;"
                      x-bind:class="aktif === {{ $i }} && 'font-semibold text-ink'">{{ $d['tampil'] ? $d['sumbu'] : '' }}</span>
            @endforeach
        </div>
    </div>

    <details class="mt-4 group">
        <summary class="cursor-pointer text-admin-label font-semibold text-ink-faint transition-colors hover:text-ink">
            Lihat sebagai tabel
        </summary>

        <div class="mt-3 overflow-hidden rounded-corner border border-line">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-line bg-mist/60">
                        <th class="px-4 py-2 text-admin-overline uppercase text-ink-faint">Bulan</th>
                        <th class="px-4 py-2 text-right text-admin-overline uppercase text-ink-faint">{{ $seriesLabel }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataAlpine as $d)
                        <tr class="border-b border-line last:border-0">
                            <td class="px-4 py-2 text-admin-body text-ink-muted">{{ $d['label'] }}</td>
                            <td class="px-4 py-2 text-right text-admin-body tabular-nums text-ink">{{ $d['nilai'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
