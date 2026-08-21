@props([
    'labels',            // ['Sep 2025', 'Okt 2025', …] — dipakai keterangan & tabel
    'values',            // [8, 3, 12, …]

    // Sebutan yang ditulis di sumbu X. Dibiarkan kosong berarti memakai
    // $labels apa adanya. Dipisah karena sumbu butuh sebutan sependek mungkin
    // agar semuanya muat, sedangkan keterangan melayang justru harus lengkap —
    // "Feb" saja tidak memberi tahu tahun berapa.
    'axisLabels' => null,
    'seriesLabel' => 'Nilai',

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

    /*
     * Batas atas sumbu Y dibulatkan ke angka yang "enak dibaca".
     *
     * Memakai nilai tertinggi apa adanya menghasilkan sumbu seperti 0–29, dan
     * garis bantunya jatuh di 7,25 / 14,5 / 21,75 — angka yang tidak pernah
     * dipakai siapa pun untuk membandingkan. Langkahnya dicari dari deret
     * 1, 2, 5 dikali pangkat sepuluh, lalu dipilih yang menghasilkan tiga
     * sampai enam garis.
     */
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

    /*
     * Titik diletakkan di TENGAH pita bulannya, bukan di tepi.
     *
     * Sebulan adalah rentang waktu, bukan satu saat. Menaruhnya di tepi
     * membuat garis mulai dan berakhir menempel di dinding, dan sebutan
     * bulannya — yang berada di tengah pita — tidak lagi lurus dengan
     * titiknya.
     */
    $koordinat = [];

    foreach ($titik as $i => $nilai) {
        $koordinat[] = [
            'x' => ($i + 0.5) / max($jumlah, 1) * $W,
            'y' => $H - ($batas > 0 ? $nilai / $batas : 0) * $H,
        ];
    }

    /*
     * Garis dihaluskan dengan Catmull-Rom yang diubah jadi kurva Bezier.
     *
     * Rumus bawaan "kurva mulus" di banyak pustaka grafik membuat garis
     * melengkung melewati nilai yang sebenarnya — sebuah bulan bisa terlihat
     * menyentuh angka yang tidak pernah terjadi. Catmull-Rom selalu melewati
     * titik aslinya; yang dihaluskan hanya jalur di antaranya.
     */
    $jalur = '';

    if ($jumlah === 1) {
        $jalur = "M {$koordinat[0]['x']} {$koordinat[0]['y']}";
    } elseif ($jumlah > 1) {
        $jalur = 'M ' . round($koordinat[0]['x'], 2) . ' ' . round($koordinat[0]['y'], 2);

        for ($i = 0; $i < $jumlah - 1; $i++) {
            $p0 = $koordinat[max($i - 1, 0)];
            $p1 = $koordinat[$i];
            $p2 = $koordinat[$i + 1];
            $p3 = $koordinat[min($i + 2, $jumlah - 1)];

            $c1x = $p1['x'] + ($p2['x'] - $p0['x']) / 6;
            $c1y = $p1['y'] + ($p2['y'] - $p0['y']) / 6;
            $c2x = $p2['x'] - ($p3['x'] - $p1['x']) / 6;
            $c2y = $p2['y'] - ($p3['y'] - $p1['y']) / 6;

            $jalur .= ' C ' . round($c1x, 2) . ' ' . round($c1y, 2)
                    . ', ' . round($c2x, 2) . ' ' . round($c2y, 2)
                    . ', ' . round($p2['x'], 2) . ' ' . round($p2['y'], 2);
        }
    }

    // Bidang arsir: jalur yang sama, ditutup ke dasar sumbu.
    $arsir = $jalur !== ''
        ? $jalur . ' L ' . round(end($koordinat)['x'], 2) . " {$H} L " . round($koordinat[0]['x'], 2) . " {$H} Z"
        : '';

    // Data untuk Alpine: posisi dalam persen supaya penunjuk dan keterangan
    // bisa diletakkan dengan CSS biasa, tanpa menghitung ulang di JavaScript.
    $dataAlpine = [];

    foreach ($titik as $i => $nilai) {
        $dataAlpine[] = [
            'label' => $sebut[$i] ?? '',
            'sumbu' => $sebutSumbu[$i] ?? ($sebut[$i] ?? ''),
            'nilai' => $nilai,
            'kiri'  => round(($i + 0.5) / max($jumlah, 1) * 100, 4),
            'atas'  => round($batas > 0 ? (1 - $nilai / $batas) * 100 : 100, 4),
        ];
    }

    /*
     * Penjarangan sebutan sumbu X — jaring pengaman, bukan aturan utama.
     *
     * Ambangnya sengaja tinggi (di atas 14 titik). Menyembunyikan sebagian
     * sebutan menimbulkan salah baca yang lebih buruk daripada sesak: orang
     * menghitung sebutan yang terlihat dan menyimpulkan grafiknya memuat enam
     * bulan padahal dua belas. Jalan keluar yang benar adalah memendekkan
     * sebutannya (lihat prop axisLabels), bukan membuangnya.
     *
     * Dihitung mundur dari yang TERAKHIR: titik terbaru yang paling sering
     * dicari, jadi ia yang harus selalu bersebutan.
     */
    $loncat = $jumlah > 14 ? 2 : 1;

    foreach ($dataAlpine as $i => $d) {
        $dataAlpine[$i]['tampil'] = ($jumlah - 1 - $i) % $loncat === 0;
    }

    $idGradien = 'grad-' . \Illuminate\Support\Str::random(6);
@endphp

<div x-data="{ aktif: null }" {{ $attributes->class('flex w-full flex-col') }}>

    {{-- min-h-0: tanpa itu, anak flex menolak menyusut di bawah tinggi isinya
         dan seluruh kartu ikut memanjang alih-alih grafiknya yang menyesuaikan. --}}
    <div class="flex min-h-0 flex-1 gap-3 {{ $minHeight }}">
        {{-- Sumbu Y sebagai HTML, bukan <text> di dalam SVG: SVG-nya
             diregangkan mendatar mengikuti lebar kartu, dan teks di dalamnya
             ikut melar jadi gepeng. --}}
        {{-- Tanpa tinggi yang ditulis sendiri: sebagai anak flex ia otomatis
             setinggi bidang gambarnya. Sebelumnya angkanya disalin dari prop
             tinggi lewat str_replace — begitu propnya berubah bentuk, sumbu Y
             dan bidang gambarnya diam-diam jadi beda tinggi. --}}
        <div class="flex shrink-0 flex-col-reverse justify-between text-right text-[11px] tabular-nums text-ink-faint"
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

                {{-- Garis bantu mendatar. Tipis dan pucat: ia alat bantu baca,
                     bukan bagian dari datanya. --}}
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
                    {{-- non-scaling-stroke: tanpa ini, garisnya ikut diregangkan
                         mendatar bersama viewBox dan tebalnya berubah-ubah
                         mengikuti lebar layar. --}}
                    <path d="{{ $jalur }}" fill="none" stroke="var(--color-brand)"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          vector-effect="non-scaling-stroke"/>
                @endif
            </svg>

            {{-- ── Lapisan sentuh ────────────────────────────────────────────
                 Satu tombol selebar pita per bulan, setinggi grafik. Sasaran
                 sentuhnya jauh lebih besar daripada titiknya sendiri, jadi
                 keterangannya tidak perlu dikejar dengan tetikus. --}}
            @foreach($dataAlpine as $i => $d)
                <button type="button"
                        x-on:mouseenter="aktif = {{ $i }}" x-on:focus="aktif = {{ $i }}"
                        x-on:mouseleave="aktif = null" x-on:blur="aktif = null"
                        class="absolute top-0 h-full focus:outline-none"
                        style="left: {{ round($i / max($jumlah, 1) * 100, 4) }}%; width: {{ round(100 / max($jumlah, 1), 4) }}%;">
                    <span class="sr-only">{{ $d['label'] }}: {{ $d['nilai'] }}</span>
                </button>
            @endforeach

            {{-- ── Penunjuk tegak & titik ───────────────────────────────── --}}
            @foreach($dataAlpine as $i => $d)
                <template x-if="aktif === {{ $i }}">
                    <div class="pointer-events-none absolute inset-y-0" style="left: {{ $d['kiri'] }}%;">
                        <div class="absolute inset-y-0 w-px border-l border-dashed border-line-strong"></div>

                        <div class="absolute h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full
                                    border-2 border-brand bg-canvas"
                             style="top: {{ $d['atas'] }}%;"></div>
                    </div>
                </template>
            @endforeach

            {{-- ── Keterangan ────────────────────────────────────────────────
                 Diletakkan mengikuti tepi kiri/kanan supaya tidak keluar dari
                 kartu di bulan pertama dan terakhir. --}}
            @foreach($dataAlpine as $i => $d)
                <template x-if="aktif === {{ $i }}">
                    <div class="pointer-events-none absolute z-10 whitespace-nowrap rounded-corner border border-line
                                bg-canvas px-3 py-2 shadow-[0_12px_28px_-12px_rgba(26,29,27,0.35)]"
                         style="left: {{ $d['kiri'] }}%; top: {{ $d['atas'] }}%;
                                transform: translate({{ $i > $jumlah / 2 ? '-100%' : '0' }}, -50%)
                                           translateX({{ $i > $jumlah / 2 ? '-14px' : '14px' }});">
                        <p class="text-[12px] font-semibold text-ink">{{ $d['label'] }}</p>
                        <p class="mt-0.5 flex items-center gap-1.5 text-[12px] text-ink-muted">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand"></span>
                            {{ $seriesLabel }}:
                            <span class="font-semibold tabular-nums text-ink">{{ $d['nilai'] }}</span>
                        </p>
                    </div>
                </template>
            @endforeach
        </div>
    </div>

    {{-- ── Sumbu X ──────────────────────────────────────────────────────── --}}
    <div class="mt-3 flex" aria-hidden="true">
        {{-- Ruang kosong selebar sumbu Y supaya sebutan bulan tetap lurus
             dengan titiknya. --}}
        <div class="shrink-0 pr-3 text-right text-[11px] tabular-nums text-transparent">{{ $batas }}</div>

        <div class="flex min-w-0 flex-1">
            @foreach($dataAlpine as $i => $d)
                {{-- Yang dijarangkan tetap dirender sebagai wadah kosong,
                     supaya lebar tiap pita tetap sama dan sebutan yang tersisa
                     tidak bergeser dari titiknya. --}}
                <span class="min-w-0 flex-1 truncate text-center text-[11px] text-ink-faint"
                      style="width: {{ round(100 / max($jumlah, 1), 4) }}%;"
                      x-bind:class="aktif === {{ $i }} && 'font-semibold text-ink'">{{ $d['tampil'] ? $d['sumbu'] : '' }}</span>
            @endforeach
        </div>
    </div>

    {{-- ── Kembaran tabel ────────────────────────────────────────────────
         Grafik ini tidak bisa dibaca pembaca layar, dan angkanya tidak bisa
         disalin dari sebuah garis. Tabel yang sama datanya disediakan di
         balik satu klik — bukan disembunyikan, supaya siapa pun bisa
         membukanya. --}}
    <details class="mt-4 group">
        <summary class="cursor-pointer text-[12px] font-semibold text-ink-faint transition-colors hover:text-ink">
            Lihat sebagai tabel
        </summary>

        <div class="mt-3 overflow-hidden rounded-corner border border-line">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-line bg-mist/60">
                        <th class="px-4 py-2 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">Bulan</th>
                        <th class="px-4 py-2 text-right text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">{{ $seriesLabel }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataAlpine as $d)
                        <tr class="border-b border-line last:border-0">
                            <td class="px-4 py-2 text-[13px] text-ink-muted">{{ $d['label'] }}</td>
                            <td class="px-4 py-2 text-right text-[13px] tabular-nums text-ink">{{ $d['nilai'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
