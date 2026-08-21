<x-layouts.app>

<div class="mx-auto max-w-[1400px]">

    {{-- ══════════════════════════════════════════════════════════════════
         SAPAAN
         ══════════════════════════════════════════════════════════════════ --}}
    @php
        $zona = \App\Models\Setting::where('key', 'timezone')->value('value') ?: config('app.timezone');
        $kini = now()->timezone($zona);
        [$waktu, $emotikon] = match (true) {
            $kini->hour < 11 => ['Selamat pagi',  '☀️'],
            $kini->hour < 15 => ['Selamat siang', '🌤️'],
            $kini->hour < 18 => ['Selamat sore',  '🌇'],
            default          => ['Selamat malam', '🌙'],
        };
    @endphp

    <div class="mb-8">
        <h1 class="flex flex-wrap items-center gap-x-2.5 font-ui text-[24px] font-bold
                   leading-[1.2] tracking-[-0.02em] text-ink sm:text-[28px]">
            <span>{{ $waktu }}, {{ auth()->user()->name }}</span>

            
            <span aria-hidden="true" class="text-[22px] leading-none sm:text-[26px]">{{ $emotikon }}</span>
        </h1>

        <p class="mt-2 text-[13px] text-ink-muted">
            {{ $kini->locale('id')->translatedFormat('l, j F Y') }}

            @can('view inquiries')
                @if($newInquiries > 0)
                    · <span class="font-semibold text-ink">{{ $newInquiries }} inquiry baru</span> menunggu ditangani.
                @else
                    · Tidak ada inquiry baru yang menunggu.
                @endif
            @endcan
        </p>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         PERINGATAN MASA BERLAKU SERTIFIKASI
         ══════════════════════════════════════════════════════════════════ --}}
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

        
        <div class="mb-8 rounded-corner border border-line bg-canvas" role="alert">

            <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-4">
                <div class="flex min-w-0 items-start gap-3">
                    <span @class([
                        'flex h-8 w-8 shrink-0 items-center justify-center rounded-control',
                        'bg-danger/10 text-danger' => $adaKedaluwarsa,
                        'bg-mist text-ink-muted'   => ! $adaKedaluwarsa,
                    ])>
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <p @class([
                            'text-[14px] font-semibold',
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

                        <p class="mt-1 text-[13px] leading-relaxed text-ink-muted">
                            Sertifikasi yang lewat tanggal berhenti tampil sebagai bukti kelayakan
                            di situs publik, dan perpanjangannya makan waktu berminggu-minggu.
                        </p>
                    </div>
                </div>

                @can('manage certifications')
                    <a href="{{ route('admin.certifications.index') }}"
                       class="shrink-0 text-[13px] font-semibold text-brand underline-offset-4 hover:underline">
                        Kelola sertifikasi
                    </a>
                @endcan
            </div>

            <div class="space-y-2 px-5 pb-5">
                @foreach($perluDiurus as $cert)
                        @php

                            $nama  = $cert->translated_name;
                            $lewat = $cert->expires_at->isPast();

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
                            <div class="min-w-0">
                                <span class="block truncate text-[13px] font-semibold text-ink"
                                      title="{{ $nama ?: $cert->slug }}">{{ $nama ?: $cert->slug }}</span>
                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint">{{ $cert->issuer }}</span>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-[13px] tabular-nums text-ink-muted">
                                    {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                                </span>

                                <span @class([
                                    'inline-flex w-[104px] justify-center rounded-full px-2.5 py-1 text-[11px] font-bold',
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


    {{-- ══════════════════════════════════════════════════════════════════
         RINGKASAN
         ══════════════════════════════════════════════════════════════════ --}}
    @php
        $pengguna = auth()->user();
        $kartu = collect();

        if ($pengguna->can('view inquiries')) {
            $kartu = $kartu->concat([
                ['label' => 'Total inquiry', 'nilai' => $totalInquiries,      'ikon' => 'inquiry',
                 'catatan' => 'Sepanjang waktu',   'rute' => 'admin.inquiries.index', 'sorot' => false],
                ['label' => 'Inquiry baru',  'nilai' => $newInquiries,        'ikon' => 'bell',
                 'catatan' => 'Belum ditangani',   'rute' => 'admin.inquiries.index', 'sorot' => true],
                ['label' => 'Diproses',      'nilai' => $processingInquiries, 'ikon' => 'inquiry',
                 'catatan' => 'Sedang berjalan',   'rute' => 'admin.inquiries.index', 'sorot' => false],
                ['label' => 'Selesai',       'nilai' => $closedInquiries,     'ikon' => 'certification',
                 'catatan' => 'Sudah ditutup',     'rute' => 'admin.inquiries.index', 'sorot' => false],
            ]);
        }

        if ($pengguna->can('manage products')) {
            $kartu = $kartu->concat([
                ['label' => 'Produk terbit', 'nilai' => $totalProducts, 'ikon' => 'product',
                 'catatan' => 'Tampil di katalog', 'rute' => 'admin.products.index', 'sorot' => false],
                ['label' => 'Produk draf',   'nilai' => $draftProducts, 'ikon' => 'product',
                 'catatan' => 'Belum terbit',      'rute' => 'admin.products.index', 'sorot' => false],
            ]);
        }

        if ($pengguna->can('manage news')) {
            $kartu->push(['label' => 'Berita terbit', 'nilai' => $totalNews, 'ikon' => 'news',
                          'catatan' => 'Tampil di situs', 'rute' => 'admin.news.index', 'sorot' => false]);
        }
    @endphp

    @if($kartu->isNotEmpty())

        <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach($kartu as $k)
                
                <a href="{{ route($k['rute']) }}"
                   @class([
                       'card p-5 transition-colors hover:border-line-strong',
                       'border-brand/25 bg-brand-wash hover:border-brand/45' => $k['sorot'],
                   ])>
                    <span class="flex items-start justify-between gap-3">
                        <span class="text-[12px] font-semibold text-ink-muted">{{ $k['label'] }}</span>

                        <span @class([
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-control',
                            'bg-brand text-white'    => $k['sorot'],
                            'bg-mist text-ink-faint' => ! $k['sorot'],
                        ])>
                            <x-icon.admin :name="$k['ikon']" size="h-4 w-4" />
                        </span>
                    </span>

                    
                    <span @class([
                        'mt-4 block text-[30px] font-bold leading-none tabular-nums',
                        'text-brand' => $k['sorot'],
                        'text-ink'   => ! $k['sorot'],
                    ])>{{ number_format($k['nilai']) }}</span>

                    <span class="mt-1.5 block text-[12px] text-ink-faint">{{ $k['catatan'] }}</span>
                </a>
            @endforeach
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════
         INQUIRY TERBARU
         ══════════════════════════════════════════════════════════════════ --}}
    @can('view inquiries')
        @if($latestInquiries->isNotEmpty())
            {{-- Sebutan status, ronanya, dan nama negara tinggal di dalam
                 x-admin.status-pill dan x-admin.country. Halaman inquiry
                 memakai keduanya juga; kalau petanya disalin ke sini, satu
                 status baru cukup untuk membuat kedua halaman berbeda
                 warna tanpa ada yang sadar. --}}
            
            <div class="card mb-8">
                
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                            <x-icon.admin name="inquiry" size="h-4 w-4" />
                        </span>

                        <div>
                            <h2 class="font-ui text-[15px] font-semibold text-ink">Inquiry terbaru</h2>
                            <p class="mt-0.5 text-[12px] text-ink-muted">Permintaan penawaran yang paling baru masuk.</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.inquiries.index') }}"
                       class="text-[13px] font-semibold text-brand underline-offset-4 hover:underline">
                        Lihat semua inquiry
                    </a>
                </div>
                
                <div class="p-5">
                    <div class="overflow-hidden rounded-corner border border-line">
                        <div class="overflow-x-auto">
                    
                    @php
                        /* Susunan kolomnya sengaja sama persis dengan tabel di
                           halaman Inquiry — kolomnya, urutannya, dan cara tiap
                           sel disusun. Dua tabel berisi hal yang sama tapi
                           berbeda urutan memaksa mata belajar dua kali, dan
                           yang satu ini justru pintu masuk ke yang satunya.

                           Bedanya cuma satu: tanpa kolom "Aksi". Di dasbor
                           barisnya memang tidak bisa dikelola, jadi tombolnya
                           akan jadi janji yang tidak ditepati. Lebar yang
                           dibebaskannya dibagi ke kolom lain. */
                        $kolom = [
                            ['label' => 'Pembeli',    'lebar' => 'w-[22%]', 'rata' => 'text-left'],
                            ['label' => 'Perusahaan', 'lebar' => 'w-[22%]', 'rata' => 'text-left'],
                            ['label' => 'Produk',     'lebar' => 'w-[19%]', 'rata' => 'text-left'],
                            ['label' => 'Status',     'lebar' => 'w-[11%]', 'rata' => 'text-left'],
                            ['label' => 'Ditangani',  'lebar' => 'w-[14%]', 'rata' => 'text-left'],
                            ['label' => 'Masuk',      'lebar' => 'w-[12%]', 'rata' => 'text-left'],
                        ];
                    @endphp

                    <table class="w-full min-w-[960px] table-fixed">
                        <thead>
                            
                            <tr class="border-b border-line bg-mist/60">
                                @foreach($kolom as $i => $k)
                                    <th @class([
                                        'py-3 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint',
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
                                    $baru   = $inquiry->status === 'new';
                                    $sales  = $inquiry->assignedSales;
                                    $produk = $inquiry->product?->translated_name;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Pembeli. Garis hijau di tepi kiri menandai yang
                                         belum tersentuh — penanda kedua di samping
                                         pil "Baru". --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $baru,
                                        'border-transparent' => ! $baru,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$inquiry->name" size="sm" />

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $inquiry->name }}">{{ $inquiry->name }}</span>
                                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
                                                      title="{{ $inquiry->email }}">{{ $inquiry->email }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Perusahaan, dengan negaranya sebagai baris kedua --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'font-semibold text-ink' => filled($inquiry->company),
                                            'text-ink-faint'         => blank($inquiry->company),
                                        ]) title="{{ $inquiry->company }}">{{ $inquiry->company ?: 'Perorangan' }}</span>

                                        <x-admin.country :code="$inquiry->country_code" size="sm" class="mt-1" />
                                    </td>

                                    {{-- Produk, dengan volume yang diminta di bawahnya --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'font-semibold text-ink' => filled($produk),
                                            'text-ink-faint'         => blank($produk),
                                        ]) title="{{ $produk }}">{{ $produk ?: 'Tanpa produk tertentu' }}</span>

                                        @if(filled($inquiry->volume))
                                            <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
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
                                                <x-admin.avatar :name="$sales->name" size="sm"
                                                                class="!h-7 !w-7 !text-[11px]" />
                                                <span class="min-w-0 truncate text-[13px] text-ink-muted"
                                                      title="{{ $sales->name }}">{{ $sales->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint" title="Belum ditugaskan">&mdash;</span>
                                        @endif
                                    </td>

                                    {{-- Masuk --}}
                                    <td class="py-4 pl-3 pr-5 align-middle">
                                        <time datetime="{{ $inquiry->created_at->toIso8601String() }}" class="block">
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $inquiry->created_at->locale('id')->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $inquiry->created_at->format('H:i') }}
                                            </span>
                                        </time>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan


    {{-- ══════════════════════════════════════════════════════════════════
         GRAFIK
         ══════════════════════════════════════════════════════════════════ --}}
    @can('view inquiries')
        <div class="mb-8 grid gap-6 lg:grid-cols-5">

            <div class="lg:col-span-3">
        {{-- ══════════════════════════════════════════════════════════════════
             GRAFIK INQUIRY PER BULAN
             ══════════════════════════════════════════════════════════════════ --}}
            @php

                $bulan = collect($chartMonthLabels)->map(function ($l) {
                    try {
                        return \Carbon\Carbon::createFromFormat('M Y', $l);
                    } catch (\Throwable $e) {
                        return null;
                    }
                });

                $labelBulan = $bulan
                    ->map(fn ($t, $i) => $t?->locale('id')->translatedFormat('M Y') ?: $chartMonthLabels[$i])
                    ->all();

                $tahunTerakhir = null;

                $labelSumbu = $bulan->map(function ($t, $i) use (&$tahunTerakhir, $chartMonthLabels) {
                    if (! $t) {
                        return $chartMonthLabels[$i];
                    }

                    $nama = $t->locale('id')->translatedFormat('M');

                    if ($t->year !== $tahunTerakhir) {
                        $tahunTerakhir = $t->year;

                        return $nama . ' ' . $t->year;
                    }

                    return $nama;
                })->all();

                $periode = count($labelBulan)
                    ? reset($labelBulan) . ' – ' . end($labelBulan)
                    : null;
            @endphp
            
            <div class="card flex h-full flex-col">
                <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                            <x-icon.admin name="chart" size="h-4 w-4" />
                        </span>

                        <div>
                            <h2 class="font-ui text-[15px] font-semibold text-ink">Inquiry per bulan</h2>
                            <p class="mt-0.5 text-[12px] text-ink-muted">
                                Pola permintaan penawaran sepanjang dua belas bulan terakhir.
                            </p>
                        </div>
                    </div>

                    @if($periode)
                        <span class="inline-flex items-center gap-2 rounded-full border border-line bg-mist
                                     px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <rect x="2.2" y="3.4" width="11.6" height="10.4" rx="1.6" stroke="currentColor" stroke-width="1.3"/>
                                <path d="M2.2 6.6h11.6M5.6 2.2v2.4M10.4 2.2v2.4" stroke="currentColor"
                                      stroke-width="1.3" stroke-linecap="round"/>
                            </svg>
                            {{ $periode }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-5">
                    @if(count($chartMonthData) > 0)
                        <div class="flex flex-1 flex-col rounded-corner border border-line p-5">
                            <x-chart.area :labels="$labelBulan" :axis-labels="$labelSumbu"
                                          :values="$chartMonthData"
                                          series-label="Inquiry" class="flex-1" />
                        </div>
                    @else
                        <div class="flex flex-1 items-center justify-center rounded-corner border border-line p-5">
                            <p class="text-[13px] text-ink-faint">
                                Belum ada inquiry yang masuk pada rentang ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            </div>

            <div class="lg:col-span-2">
    {{-- ══════════════════════════════════════════════════════════════════
         NEGARA PENGIRIM INQUIRY
         ══════════════════════════════════════════════════════════════════ --}}
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
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="market" size="h-4 w-4" />
                </span>

                <div class="min-w-0">
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Negara pengirim inquiry</h2>
                    <p class="mt-0.5 truncate text-[12px] text-ink-muted">
                        Sepuluh terbanyak sepanjang waktu.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.inquiries.index') }}"
               class="shrink-0 text-[13px] font-semibold text-brand underline-offset-4 hover:underline">
                Lihat semua inquiry
            </a>
        </div>

        @if($peringkat->isNotEmpty())
            
            <div class="flex-1 p-5">
                <div class="h-full overflow-hidden rounded-corner border border-line">
                    <table class="w-full table-fixed text-left">
                        <thead>
                            <tr class="border-b border-line bg-mist/60">
                                <th class="w-[12%] py-2.5 pl-4 pr-2 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">#</th>
                                <th class="w-[45%] px-2 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">Negara</th>
                                <th class="w-[27%] px-2"></th>
                                <th class="w-[16%] py-2.5 pl-2 pr-4 text-right text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">Inquiry</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($peringkat as $i => $n)
                                @php $panjang = round($n['nilai'] / $tertinggiNegara * 100, 2); @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    <td class="py-2.5 pl-4 pr-2 text-[13px] tabular-nums text-ink-faint">{{ $i + 1 }}</td>

                                    <td class="px-2 py-2.5">

                                        <span class="flex items-center gap-2">
                                            <span class="inline-flex shrink-0 items-center rounded-full bg-mist-deep px-2 py-0.5
                                                         text-[10px] font-bold tracking-[0.04em] text-ink-muted">{{ $n['kode'] }}</span>
                                            <span class="min-w-0 truncate text-[13px] font-semibold text-ink"
                                                  title="{{ $n['nama'] }}">{{ $n['nama'] }}</span>
                                        </span>
                                    </td>

                                    <td class="px-2 py-2.5" aria-hidden="true">
                                        <span class="block h-1.5 w-full overflow-hidden rounded-full bg-mist">
                                            <span class="block h-full rounded-full bg-brand/70 transition-colors group-hover:bg-brand"
                                                  style="width: {{ $panjang }}%;"></span>
                                        </span>
                                    </td>

                                    <td class="py-2.5 pl-2 pr-4 text-right text-[13px] font-semibold tabular-nums text-ink">
                                        {{ number_format($n['nilai']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="px-6 py-16 text-center text-[13px] text-ink-faint">
                Belum ada inquiry yang masuk.
            </p>
        @endif
    </div>
            </div>
        </div>
    @endcan

</div>
</x-layouts.app>
