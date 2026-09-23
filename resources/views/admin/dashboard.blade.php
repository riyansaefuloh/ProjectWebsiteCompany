<x-layouts.app>

<div class="mx-auto max-w-[1400px]">

    @php
        $zona = \App\Models\Setting::where('key', 'timezone')->value('value') ?: config('app.timezone');
        $kini = now()->timezone($zona);
    @endphp

    <div class="mb-8">
        <h1 class="text-admin-display text-heading">
            Selamat datang, {{ auth()->user()->name }}!
            <span aria-hidden="true">👋</span>
        </h1>

        <p class="mt-2 text-admin-body text-ink-muted">
            {{ $kini->locale('id')->translatedFormat('l, j F Y') }}
        </p>
        
    </div>

    @php
        
        $perluDiurus = collect($expiredCerts)->concat($expiringSoonCerts)
            ->unique('id')
            ->sortBy('expires_at')
            ->values();

        $adaKedaluwarsa = collect($expiredCerts)->isNotEmpty();
    @endphp

    @if($perluDiurus->isNotEmpty())
        @php
            $jumlahLewat  = collect($expiredCerts)->count();
            $jumlahSegera = collect($expiringSoonCerts)->count();
        @endphp

        <div class="card mb-8" role="alert">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                <div class="flex min-w-0 items-center gap-2.5">
                    <span @class([
                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-control',
                        'bg-danger/10 text-danger' => $adaKedaluwarsa,
                        'bg-mist text-ink-muted'   => ! $adaKedaluwarsa,
                    ])>
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <p @class([
                            'text-admin-title',
                            'text-danger' => $adaKedaluwarsa,
                            'text-ink'    => ! $adaKedaluwarsa,
                        ])>
                            @if($jumlahLewat > 0 && $jumlahSegera > 0)
                                {{ $jumlahLewat }} sertifikasi sudah kedaluwarsa,
                                {{ $jumlahSegera }} lagi akan menyusul
                            @elseif($jumlahLewat > 0)
                                {{ $jumlahLewat }} sertifikasi sudah kedaluwarsa
                            @else
                                {{ $jumlahSegera }} sertifikasi akan segera kedaluwarsa
                            @endif
                        </p>

                        <p class="mt-0.5 text-admin-label text-ink-muted">
                            Sertifikasi yang kedaluwarsa tidak lagi ditampilkan sebagai bukti kelayakan di situs publik. 
                            Segera lakukan perpanjangan untuk memastikan informasi tetap valid.
                        </p>
                    </div>
                </div>

                @can('manage certifications')
                    <a href="{{ route('admin.certifications.index') }}"
                       class="shrink-0 text-admin-strong font-semibold text-brand underline-offset-4 hover:underline">
                        Kelola sertifikasi
                    </a>
                @endcan
            </div>

            <div class="space-y-2 p-5">
                @foreach($perluDiurus as $cert)
                        @php

                            $nama  = $cert->translated_name;
                            $lewat = $cert->expires_at->isPast();
                            $logo = $cert->getFirstMedia('logos');

                            $hari = (int) abs(
                                now()->startOfDay()->diffInDays($cert->expires_at->copy()->startOfDay())
                            );
                        @endphp

                        <div @class([
                            'flex flex-wrap items-center justify-between gap-x-4 gap-y-1
                             rounded-control border px-4 py-3',
                            'border-danger/20 bg-danger/5' => $lewat,
                            'border-line bg-mist'          => ! $lewat,
                        ])>
                            <div class="flex min-w-0 items-center gap-3">
                                
                                @if($logo)
                                    <img src="{{ $logo->getUrl() }}" alt=""
                                         loading="lazy" width="40" height="40"
                                         class="h-10 w-10 shrink-0 rounded-control border border-line
                                                bg-canvas object-contain p-1"
                                         title="Logo {{ $nama ?: $cert->slug }}">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                 rounded-control border border-dashed border-line-strong
                                                 bg-canvas text-ink-faint"
                                          title="Belum ada logo">
                                        <x-icon.admin name="gallery" size="h-4 w-4" />
                                    </span>
                                @endif

                                <div class="min-w-0">
                                    <span class="block truncate text-admin-strong text-ink"
                                          title="{{ $nama ?: $cert->slug }}">{{ $nama ?: $cert->slug }}</span>
                                    <span class="mt-0.5 block truncate text-admin-caption text-ink-faint">{{ $cert->issuer }}</span>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-admin-body tabular-nums text-ink-muted">
                                    {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                                </span>

                                <span @class([
                                    'admin-pill w-[104px] justify-center text-admin-caption font-semibold',
                                    'bg-danger/15 text-danger'                 => $lewat,
                                    'border border-line bg-canvas text-ink-muted' => ! $lewat,
                                ])>
                                    {{ $lewat ? 'Lewat ' . $hari . ' hari' : $hari . ' hari lagi' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
            </div>
        </div>
    @endif

    @php
        $ubah = function (int $kini, int $lalu): ?int {
            return $lalu > 0 ? (int) round((($kini - $lalu) / $lalu) * 100) : null;
        };
        
        $inquiryBulanIni  = (int) ($chartMonthData[11] ?? 0);
        $inquiryBulanLalu = (int) ($chartMonthData[10] ?? 0);

        $trenInquiry   = $ubah($inquiryBulanIni, $inquiryBulanLalu);
        $trenKunjungan = $ubah((int) $visitsThisMonth, (int) $visitsLastMonth);
    @endphp

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- ── Kartu 1: inquiry ─────────────────────────────────────────── --}}
        @can('view inquiries')
            <a href="{{ route('admin.inquiries.index') }}" wire:navigate.hover
               class="card flex flex-col p-5 transition-colors hover:border-line-strong">

                <span class="flex items-start justify-between gap-3">
                    <span class="min-w-0 truncate text-admin-title text-heading">Inquiry bulan ini</span>

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                 bg-brand-wash text-brand">
                        <x-icon.admin name="inquiry" size="h-[18px] w-[18px]" />
                    </span>
                </span>

                <span class="mt-1 block text-admin-metric tabular-nums text-heading">
                    {{ number_format($inquiryBulanIni) }}
                </span>
                
                <span class="mt-5 flex items-end justify-between gap-3">
                    <span class="flex min-w-0 items-center gap-2">
                        @if($trenInquiry !== null)
                            <span @class([
                                'admin-pill gap-0.5
                                 text-admin-caption font-semibold tabular-nums',
                                'bg-brand/10 text-brand'      => $trenInquiry > 0,
                                'bg-danger/10 text-danger'    => $trenInquiry < 0,
                                'bg-mist-deep text-ink-muted' => $trenInquiry === 0,
                            ])>
                                @if($trenInquiry !== 0)
                                    
                                    <svg class="h-3 w-3 shrink-0 {{ $trenInquiry < 0 ? 'rotate-180' : '' }}"
                                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M8 13V3m0 0L3.5 7.5M8 3l4.5 4.5" stroke="currentColor"
                                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                                {{ abs($trenInquiry) }}%
                            </span>

                            <span class="truncate text-admin-label text-ink-muted">vs bulan lalu</span>
                        @else
                            
                            <span class="truncate text-admin-label text-ink-muted">
                                Belum ada pembanding bulan lalu
                            </span>
                        @endif
                    </span>

                    <span class="shrink-0 text-right">
                        <span class="block whitespace-nowrap text-admin-label text-ink-muted">Total inquiry</span>
                        <span class="block text-admin-title tabular-nums text-ink">
                            {{ number_format($totalInquiries) }}
                        </span>
                    </span>
                </span>
            </a>
        @endcan

        {{-- ── Kartu 2: produk ──────────────────────────────────────────── --}}
        @can('manage products')
            <a href="{{ route('admin.products.index') }}" wire:navigate.hover
               class="card flex flex-col p-5 transition-colors hover:border-line-strong">

                <span class="flex items-start justify-between gap-3">
                    <span class="min-w-0 truncate text-admin-title text-heading">Total produk</span>

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                 bg-brand-wash text-brand">
                        <x-icon.admin name="product" size="h-[18px] w-[18px]" />
                    </span>
                </span>

                <span class="mt-1 block text-admin-metric tabular-nums text-heading">
                    {{ number_format($totalProductsAll) }}
                </span>
                
                <span class="mt-5 flex flex-wrap items-center justify-between gap-2">
                    <span class="admin-pill bg-mist-deep
                                 text-admin-caption font-semibold text-ink-muted">
                        <span class="tabular-nums text-ink">{{ number_format($totalProducts) }}</span>
                        Aktif
                    </span>

                    <span class="admin-pill bg-mist-deep
                                 text-admin-caption font-semibold text-ink-muted">
                        <span class="tabular-nums text-ink">{{ number_format($draftProducts) }}</span>
                        Draf
                    </span>

                    <span class="admin-pill bg-mist-deep
                                 text-admin-caption font-semibold text-ink-muted">
                        <span class="tabular-nums text-ink">{{ number_format($featuredProducts) }}</span>
                        Unggulan
                    </span>
                </span>
            </a>
        @endcan

        {{-- Kartu 3: kunjungan --}}
        <div class="card flex flex-col p-5">

            <span class="flex items-start justify-between gap-3">
                <span class="min-w-0 truncate text-admin-title text-heading">Kunjungan bulan ini</span>

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                             bg-brand-wash text-brand">
                    <x-icon.admin name="external" size="h-[18px] w-[18px]" />
                </span>
            </span>

            <span class="mt-1 block text-admin-metric tabular-nums text-heading">
                {{ number_format($visitsThisMonth) }}
            </span>

            <span class="mt-5 flex items-end justify-between gap-3">
                <span class="flex min-w-0 items-center gap-2">
                    @if($trenKunjungan !== null)
                        <span @class([
                            'admin-pill gap-0.5
                             text-admin-caption font-semibold tabular-nums',
                            'bg-brand/10 text-brand'      => $trenKunjungan > 0,
                            'bg-danger/10 text-danger'    => $trenKunjungan < 0,
                            'bg-mist-deep text-ink-muted' => $trenKunjungan === 0,
                        ])>
                            @if($trenKunjungan !== 0)
                                <svg class="h-3 w-3 shrink-0 {{ $trenKunjungan < 0 ? 'rotate-180' : '' }}"
                                     viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 13V3m0 0L3.5 7.5M8 3l4.5 4.5" stroke="currentColor"
                                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @endif
                            {{ abs($trenKunjungan) }}%
                        </span>

                        <span class="truncate text-admin-label text-ink-muted">vs bulan lalu</span>
                    @else
                        <span class="truncate text-admin-label text-ink-muted">
                            Belum ada pembanding bulan lalu
                        </span>
                    @endif
                </span>
                
                <span class="shrink-0 text-right">
                    <span class="block whitespace-nowrap text-admin-label text-ink-muted">Total kunjungan</span>
                    <span class="block text-admin-title tabular-nums text-ink">
                        {{ number_format($totalVisits) }}
                    </span>
                </span>
            </span>
        </div>
    </div>

    @can('view inquiries')
        <div class="mb-8 grid gap-6 lg:grid-cols-5">

            <div class="lg:col-span-3">
        
            @php

                $bulan = collect($chartMonthLabels)->map(function ($l) {
                    try {
                        return \Carbon\Carbon::createFromFormat('!M Y', $l);
                    } catch (\Throwable $e) {
                        return null;
                    }
                });

                $labelBulan = $bulan
                    ->map(fn ($t, $i) => $t?->locale('id')->translatedFormat('M Y') ?: $chartMonthLabels[$i])
                    ->all();

                $totalBulan = count($chartMonthData);
                
                $sumbuUntuk = function (int $n) use ($bulan, $chartMonthLabels, $totalBulan) {
                    $keluar = [];

                    for ($i = max(0, $totalBulan - $n); $i < $totalBulan; $i++) {
                        $keluar[] = $bulan[$i]?->locale('id')->translatedFormat('M')
                            ?: $chartMonthLabels[$i];
                    }

                    return $keluar;
                };
                
                $periodeUntuk = function (int $n) use ($bulan, $totalBulan) {
                    $awal  = $bulan[max(0, $totalBulan - $n)] ?? null;
                    $akhir = $bulan[$totalBulan - 1] ?? null;

                    if (! $awal || ! $akhir) {
                        return null;
                    }

                    $bentuk = $awal->year === $akhir->year ? 'M' : 'M Y';

                    return $awal->locale('id')->translatedFormat($bentuk)
                         . ' – ' . $akhir->locale('id')->translatedFormat('M Y');
                };

                $pilihanRentang = [3, 6, 12];
                $rentangAwal    = 12;

                $periode = collect($pilihanRentang)
                    ->mapWithKeys(fn ($n) => [$n => $periodeUntuk($n)])
                    ->all();
            @endphp
            
            <div class="card flex h-full flex-col"
                 x-data="{ rentang: {{ $rentangAwal }}, periode: {{ \Illuminate\Support\Js::from($periode) }} }">
                <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="chart" size="h-[18px] w-[18px]" />
                        </span>

                        <div>
                            <h2 class="text-admin-title text-heading">Tren Inquiry</h2>
                            
                            <p class="mt-0.5 text-admin-label text-ink-muted">
                                Volume inquiry masuk periode
                                <span x-text="periode[rentang]">{{ $periode[$rentangAwal] }}</span>.
                            </p>
                        </div>
                    </div>
                    
                    <div class="inline-flex shrink-0 items-center gap-0.5 rounded-full border border-line bg-mist p-0.5"
                         role="group" aria-label="Rentang waktu grafik">
                        @foreach($pilihanRentang as $n)
                            
                            <button type="button" x-on:click="rentang = {{ $n }}"
                                    x-bind:aria-pressed="rentang === {{ $n }} ? 'true' : 'false'"
                                    aria-pressed="{{ $n === $rentangAwal ? 'true' : 'false' }}"
                                    x-bind:class="{
                                        'bg-canvas text-brand shadow-[0_1px_2px_rgba(26,29,27,0.10)]': rentang === {{ $n }},
                                        'text-ink-muted hover:text-ink': rentang !== {{ $n }},
                                    }"
                                    @class([
                                        'rounded-full px-3 py-1 text-admin-label font-semibold tabular-nums
                                         transition-colors focus-visible:outline-none focus-visible:ring-2
                                         focus-visible:ring-brand/30',
                                        'bg-canvas text-brand shadow-[0_1px_2px_rgba(26,29,27,0.10)]' => $n === $rentangAwal,
                                        'text-ink-muted hover:text-ink'                               => $n !== $rentangAwal,
                                    ])>{{ $n }} bulan</button>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-1 flex-col p-5">
                    @if($totalBulan > 0)
                        @foreach($pilihanRentang as $n)
                            @php $mulai = max(0, $totalBulan - $n); @endphp
                            
                            <div x-show="rentang === {{ $n }}"
                                 @if($n !== $rentangAwal) style="display: none;" @endif
                                 class="flex flex-1 flex-col rounded-corner border border-line p-5">
                                <x-chart.area :labels="array_slice($labelBulan, $mulai)"
                                              :axis-labels="$sumbuUntuk($n)"
                                              :values="array_slice($chartMonthData, $mulai)"
                                              series-label="Inquiry" series-icon="inquiry"
                                              class="flex-1" />
                            </div>
                        @endforeach
                    @else
                        <div class="flex flex-1 items-center justify-center rounded-corner border border-line p-5">
                            <p class="text-admin-body text-ink-faint">
                                Belum ada inquiry yang masuk pada rentang ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            </div>

            <div class="lg:col-span-2">
    
    @php
        $namaNegaraGrafik = config('countries', []);

        $peringkat = collect($chartCountryLabels)
            ->map(fn ($kode, $i) => [
                'kode'  => $kode,
                'nama'  => $namaNegaraGrafik[$kode] ?? $kode,
                'nilai' => $chartCountryData[$i] ?? 0,
            ])
            ->values();

        $tertinggiNegara = $peringkat->max('nilai') ?: 1;
    @endphp

    <div class="card flex h-full flex-col">
        
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex min-w-0 items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="market" size="h-[18px] w-[18px]" />
                </span>

                <div class="min-w-0">
                    <h2 class="text-admin-title text-heading">Distribusi Negara</h2>
                    <p class="mt-0.5 truncate text-admin-label text-ink-muted">
                        Peringkat negara berdasarkan volume inquiry.
                    </p>
                </div>
            </div>

        </div>

        @if($peringkat->isNotEmpty())

            <div class="flex-1 p-5">
                <div class="h-full overflow-hidden rounded-corner border border-line">
                    <table class="w-full table-fixed text-left">
                        <thead>
                            <tr class="border-b border-line bg-mist/60">
                                <th class="w-[12%] py-2.5 pl-4 pr-2 text-admin-overline uppercase text-ink-faint">#</th>
                                <th class="w-[45%] px-2 text-admin-overline uppercase text-ink-faint">Negara</th>
                                <th class="w-[27%] px-2"></th>
                                <th class="w-[16%] py-2.5 pl-2 pr-4 text-right text-admin-overline uppercase text-ink-faint">Inquiry</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($peringkat as $i => $n)
                                @php $panjang = round($n['nilai'] / $tertinggiNegara * 100, 2); @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    <td class="py-2.5 pl-4 pr-2 text-admin-body tabular-nums text-ink-faint">{{ $i + 1 }}</td>

                                    <td class="px-2 py-2.5">

                                        <x-admin.country :code="$n['kode']" size="md" class="min-w-0" />
                                    </td>

                                    <td class="px-2 py-2.5" aria-hidden="true">
                                        <span class="block h-1.5 w-full overflow-hidden rounded-full bg-mist">
                                            <span class="block h-full rounded-full bg-brand/70 transition-colors group-hover:bg-brand"
                                                  style="width: {{ $panjang }}%;"></span>
                                        </span>
                                    </td>

                                    <td class="py-2.5 pl-2 pr-4 text-right text-admin-strong tabular-nums text-ink">
                                        {{ number_format($n['nilai']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="px-6 py-16 text-center text-admin-body text-ink-faint">
                Belum ada inquiry yang masuk.
            </p>
        @endif
    </div>
            </div>
        </div>
    @endcan

    @can('view inquiries')
        @if($latestInquiries->isNotEmpty())
            
            <div class="card mb-8">
                
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                            <x-icon.admin name="inquiry" size="h-[18px] w-[18px]" />
                        </span>

                        <div>
                            <h2 class="text-admin-title text-heading">Inquiry terbaru</h2>
                            <p class="mt-0.5 text-admin-label text-ink-muted">Daftar inquiry terbaru.</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.inquiries.index') }}"
                       class="text-admin-strong font-semibold text-brand underline-offset-4 hover:underline">
                        Lihat semua inquiry
                    </a>
                </div>
                
                <div class="p-5">
                    <div class="overflow-hidden rounded-corner border border-line">
                        <div class="overflow-x-auto">
                    
                    @php
                        
                        $kolom = [
                            ['label' => 'Pembeli',    'lebar' => 'w-[21%]', 'rata' => 'text-left'],
                            ['label' => 'Perusahaan', 'lebar' => 'w-[20%]', 'rata' => 'text-left'],
                            ['label' => 'Produk',     'lebar' => 'w-[18%]', 'rata' => 'text-left'],
                            ['label' => 'Status',     'lebar' => 'w-[10%]', 'rata' => 'text-left'],
                            ['label' => 'Ditangani',  'lebar' => 'w-[13%]', 'rata' => 'text-left'],
                            ['label' => 'Masuk',      'lebar' => 'w-[11%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',       'lebar' => 'w-[7%]',  'rata' => 'text-right'],
                        ];
                    @endphp
                    
                    <table class="w-full min-w-[1040px] table-fixed">
                        <thead>
                            
                            <tr class="border-b border-line bg-mist/60">
                                @foreach($kolom as $i => $k)
                                    <th @class([
                                        'py-3 text-admin-overline uppercase text-ink-faint',
                                        $k['lebar'], $k['rata'],
                                        'pl-5 pr-3' => $i === 0,
                                        'px-3'      => $i > 0 && $i < count($kolom) - 1,
                                        'pl-3 pr-5' => $i === count($kolom) - 1,
                                    ])>{{ $k['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($latestInquiries as $inquiry)
                                @php
                                    $sales  = $inquiry->assignedSales;
                                    $produk = $inquiry->product?->translated_name;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">
                                    
                                    <td class="py-4 pl-5 pr-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$inquiry->name" size="sm" />

                                            <div class="min-w-0">
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $inquiry->name }}">{{ $inquiry->name }}</span>
                                                <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                      title="{{ $inquiry->email }}">{{ $inquiry->email }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Perusahaan, dengan negaranya sebagai baris kedua --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate',
                                            'text-admin-strong text-ink' => filled($inquiry->company),
                                            'text-admin-body text-ink-faint' => blank($inquiry->company),
                                        ]) title="{{ $inquiry->company }}">{{ $inquiry->company ?: 'Perorangan' }}</span>

                                        <x-admin.country :code="$inquiry->country_code" size="sm" class="mt-1" />
                                    </td>

                                    {{-- Produk, dengan volume yang diminta di bawahnya --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate',
                                            'text-admin-strong text-ink' => filled($produk),
                                            'text-admin-body text-ink-faint' => blank($produk),
                                        ]) title="{{ $produk }}">{{ $produk ?: 'Tanpa produk tertentu' }}</span>

                                        @if(filled($inquiry->volume))
                                            <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                  title="Volume diminta: {{ $inquiry->volume }}">{{ $inquiry->volume }}</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$inquiry->status" />
                                    </td>

                                    {{-- Ditangani siapa --}}
                                    <td class="px-3 py-4 align-middle">
                                        @if($sales)
                                            <span class="flex items-center gap-2">
                                                <x-admin.avatar :name="$sales->name" size="sm" />
                                                <span class="min-w-0 truncate text-admin-body text-ink-muted"
                                                      title="{{ $sales->name }}">{{ $sales->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-admin-body text-ink-faint" title="Belum ditugaskan">&mdash;</span>
                                        @endif
                                    </td>

                                    {{-- Masuk --}}
                                    <td class="px-3 py-4 align-middle">
                                        <time datetime="{{ $inquiry->created_at->toIso8601String() }}" class="block">
                                            <span class="block text-admin-body tabular-nums text-ink-muted">
                                                {{ $inquiry->created_at->locale('id')->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-admin-caption tabular-nums text-ink-faint">
                                                {{ $inquiry->created_at->format('H:i') }}
                                            </span>
                                        </time>
                                    </td>
                                    
                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <button type="button" x-data
                                                x-on:click="Livewire.dispatch('buka-inquiry', { id: {{ \Illuminate\Support\Js::from($inquiry->id) }} })"
                                                title="Kelola inquiry dari {{ $inquiry->name }}"
                                                aria-label="Kelola inquiry dari {{ $inquiry->name }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                       border border-line bg-canvas text-ink-muted transition-colors
                                                       hover:border-brand hover:bg-brand hover:text-white">
                                            <x-icon.admin name="manage" size="h-4 w-4" />
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <livewire:admin.inquiry-detail />
        @endif
    @endcan

</div>
</x-layouts.app>