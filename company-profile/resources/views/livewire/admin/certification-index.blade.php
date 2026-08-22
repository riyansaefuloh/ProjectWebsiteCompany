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
            <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                Sertifikasi
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
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


    {{-- ══════════════════════════════════════════════════════════════════
         PERINGATAN MASA BERLAKU

         Disalin apa adanya dari peringatan yang sama di dasbor — kalimat,
         bentuk, dan rona barisnya. Dua tempat yang menyampaikan hal yang sama
         tapi berbeda rupa membuat orang mengira keduanya hal yang berbeda.
         ══════════════════════════════════════════════════════════════════ --}}
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
        <div class="mb-6 rounded-corner border border-line bg-canvas" role="alert">

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
                        {{-- Kalimatnya menyebut jumlah dan keadaannya, bukan
                             jendela waktu kuerinya. "Akan kedaluwarsa dalam 30
                             hari" sementara barisnya berbunyi "11 hari lagi"
                             membuat pembacanya mengira ada dua hitungan yang
                             berbeda. --}}
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
            </div>

            <div class="space-y-2 px-5 pb-5">
                @foreach($perluDiurus as $cert)
                    @php
                        $namaAlert = $cert->translated_name ?: $cert->slug;
                        $lewatAlert = $cert->expires_at->isPast();

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
                        <div class="min-w-0">
                            <span class="block truncate text-[13px] font-semibold text-ink"
                                  title="{{ $namaAlert }}">{{ $namaAlert }}</span>
                            <span class="mt-0.5 block truncate text-[12px] text-ink-faint">{{ $cert->issuer }}</span>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-[13px] tabular-nums text-ink-muted">
                                {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                            </span>

                            <span @class([
                                'inline-flex w-[104px] justify-center rounded-full px-2.5 py-1 text-[11px] font-bold',
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

            <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-brand-deep">
                {{ session('message') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════
         PENYARING
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="card mb-6 overflow-visible">

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
                <span class="mr-1 inline-flex shrink-0 items-center gap-1.5 text-[11px] font-bold
                             uppercase tracking-[0.08em] text-ink-faint">
                    <x-icon.admin name="filter" size="h-3.5 w-3.5" />
                    Disaring
                </span>

                @foreach($penyaringAktif as $f)
                    <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-line
                                 bg-canvas py-1 pl-3 pr-1.5 text-[12px] text-ink-muted">
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
                            class="ml-auto shrink-0 text-[12px] font-semibold text-brand underline-offset-4 hover:underline">
                        Hapus semua
                    </button>
                @endif
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         TABEL
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="card">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="certification" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar sertifikasi</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut menurut nomor urutan tampilnya di situs publik.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($certifications->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'sertifikasi' }}
            </span>
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
                                            'py-3 text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint',
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

                                    {{-- Sertifikasi: logo, nama, nomor, dan berkas
                                         PDF-nya dalam satu sel.

                                         Garis merah di tepi kiri menandai yang sudah
                                         lewat tanggal — penanda kedua di samping lencana
                                         harinya, supaya baris yang bermasalah terlihat
                                         dari ujung mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-danger'      => $lewat,
                                        'border-transparent' => ! $lewat,
                                    ])>
                                        <div class="flex items-center gap-3">

                                            {{-- Logonya object-contain, bukan object-cover:
                                                 lambang lembaga sertifikasi punya bentuk
                                                 macam-macam, dan memotongnya sampai penuh
                                                 kotak membuang justru bagian yang
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
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>

                                                {{-- Baris kedua: nomor sertifikatnya, dan
                                                     keping PDF di sebelahnya kalau berkasnya
                                                     ada. Nomornya berangka lebar seragam —
                                                     yang dilakukan orang dengan nomor ini
                                                     adalah mencocokkannya karakter demi
                                                     karakter. --}}
                                                <span class="mt-0.5 flex items-center gap-2">
                                                    <span class="min-w-0 truncate text-[12px] tabular-nums text-ink-faint"
                                                          title="Nomor sertifikat: {{ $cert->certificate_number }}">{{ $cert->certificate_number ?: 'Tanpa nomor' }}</span>

                                                    @if($pdf)
                                                        <a href="{{ $pdf->getUrl() }}" target="_blank" rel="noopener"
                                                           title="Buka berkas PDF {{ $nama }}"
                                                           aria-label="Buka berkas PDF {{ $nama }}"
                                                           class="inline-flex shrink-0 items-center gap-1 rounded-full
                                                                  border border-line bg-canvas px-2 py-0.5 text-[10px]
                                                                  font-bold text-ink-muted transition-colors
                                                                  hover:border-brand hover:text-brand">
                                                            <x-icon.admin name="pdf" size="h-2.5 w-2.5" />
                                                            PDF
                                                        </a>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="block truncate text-[13px] font-semibold text-ink"
                                              title="{{ $cert->issuer }}">{{ $cert->issuer }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($cert->expires_at)
                                            <time datetime="{{ $cert->expires_at->toDateString() }}"
                                                  @class([
                                                      'block text-[13px] tabular-nums',
                                                      'font-semibold text-danger' => $lewat,
                                                      'text-ink-muted'            => ! $lewat,
                                                  ])>
                                                {{ $cert->expires_at->locale('id')->translatedFormat('d M Y') }}
                                            </time>

                                            @if($lewat || $segera)
                                                <span @class([
                                                    'mt-1 inline-flex items-center rounded-full px-2 py-0.5
                                                     text-[10px] font-bold',
                                                    'bg-danger/15 text-danger'                     => $lewat,
                                                    'border border-line bg-canvas text-ink-muted'  => ! $lewat,
                                                ])>{{ $lewat ? 'Lewat ' . $hari . ' hari' : $hari . ' hari lagi' }}</span>
                                            @endif
                                        @else
                                            <span class="text-[13px] text-ink-faint">Tanpa masa berlaku</span>
                                        @endif
                                    </td>

                                    {{-- Urutan tampil. Angka yang kembar diberi tanda:
                                         dua sertifikasi berangka sama akan berurutan
                                         seadanya di situs publik, dan tidak ada yang
                                         memberi tahu kalau tandanya tidak ada. --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'inline-flex h-7 min-w-7 items-center justify-center rounded-control
                                             px-2 text-[13px] font-semibold tabular-nums',
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

                                            <button type="button" wire:click="delete('{{ $cert->id }}')"
                                                    wire:confirm="Hapus sertifikasi &quot;{{ $nama }}&quot;? Logo dan berkas PDF-nya ikut terhapus, dan produk yang menautkannya kehilangan bukti kelayakan ini."
                                                    title="Hapus {{ $nama }}"
                                                    aria-label="Hapus {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-danger hover:bg-danger hover:text-white">
                                                <x-icon.admin name="trash" size="h-4 w-4" />
                                            </button>
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
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada sertifikasi yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                statusnya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'selectedStatus']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada sertifikasi
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
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

            <div class="relative flex max-h-[90vh] w-full max-w-[900px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="certification" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-sertifikasi"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah sertifikasi' : 'Tambah sertifikasi' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
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

                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Informasi sertifikasi
                                </h3>

                                <div class="space-y-4">

                                    {{-- Nama dalam dua bahasa, berdampingan — bukan di
                                         balik sakelar bahasa seperti modal Produk dan
                                         Kategori.

                                         Alasannya: di sana sakelarnya mengatur DUA isian
                                         sekaligus (nama dan deskripsi) yang masing-masing
                                         panjang, jadi menyembunyikan salah satunya
                                         menghemat banyak. Sertifikasi cuma
                                         menerjemahkan namanya — satu kolom pendek — dan
                                         sakelar untuk satu kolom justru menyembunyikan
                                         separuh pekerjaan tanpa menghemat apa pun. --}}
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <label class="block text-[12px] font-semibold text-ink-faint">
                                                Nama sertifikasi <span class="text-brand">*</span>
                                            </label>
                                            <button type="button" wire:click="autoTranslate" wire:loading.attr="disabled" wire:target="autoTranslate"
                                                title="Terjemahkan ID ke EN otomatis"
                                                class="inline-flex items-center gap-1.5 rounded-[5px] px-2 py-1 text-[11px] font-semibold bg-brand/10 text-brand hover:bg-brand/20 transition-colors">
                                                <span wire:loading.remove wire:target="autoTranslate">🌐 Auto EN</span>
                                                <span wire:loading wire:target="autoTranslate">⏳ ...</span>
                                            </button>
                                        </div>

                                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <div class="relative">
                                                    <span class="pointer-events-none absolute left-3.5 top-1/2
                                                                 -translate-y-1/2 text-[10px] font-bold
                                                                 uppercase tracking-[0.06em] text-ink-faint">EN</span>

                                                    <input type="text" wire:model="name_en"
                                                           aria-label="Nama sertifikasi dalam bahasa Inggris"
                                                           placeholder="Nama sertifikasi"
                                                           class="admin-control pl-11">
                                                </div>

                                                @error('name_en')
                                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <div class="relative">
                                                    <span class="pointer-events-none absolute left-3.5 top-1/2
                                                                 -translate-y-1/2 text-[10px] font-bold
                                                                 uppercase tracking-[0.06em] text-ink-faint">ID</span>

                                                    <input type="text" wire:model="name_id"
                                                           aria-label="Nama sertifikasi dalam bahasa Indonesia"
                                                           placeholder="Nama sertifikasi"
                                                           class="admin-control pl-11">
                                                </div>

                                                @error('name_id')
                                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="sertif-issuer" class="block text-[12px] font-semibold text-ink-faint">
                                            Lembaga penerbit <span class="text-brand">*</span>
                                        </label>

                                        <input type="text" wire:model="issuer" id="sertif-issuer"
                                               placeholder="mis. BPJPH — Kementerian Agama RI"
                                               class="admin-control mt-2">

                                        @error('issuer')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-nomor" class="block text-[12px] font-semibold text-ink-faint">
                                            Nomor sertifikat
                                        </label>

                                        {{-- tabular-nums: yang dilakukan orang dengan nomor
                                             ini adalah mencocokkannya karakter demi
                                             karakter dengan dokumen aslinya. --}}
                                        <input type="text" wire:model="certificate_number" id="sertif-nomor"
                                               placeholder="mis. ID31110001234560124"
                                               class="admin-control mt-2 tabular-nums">

                                        @error('certificate_number')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: masa berlaku ──────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Masa berlaku
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="sertif-terbit" class="block text-[12px] font-semibold text-ink-faint">
                                            Tanggal terbit
                                        </label>

                                        <input type="date" wire:model="issued_at" id="sertif-terbit"
                                               max="{{ $expires_at ?: '' }}" class="admin-control mt-2">

                                        @error('issued_at')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-kedaluwarsa" class="block text-[12px] font-semibold text-ink-faint">
                                            Tanggal kedaluwarsa
                                        </label>

                                        {{-- min/max saling mengunci, mencerminkan aturan
                                             after_or_equal:issued_at di komponennya —
                                             tanggal yang mustahil tidak pernah sempat
                                             terkirim, jadi galatnya tidak perlu muncul. --}}
                                        <input type="date" wire:model="expires_at" id="sertif-kedaluwarsa"
                                               min="{{ $issued_at ?: '' }}" class="admin-control mt-2">

                                        @error('expires_at')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <p class="flex items-start gap-1.5 text-[12px] leading-relaxed text-ink-faint">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M8 7.4v3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            <circle cx="8" cy="5.2" r="0.75" fill="currentColor"/>
                                        </svg>
                                        Dikosongkan berarti tanpa masa berlaku. Yang lewat tanggal
                                        berhenti tampil di situs publik.
                                    </p>
                                </div>
                            </section>

                            {{-- ── Kartu: berkas ────────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Berkas
                                </h3>

                                {{-- ── Logo ─────────────────────────────── --}}
                                <span class="block text-[12px] font-semibold text-ink-faint">Logo lembaga</span>

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
                                                <button type="button" wire:click="deleteLogo"
                                                        wire:confirm="Hapus logo ini?"
                                                        aria-label="Hapus logo"
                                                        class="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                               justify-center rounded-control bg-white/90 text-danger
                                                               transition-colors hover:bg-white">
                                                    <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                                                </button>
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
                                                             text-center text-[11px] leading-snug text-ink-muted">
                                                    {{ $logoFile->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <label title="{{ filled($existingLogoUrl) ? 'Ganti logo' : 'Tambah logo' }}"
                                           class="flex aspect-square cursor-pointer items-center justify-center
                                                  rounded-control border-2 border-dashed border-line-strong
                                                  bg-mist/40 text-ink-faint transition-colors
                                                  hover:border-brand hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="logoFile" id="sertif-logo"
                                               accept="image/*"
                                               aria-label="{{ filled($existingLogoUrl) ? 'Ganti logo' : 'Tambah logo' }}"
                                               class="sr-only">

                                        <span wire:loading.remove wire:target="logoFile">
                                            <svg class="h-9 w-9" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <svg wire:loading wire:target="logoFile"
                                             class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                    Satu gambar saja, maksimal 2 MB.
                                </p>

                                @error('logoFile')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror

                                {{-- ── Dokumen PDF ──────────────────────── --}}
                                <span class="mt-5 block text-[12px] font-semibold text-ink-faint">Dokumen resmi</span>

                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    @if($editingId && filled($existingPdfUrl))
                                        <div class="group relative flex aspect-square flex-col items-center
                                                    justify-center gap-1.5 overflow-hidden rounded-control
                                                    border border-line bg-mist/40 text-ink-muted">
                                            <x-icon.admin name="pdf" size="h-7 w-7" />

                                            <a href="{{ $existingPdfUrl }}" target="_blank" rel="noopener"
                                               class="text-[11px] font-semibold text-brand underline-offset-4 hover:underline">
                                                Buka berkas
                                            </a>

                                            <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                        from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                        group-hover:opacity-100 group-focus-within:opacity-100">
                                                <button type="button" wire:click="deletePdf"
                                                        wire:confirm="Hapus dokumen PDF ini?"
                                                        aria-label="Hapus dokumen PDF"
                                                        class="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                               justify-center rounded-control bg-white/90 text-danger
                                                               transition-colors hover:bg-white">
                                                    <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                                                </button>
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

                                            <span class="line-clamp-2 text-[11px] leading-snug">
                                                {{ $pdfFile->getClientOriginalName() }}
                                            </span>

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <label title="{{ filled($existingPdfUrl) ? 'Ganti dokumen PDF' : 'Tambah dokumen PDF' }}"
                                           class="flex aspect-square cursor-pointer items-center justify-center
                                                  rounded-control border-2 border-dashed border-line-strong
                                                  bg-mist/40 text-ink-faint transition-colors
                                                  hover:border-brand hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="pdfFile" id="sertif-pdf"
                                               accept="application/pdf"
                                               aria-label="{{ filled($existingPdfUrl) ? 'Ganti dokumen PDF' : 'Tambah dokumen PDF' }}"
                                               class="sr-only">

                                        <span wire:loading.remove wire:target="pdfFile">
                                            <svg class="h-9 w-9" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <svg wire:loading wire:target="pdfFile"
                                             class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                    Hanya berkas PDF, maksimal 5 MB.
                                </p>

                                @error('pdfFile')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Penerbitan
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Status</label>

                                        {{-- :nullable="false" — sertifikasi selalu berada
                                             di salah satu dari dua keadaan ini. --}}
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status sertifikasi" placeholder="Aktif"
                                                        :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'active',   'label' => 'Aktif'],
                                                            ['nilai' => 'inactive', 'label' => 'Nonaktif'],
                                                        ]" />

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Yang nonaktif tetap tersimpan di sini, tapi berhenti tampil
                                            di beranda dan halaman Tentang Kami.
                                        </p>

                                        @error('status')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sertif-urutan" class="block text-[12px] font-semibold text-ink-faint">
                                            Urutan tampil
                                        </label>

                                        <input type="number" wire:model="sort_order" id="sertif-urutan"
                                               min="0" step="1" class="admin-control mt-2">

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Angka lebih kecil tampil lebih dulu. Dua sertifikasi berangka
                                            sama akan berurutan seadanya.
                                        </p>

                                        @error('sort_order')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
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
