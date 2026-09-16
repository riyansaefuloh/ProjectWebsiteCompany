<div class="mx-auto max-w-[1400px]">

    @php
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $selectedStatus === 'active' ? 'Aktif' : 'Nonaktif',
                'props' => ['selectedStatus'],
            ]);
        }

        $bersihkan = function (array $props) {
            $akhir = array_pop($props);

            return collect($props)->map(fn ($x) => "\$wire.\$set('{$x}', '', false);")->implode(' ')
                 . " \$wire.\$set('{$akhir}', '');";
        };
    @endphp

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-admin-display text-heading">
                Sertifikasi
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
                Bukti kelayakan yang ditampilkan sebagai jaminan di situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah sertifikasi
        </button>
    </div>

    {{-- ══ PERINGATAN MASA BERLAKU — kalimat, bentuk, dan rona barisnya
         disalin apa adanya dari peringatan yang sama di dasbor. ══ --}}
    @php
        $perluDiurus = collect($expiredCerts)->concat($expiringSoonCerts)
            ->unique('id')
            ->sortBy('expires_at')
            ->values();

        $adaKedaluwarsa = collect($expiredCerts)->isNotEmpty();
        $jumlahLewat    = collect($expiredCerts)->count();
        $jumlahSegera   = collect($expiringSoonCerts)->count();
    @endphp

    @if($perluDiurus->isNotEmpty())
        <div class="card mb-6" role="alert">

            {{-- Kepala bergaris bawah, keping 36px berikon 18px — sama persis
                 dengan peringatan yang sama di dasbor. --}}
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
                        {{-- Kalimatnya menyebut jumlah dan keadaannya, bukan
                             jendela waktu kuerinya: "dalam 30 hari" di atas
                             baris berbunyi "11 hari lagi" terbaca seperti dua
                             hal berbeda. --}}
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

                {{-- Dasbor menaruh tautan "Kelola sertifikasi" di sini; di
                     halaman ini tautan itu menunjuk ke halaman yang sedang
                     dibuka, jadi sengaja tidak ikut. --}}
            </div>

            <div class="space-y-2 p-5">
                @foreach($perluDiurus as $cert)
                    @php
                        $namaAlert = $cert->translated_name ?: $cert->slug;
                        $lewatAlert = $cert->expires_at->isPast();
                        $logoAlert = $cert->getFirstMedia('logos');

                        $hariAlert = (int) abs(
                            now()->startOfDay()->diffInDays($cert->expires_at->copy()->startOfDay())
                        );
                    @endphp

                    <div @class([
                        'flex flex-wrap items-center justify-between gap-x-4 gap-y-1
                         rounded-control border px-4 py-3',
                        'border-danger/20 bg-danger/5' => $lewatAlert,
                        'border-line bg-mist'          => ! $lewatAlert,
                    ])>
                        <div class="flex min-w-0 items-center gap-3">
                            {{-- Logo 40px. Yang belum punya logo tetap
                                 mendapat petaknya — berbingkai putus-putus —
                                 supaya nama sertifikasi di seluruh daftar
                                 tetap berawal di garis yang sama. --}}
                            @if($logoAlert)
                                <img src="{{ $logoAlert->getUrl() }}" alt=""
                                     loading="lazy" width="40" height="40"
                                     class="h-10 w-10 shrink-0 rounded-control border border-line
                                            bg-canvas object-contain p-1"
                                     title="Logo {{ $namaAlert }}">
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
                                      title="{{ $namaAlert }}">{{ $namaAlert }}</span>
                                <span class="mt-0.5 block truncate text-admin-caption text-ink-faint">{{ $cert->issuer }}</span>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-admin-body tabular-nums text-ink-muted">
                                {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                            </span>

                            <span @class([
                                'inline-flex w-[104px] justify-center rounded-full px-2.5 py-1 text-admin-caption font-semibold',
                                'bg-danger/15 text-danger'                    => $lewatAlert,
                                'border border-line bg-canvas text-ink-muted' => ! $lewatAlert,
                            ])>
                                {{ $lewatAlert ? 'Lewat ' . $hariAlert . ' hari' : $hariAlert . ' hari lagi' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         PESAN SETELAH TERSIMPAN
         ══════════════════════════════════════════════════════════════════ --}}
    @if(session()->has('message'))
        <div x-data="{ tampil: true }" x-show="tampil" x-collapse
             class="mb-6 flex items-start gap-3 rounded-corner border border-brand/25 bg-brand-wash px-5 py-4"
             role="status">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>

            <p class="min-w-0 flex-1 pt-1 text-admin-strong text-brand-deep">
                {{ session('message') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         TABEL
         ══════════════════════════════════════════════════════════════════ --}}
    {{-- overflow-visible: menu turun penyaring di dalamnya melayang keluar
         dari bingkai kartu, dan .card membawa overflow-hidden yang akan
         memotongnya tepat di garis bawah kartu. --}}
    <div class="card overflow-visible">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                {{-- Keping lambang 36px berlatar hijau muda dengan ikon 18px — sama
                     persis dengan kepala kartu di dasbor, Inquiry, Produk, dan
                     Kategori. --}}
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="certification" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-heading">Daftar sertifikasi</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Urut menurut nomor urutan tampilnya di situs publik.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($certifications->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'sertifikasi' }}
            </span>
        </div>

        {{-- ══ PENYARING — berdiri di DALAM kartu, tepat di atas tabelnya,
             berbingkai sendiri seperti tabelnya. ══ --}}
        <div class="px-5 pt-5">
            <div class="rounded-corner border border-line">

                {{-- Dua kendali berbagi satu baris — pencarian dua pertiga, status
                     sepertiga. Susunan yang sama dengan halaman Kategori. --}}
                <div class="grid gap-4 p-5 lg:grid-cols-3">

                    <div class="relative lg:col-span-2">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                            <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                        </span>

                        <input type="search" wire:model.live="search" id="cari-sertifikasi"
                               aria-label="Cari sertifikasi"
                               placeholder="Cari nama sertifikasi atau penerbitnya…"
                               class="admin-control pl-11 pr-10">

                        <span wire:loading wire:target="search"
                              class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>

                    <x-admin.select model="selectedStatus" :value="$selectedStatus"
                                    label="Saring menurut status" placeholder="Semua status"
                                    :options="[
                                        ['nilai' => 'active',   'label' => 'Aktif'],
                                        ['nilai' => 'inactive', 'label' => 'Nonaktif'],
                                    ]" />
                </div>

                @if($penyaringAktif->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2 rounded-b-corner border-t border-line
                                bg-mist/60 px-5 py-3">
                        <span class="mr-1 inline-flex shrink-0 items-center gap-1.5 text-admin-overline
                                     uppercase text-ink-faint">
                            <x-icon.admin name="filter" size="h-3.5 w-3.5" />
                            Disaring
                        </span>

                        @foreach($penyaringAktif as $f)
                            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-line
                                         bg-canvas py-1 pl-3 pr-1.5 text-admin-label text-ink-muted">
                                <span class="min-w-0 truncate">
                                    {{ $f['label'] }}: <span class="font-semibold text-ink">{{ $f['nilai'] }}</span>
                                </span>

                                <button type="button" x-on:click="{{ $bersihkan($f['props']) }}"
                                        aria-label="Hapus penyaring {{ $f['label'] }}"
                                        class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full
                                               text-ink-faint transition-colors hover:bg-mist-deep hover:text-ink">
                                    <x-icon.admin name="close" size="h-3 w-3" />
                                </button>
                            </span>
                        @endforeach

                        @if($penyaringAktif->count() > 1)
                            <button type="button"
                                    x-on:click="{{ $bersihkan(['search', 'selectedStatus']) }}"
                                    class="ml-auto shrink-0 text-admin-label font-semibold text-brand underline-offset-4 hover:underline">
                                Hapus semua
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        /* Susunan kolomnya tetap seperti yang sudah ada — tujuh
                           kolom, urutan sama — hanya diterjemahkan dan ditata. */
                        /* Satu sel untuk logo, nama, nomor, dan berkas PDF-nya —
                           pola yang sama dengan halaman Produk dan Kategori.
                           Keempatnya menunjuk benda yang sama; memisahkannya
                           jadi dua kolom memaksa mata melompat untuk merangkai
                           satu keterangan. */
                        /* Tanggal terbit tidak ikut ditabelkan. Yang menuntut
                           tindakan adalah tanggal kedaluwarsanya; tanggal
                           terbitnya cuma catatan arsip yang bisa dilihat saat
                           sertifikasinya dibuka. Isiannya tetap ada di modal. */
                        $kolom = [
                            ['label' => 'Sertifikasi',  'lebar' => 'w-[34%]', 'rata' => 'text-left'],
                            ['label' => 'Penerbit',     'lebar' => 'w-[21%]', 'rata' => 'text-left'],
                            ['label' => 'Kedaluwarsa',  'lebar' => 'w-[15%]', 'rata' => 'text-left'],
                            ['label' => 'Urutan',       'lebar' => 'w-[9%]',  'rata' => 'text-left'],
                            ['label' => 'Status',       'lebar' => 'w-[11%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',         'lebar' => 'w-[10%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[1000px] table-fixed">
                        @if($certifications->isNotEmpty())
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
                        @endif

                        @php
                            /* Nomor urutan yang dipakai lebih dari satu sertifikasi.
                               Dihitung sekali di sini, bukan di dalam perulangan:
                               menghitungnya per baris berarti menyapu seluruh
                               koleksinya sebanyak jumlah barisnya. */
                            $urutanGanda = collect($certifications->items())
                                ->countBy('sort_order')
                                ->filter(fn ($n) => $n > 1)
                                ->keys();
                        @endphp

                        <tbody>
                            @forelse($certifications as $cert)
                                @php
                                    $nama = $cert->translated_name ?: $cert->slug;
                                    $urutanKembar = $urutanGanda->contains($cert->sort_order);

                                    /* getFirstMedia(), bukan getFirstMediaUrl(): yang
                                       kedua mengembalikan untai kosong saat tidak ada
                                       berkasnya, dan untai kosong di src membuat
                                       peramban memuat ulang HALAMAN ini sebagai
                                       gambar. */
                                    $logo = $cert->getFirstMedia('logos');
                                    $pdf  = $cert->getFirstMedia('pdfs');

                                    $lewat  = $cert->expires_at && $cert->expires_at->isPast();
                                    $segera = $cert->expires_at && ! $lewat
                                              && $cert->expires_at->lte(now()->addDays(30));

                                    $hari = $cert->expires_at
                                        ? (int) abs(now()->startOfDay()
                                            ->diffInDays($cert->expires_at->copy()->startOfDay()))
                                        : null;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Garis tepi kiri dihapus — keadaan
                                         kedaluwarsa sudah terbaca dari kolom
                                         Berlaku sampai. --}}
                                    <td class="py-4 pl-5 pr-3 align-middle">
                                        <div class="flex items-center gap-3">

                                            {{-- object-contain, bukan
                                                 object-cover: lambang lembaga
                                                 sertifikasi bentuknya
                                                 macam-macam, dan memotongnya
                                                 sampai penuh kotak membuang
                                                 bagian yang justru
                                                 mengenalinya. --}}
                                            @if($logo)
                                                <img src="{{ $logo->getUrl() }}" alt=""
                                                     loading="lazy" width="40" height="40"
                                                     class="h-10 w-10 shrink-0 rounded-control border border-line
                                                            bg-canvas object-contain p-1"
                                                     title="Logo {{ $nama }}">
                                            @else
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                             rounded-control border border-dashed border-line-strong
                                                             bg-mist text-ink-faint"
                                                      title="Belum ada logo">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>

                                                {{-- Baris kedua: nomor
                                                     sertifikat, berangka
                                                     lebar seragam — yang
                                                     dilakukan orang dengan
                                                     nomor ini adalah
                                                     mencocokkannya karakter
                                                     per karakter. --}}
                                                <span class="mt-0.5 flex items-center gap-2">
                                                    <span class="min-w-0 truncate text-admin-caption tabular-nums text-ink-faint"
                                                          title="Nomor sertifikat: {{ $cert->certificate_number }}">{{ $cert->certificate_number ?: 'Tanpa nomor' }}</span>

                                                    @if($pdf)
                                                        <a href="{{ $pdf->getUrl() }}" target="_blank" rel="noopener"
                                                           title="Buka berkas PDF {{ $nama }}"
                                                           aria-label="Buka berkas PDF {{ $nama }}"
                                                           class="inline-flex shrink-0 items-center gap-1 rounded-full
                                                                  border border-line bg-canvas px-2.5 py-1 text-admin-caption
                                                                  font-semibold text-ink-muted transition-colors
                                                                  hover:border-brand hover:text-brand">
                                                            <x-icon.admin name="pdf" size="h-3 w-3" />
                                                            PDF
                                                        </a>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="block truncate text-admin-strong text-ink"
                                              title="{{ $cert->issuer }}">{{ $cert->issuer }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($cert->expires_at)
                                            <time datetime="{{ $cert->expires_at->toDateString() }}"
                                                  @class([
                                                      'block text-admin-body tabular-nums',
                                                      'font-semibold text-danger' => $lewat,
                                                      'text-ink-muted'            => ! $lewat,
                                                  ])>
                                                {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                                            </time>

                                            @if($lewat || $segera)
                                                <span @class([
                                                    'mt-1 inline-flex items-center rounded-full px-2.5 py-1
                                                     text-admin-caption font-semibold',
                                                    'bg-danger/15 text-danger'                     => $lewat,
                                                    'border border-line bg-canvas text-ink-muted'  => ! $lewat,
                                                ])>{{ $lewat ? 'Lewat ' . $hari . ' hari' : $hari . ' hari lagi' }}</span>
                                            @endif
                                        @else
                                            <span class="text-admin-body text-ink-faint">Tanpa masa berlaku</span>
                                        @endif
                                    </td>

                                    {{-- Angka urutan yang kembar diberi
                                         tanda: dua sertifikasi berangka sama
                                         akan berurutan seadanya di situs
                                         publik, dan tanpa tanda tidak ada
                                         yang memberi tahu. --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'inline-flex h-7 min-w-7 items-center justify-center rounded-control
                                             px-2 text-admin-strong tabular-nums',
                                            'bg-mist text-ink-muted'                            => ! $urutanKembar,
                                            'border border-danger/25 bg-danger/5 text-danger'   => $urutanKembar,
                                        ])
                                              title="{{ $urutanKembar
                                                        ? 'Urutan ' . $cert->sort_order . ' dipakai lebih dari satu sertifikasi'
                                                        : 'Urutan tampil: ' . $cert->sort_order }}">{{ $cert->sort_order }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$cert->status" />
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $cert->id }}')"
                                                    title="Ubah {{ $nama }}"
                                                    aria-label="Ubah {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

<x-admin.confirm-delete metode="delete"
                                                                    :id="$cert->id"
                                                                    :nama="$nama"
                                                                    judul="Hapus sertifikasi?"
                                                                    tombol="Ya, hapus sertifikasi">
                                                Logo dan berkas PDF-nya ikut terhapus, dan produk yang menautkannya kehilangan bukti kelayakan ini.
                                            </x-admin.confirm-delete>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'certification'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-admin-title text-heading">
                                                Tidak ada sertifikasi yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                statusnya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'selectedStatus']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-admin-title text-heading">
                                                Belum ada sertifikasi
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Sertifikasi yang ditambahkan di sini tampil sebagai bukti
                                                kelayakan di situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah sertifikasi pertama
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="border-t border-line px-6 py-4">
            {{ $certifications->links('vendor.pagination.admin', ['satuan' => 'sertifikasi']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH SERTIFIKASI
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-sertifikasi">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[1000px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-5">
                        <div class="flex min-w-0 items-center gap-3.5">
                            {{-- Keping 52px, setinggi blok dua baris di
                                 sebelahnya (judul 30px + jarak 6 + keterangan
                                 17). --}}
                            <span class="flex h-13 w-13 shrink-0 items-center justify-center rounded-corner
                                         bg-brand-wash text-brand">
                                <x-icon.admin name="certification" size="h-6 w-6" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-sertifikasi"
                                    class="truncate text-admin-display text-heading">
                                    {{ $editingId ? 'Ubah sertifikasi' : 'Tambah sertifikasi' }}
                                </h2>
                                <p class="mt-1.5 text-admin-label text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi,
                                    termasuk nama di kedua bahasa.
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="$set('showModal', false)"
                                aria-label="Tutup"
                                class="-mr-1 shrink-0 rounded-control p-1.5 text-ink-faint
                                       transition-colors hover:bg-mist hover:text-ink">
                            <x-icon.admin name="close" size="h-4 w-4" />
                        </button>
                    </div>

                    {{-- ── Dua kolom ───────────────────────────────────── --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                {{-- Sakelar bahasa dan tombol Terjemahkan duduk di KEPALA KARTU,
                                     sebaris dengan judulnya — susunan yang sama dengan modal
                                     Produk, Kategori, Pasar Ekspor, Berita, dan Halaman.
                                
                                     Sebelumnya keduanya berdiri di dalam badan kartu, dan karena
                                     barisnya selebar kartu, tombolnya terlempar 201px dari
                                     sakelarnya. Di modal lain jaraknya 8px. --}}
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="certification" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-heading">Informasi sertifikasi</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Nama, lembaga penerbit, dan nomor.</p>
                                        </div>
                                    </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2">
                                    <div class="inline-flex shrink-0 items-center gap-0.5 rounded-full
                                                border border-line bg-mist p-0.5"
                                         role="group" aria-label="Bahasa yang sedang disunting">
                                        @foreach(['id' => 'Indonesia', 'en' => 'English'] as $kode => $sebutan)
                                            @php
                                                /* Titik penanda: bahasa ini masih kosong
                                                   sementara bahasa satunya sudah diisi.
                                                   Akibatnya tidak kentara dari panel —
                                                   kartunya akan menampilkan teks bahasa
                                                   satunya. */
                                                $lain    = $kode === 'en' ? 'id' : 'en';
                                                $isiIni  = filled(trim($kode === 'en' ? $name_en : $name_id));
                                                $isiLain = filled(trim($lain === 'en' ? $name_en : $name_id));
                                                $timpang = ! $isiIni && $isiLain;
                                            @endphp

                                            <button type="button" wire:click="$set('activeTab', '{{ $kode }}')"
                                                    aria-pressed="{{ $activeTab === $kode ? 'true' : 'false' }}"
                                                    @class([
                                                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1
                                                         text-admin-label font-semibold transition-colors
                                                         focus-visible:outline-none focus-visible:ring-2
                                                         focus-visible:ring-brand/30',
                                                        'bg-canvas text-brand shadow-[0_1px_2px_rgba(26,29,27,0.10)]'
                                                            => $activeTab === $kode,
                                                        'text-ink-muted hover:text-ink' => $activeTab !== $kode,
                                                    ])>
                                                {{ $sebutan }}
                                                @if($timpang)
                                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-status-new"
                                                          title="Belum diisi — kartu berbahasa ini akan memakai teks bahasa satunya"
                                                          aria-label="Belum diisi"></span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>

                                    <button type="button" wire:click="autoTranslate"
                                            wire:loading.attr="disabled" wire:target="autoTranslate"
                                            title="Salin isian Indonesia ke English, lalu terjemahkan"
                                            class="admin-btn admin-btn-quiet shrink-0 !py-1.5 disabled:opacity-60">
                                        <svg wire:loading wire:target="autoTranslate"
                                             class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>

                                        <x-icon.admin name="send" size="h-3.5 w-3.5" class="shrink-0 text-brand"
                                                      wire:loading.remove wire:target="autoTranslate" />

                                        Terjemahkan
                                    </button>
                                </div>
                                </div>

                                <div class="p-5">

                                <div class="space-y-4">

                                    {{-- Dua bahasa BERDAMPINGAN, bukan di
                                         balik sakelar seperti modal Produk
                                         dan Kategori: di sini sakelarnya
                                         hanya akan mengatur satu isian, dan
                                         sakelar untuk satu isian cuma
                                         menambah langkah. --}}
                                    {{-- Sakelar bahasa DI SAMPING tombol terjemah, dan
                                         kolomnya bertukar di tempat yang sama — pola
                                         yang sama dengan modal Halaman.

                                         Yang lama menaruh kolom Indonesia dan Inggris
                                         berdampingan: dua bahasa harus dibaca sekaligus
                                         padahal yang dikerjakan satu, dan tiap kolom
                                         cuma dapat separuh lebar — mahal untuk
                                         keterangan sepanjang 300 karakter.

                                         Sakelarnya berdiri di bingkai sendiri, terpisah
                                         dari tombol terjemah: bingkai berlekuk begitu
                                         berjanji "pilih salah satu", dan tombol tindakan
                                         di dalamnya mengingkari janji itu. --}}
                                    <div>

                                        {{-- Pesan gagal-terjemah, tepat di bawah sakelar yang
                                             dilayaninya — bukan di puncak jendela dan bukan
                                             lewat flash. --}}
                                        @if($galatTerjemah)
                                            <p class="mt-2 flex items-start gap-1.5 text-admin-caption text-danger" role="alert">
                                                <svg class="mt-px h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                    <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                                    <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                                    <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                                                </svg>
                                                {{ $galatTerjemah }}
                                            </p>
                                        @endif

                                        <div class="mt-4">
                                            <div>
                                                <label for="sertif-nama" class="block text-admin-label text-ink-faint">
                                                    Nama sertifikasi <span class="text-brand">*</span>
                                                </label>

                                                @foreach(['id', 'en'] as $bahasa)
                                                    <div @class(['mt-2', 'hidden' => $activeTab !== $bahasa])>
                                                        <input type="text" wire:model="name_{{ $bahasa }}"
                                                               id="{{ $bahasa === 'id' ? 'sertif-nama' : 'sertif-nama-en' }}"
                                                               aria-label="Nama sertifikasi dalam bahasa {{ $bahasa === 'id' ? 'Indonesia' : 'Inggris' }}"
                                                               placeholder="Nama sertifikasi"
                                                               class="admin-control">

                                                        @error('name_' . $bahasa)
                                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="sertif-issuer" class="block text-admin-label text-ink-faint">
                                            Lembaga penerbit <span class="text-brand">*</span>
                                        </label>

                                        <input type="text" wire:model="issuer" id="sertif-issuer"
                                               placeholder="mis. BPJPH — Kementerian Agama RI"
                                               class="admin-control mt-2">

                                        @error('issuer')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-nomor" class="block text-admin-label text-ink-faint">
                                            Nomor sertifikat
                                        </label>

                                        {{-- tabular-nums: yang dilakukan orang dengan nomor
                                             ini adalah mencocokkannya karakter demi
                                             karakter dengan dokumen aslinya. --}}
                                        <input type="text" wire:model="certificate_number" id="sertif-nomor"
                                               placeholder="mis. ID31110001234560124"
                                               class="admin-control mt-2 tabular-nums">

                                        @error('certificate_number')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: masa berlaku ──────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="chart" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-heading">Masa berlaku</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Tanggal terbit dan kedaluwarsa sertifikat.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                <div class="space-y-4">
                                    <div>
                                        <label for="sertif-terbit" class="block text-admin-label text-ink-faint">
                                            Tanggal terbit
                                        </label>

                                        <input type="date" wire:model="issued_at" id="sertif-terbit"
                                               max="{{ $expires_at ?: '' }}" class="admin-control mt-2">

                                        @error('issued_at')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-kedaluwarsa" class="block text-admin-label text-ink-faint">
                                            Tanggal kedaluwarsa
                                        </label>

                                        {{-- min/max saling mengunci,
                                             mencerminkan
                                             after_or_equal:issued_at di
                                             komponennya — tanggal yang
                                             mustahil tidak pernah sempat
                                             terkirim. --}}
                                        <input type="date" wire:model="expires_at" id="sertif-kedaluwarsa"
                                               min="{{ $issued_at ?: '' }}" class="admin-control mt-2">

                                        @error('expires_at')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <p class="admin-hint flex items-start gap-1.5">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M8 7.4v3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            <circle cx="8" cy="5.2" r="0.75" fill="currentColor"/>
                                        </svg>
                                        Dikosongkan berarti tanpa masa berlaku. Tanggal yang sudah lewat
                                        membuat sertifikasinya berhenti tampil di situs publik.
                                    </p>
                                </div>
                            </div>
                            </section>

                            {{-- ── Kartu: berkas ────────────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-heading">Berkas</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Logo lembaga dan dokumen sertifikat.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                {{-- ── Logo ─────────────────────────────── --}}
                                <span class="block text-admin-label text-ink-faint">Logo lembaga</span>

                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    @if($editingId && filled($existingLogoUrl))
                                        <div class="group relative aspect-square overflow-hidden
                                                    rounded-control border border-line">
                                            {{-- object-contain: lambang lembaga berbentuk
                                                 macam-macam, dan memotongnya sampai penuh
                                                 kotak membuang bagian yang mengenalinya. --}}
                                            <img src="{{ $existingLogoUrl }}" alt=""
                                                 class="block h-full w-full bg-canvas object-contain p-2">

                                            <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                        from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                        group-hover:opacity-100 group-focus-within:opacity-100">
                                                <x-admin.confirm-delete metode="deleteLogo"
                                                                        label="Hapus logo"
                                                                        judul="Hapus logo sertifikasi?"
                                                                        tombol="Ya, hapus logo"
                                                                        ikon="h-3.5 w-3.5"
                                                                        kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                               justify-center rounded-control bg-white/90 text-danger
                                                                               transition-colors hover:bg-white">
                                                    Berkasnya ikut terhapus dari penyimpanan, dan sertifikasi ini kembali tampil tanpa logo di situs publik.
                                                </x-admin.confirm-delete>
                                            </div>
                                        </div>
                                    @endif

                                    @if($logoFile)
                                        @php
                                            try {
                                                $pratinjauLogo = $logoFile->temporaryUrl();
                                            } catch (\Throwable $e) {
                                                $pratinjauLogo = null;
                                            }
                                        @endphp

                                        <div class="relative aspect-square overflow-hidden rounded-control
                                                    border border-dashed border-brand/50 bg-brand-wash">
                                            @if($pratinjauLogo)
                                                <img src="{{ $pratinjauLogo }}" alt=""
                                                     class="block h-full w-full object-contain p-2">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center px-3
                                                             text-center text-admin-caption text-ink-muted">
                                                    {{ $logoFile->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-admin-caption font-semibold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <x-admin.upload-tile model="logoFile"
                                                         id="sertif-logo"
                                                         judul="{{ filled($existingLogoUrl) ? 'Ganti logo' : 'Tambah logo' }}"
                                                         label="{{ filled($existingLogoUrl) ? 'Ganti logo' : 'Tambah logo' }}" />
                                </div>

                                <p class="admin-hint">
                                    Satu gambar saja, maksimal 2 MB.
                                </p>

                                @error('logoFile')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror

                                {{-- ── Dokumen PDF ──────────────────────── --}}
                                <span class="mt-5 block text-admin-label text-ink-faint">Dokumen resmi</span>

                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    @if($editingId && filled($existingPdfUrl))
                                        <div class="group relative flex aspect-square flex-col items-center
                                                    justify-center gap-1.5 overflow-hidden rounded-control
                                                    border border-line bg-mist/40 text-ink-muted">
                                            <x-icon.admin name="pdf" size="h-7 w-7" />

                                            <a href="{{ $existingPdfUrl }}" target="_blank" rel="noopener"
                                               class="text-admin-caption font-semibold text-brand underline-offset-4 hover:underline">
                                                Buka berkas
                                            </a>

                                            <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                        from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                        group-hover:opacity-100 group-focus-within:opacity-100">
                                                <x-admin.confirm-delete metode="deletePdf"
                                                                        label="Hapus dokumen PDF"
                                                                        judul="Hapus dokumen PDF?"
                                                                        tombol="Ya, hapus dokumen"
                                                                        ikon="h-3.5 w-3.5"
                                                                        kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                               justify-center rounded-control bg-white/90 text-danger
                                                                               transition-colors hover:bg-white">
                                                    Berkasnya ikut terhapus dari penyimpanan, dan tombol unduh sertifikat ini hilang dari situs publik.
                                                </x-admin.confirm-delete>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Berkas PDF tidak bisa dipratinjau sebagai gambar,
                                         jadi yang ditampilkan namanya. --}}
                                    @if($pdfFile)
                                        <div class="relative flex aspect-square flex-col items-center justify-center
                                                    gap-1.5 overflow-hidden rounded-control border border-dashed
                                                    border-brand/50 bg-brand-wash px-3 text-center text-ink-muted">
                                            <x-icon.admin name="pdf" size="h-7 w-7" />

                                            <span class="line-clamp-2 text-admin-caption">
                                                {{ $pdfFile->getClientOriginalName() }}
                                            </span>

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-admin-caption font-semibold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <x-admin.upload-tile model="pdfFile"
                                                         id="sertif-pdf"
                                                         accept="application/pdf"
                                                         judul="{{ filled($existingPdfUrl) ? 'Ganti dokumen PDF' : 'Tambah dokumen PDF' }}"
                                                         label="{{ filled($existingPdfUrl) ? 'Ganti dokumen' : 'Tambah dokumen' }}" />
                                </div>

                                <p class="admin-hint">
                                    Hanya berkas PDF, maksimal 5 MB.
                                </p>

                                @error('pdfFile')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="manage" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-heading">Penerbitan</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Status tayang dan urutan sertifikasi di situs publik.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">Status</label>

                                        {{-- :nullable="false" — sertifikasi selalu berada
                                             di salah satu dari dua keadaan ini. --}}
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status sertifikasi" placeholder="Aktif"
                                                        :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'active',   'label' => 'Aktif'],
                                                            ['nilai' => 'inactive', 'label' => 'Nonaktif'],
                                                        ]" />
                                        
                                        @error('status')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-urutan" class="block text-admin-label text-ink-faint">
                                            Urutan tampil
                                        </label>

                                        <input type="number" wire:model="sort_order" id="sertif-urutan"
                                               min="0" step="1" class="admin-control mt-2">
                                        
                                        <p class="admin-hint">
                                            Angka kecil tampil lebih dulu di situs publik.
                                        </p>

                                        @error('sort_order')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            </section>
                        </div>
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="admin-btn admin-btn-quiet">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="save"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan sertifikasi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
