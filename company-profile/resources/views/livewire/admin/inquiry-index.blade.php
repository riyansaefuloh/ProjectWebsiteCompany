<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Peta sebutan status dipakai dua kali di halaman ini: sekali di
         * dalam menu pilih penyaring, sekali di keping "penyaring aktif".
         * Pilnya sendiri tidak memakai peta ini — ia punya komponennya
         * sendiri (x-admin.status-pill) yang juga dipakai dasbor.
         */
        $sebutanStatus = [
            'new'        => 'Baru',
            'processing' => 'Diproses',
            'quoted'     => 'Ditawar',
            'closed'     => 'Selesai',
            'rejected'   => 'Ditolak',
        ];

        /*
         * Daftar penyaring yang sedang menyala.
         *
         * Dirakit di sini, bukan di dalam komponen, karena isinya murni
         * kalimat untuk dibaca — kuerinya sendiri sudah membaca properti yang
         * sama di sisi PHP. Tiap keping membawa nama properti yang harus
         * dikosongkan saat kepingnya ditutup; "rentang" membawa dua sekaligus
         * karena satu tanggal tanpa pasangannya bukan sebuah rentang.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push([
                'label' => 'Cari', 'nilai' => $search, 'props' => ['search'],
            ]);
        }

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $sebutanStatus[$selectedStatus] ?? $selectedStatus,
                'props' => ['selectedStatus'],
            ]);
        }

        if (filled($selectedProduct)) {
            $namaProduk = $selectedProduct === 'general'
                ? 'Tanpa produk tertentu'
                : (optional($products->firstWhere('id', $selectedProduct))->translated_name ?: 'Produk terpilih');

            $penyaringAktif->push([
                'label' => 'Produk', 'nilai' => $namaProduk, 'props' => ['selectedProduct'],
            ]);
        }

        if (filled($dateFrom) || filled($dateTo)) {
            $awal  = filled($dateFrom) ? \Carbon\Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') : 'awal';
            $akhir = filled($dateTo)   ? \Carbon\Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y')   : 'sekarang';

            $penyaringAktif->push([
                'label' => 'Rentang', 'nilai' => $awal . ' – ' . $akhir,
                'props' => ['dateFrom', 'dateTo'],
            ]);
        }

        /*
         * Pengosong penyaring, ditulis sebagai satu untai JavaScript.
         *
         * $wire.$set punya parameter ketiga: kirim-sekarang. Empat properti
         * pertama disetel dengan false supaya nilainya menumpuk dulu di
         * peramban, dan hanya yang terakhir yang memanggil server — kalau
         * semuanya true, satu klik jadi lima kali muat ulang tabel.
         */
        $bersihkan = function (array $props) {
            $akhir = array_pop($props);

            return collect($props)->map(fn ($p) => "\$wire.\$set('{$p}', '', false);")->implode(' ')
                 . " \$wire.\$set('{$akhir}', '');";
        };
    @endphp


    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                Inquiry
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Permintaan penawaran yang masuk lewat formulir di situs publik.
            </p>
        </div>

        @can('export inquiries')
            {{-- target="_blank": unduhannya berjalan di tab lain supaya
                 penyaring yang sedang dipasang di halaman ini tidak hilang. --}}
            <a href="{{ route('admin.inquiries.export') }}" target="_blank" rel="noopener"
               class="admin-btn admin-btn-brand shrink-0">
                <x-icon.admin name="download" size="h-4 w-4" />
                Ekspor CSV
            </a>
        @endcan
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         PESAN SETELAH TERSIMPAN
         ══════════════════════════════════════════════════════════════════ --}}
    @if(session()->has('message'))
        {{-- x-data + x-show, bukan sekadar div: pesan yang tidak bisa ditutup
             akan menempel di sana sampai halaman dimuat ulang, padahal isinya
             sudah selesai dibaca dalam dua detik. --}}
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
    {{-- overflow-visible, bukan .card apa adanya: menu turun kedua penyaring
         melayang keluar dari kartunya, dan .card membawa overflow-hidden yang
         akan memotongnya tepat di garis bawah kartu. --}}
    <div class="card mb-6 overflow-visible">

        {{-- Satu petak berisi empat kendali yang masing-masing berbingkai
             sendiri. Pencarian membentang selebar petaknya karena ia yang
             paling sering dipakai — dan kolom ketik selebar sepertiga baris
             memotong kata pencarian di tengah.

             Tanpa judul di atasnya: tulisan di dalam tiap kendali —
             "Semua status", "Semua produk", dua tanggal berpasangan — sudah
             mengatakan apa yang disaringnya. Nama yang dibacakan pembaca
             layar tetap ada lewat aria-label, karena di sana tulisan itu
             belum terbaca sebelum kendalinya disentuh. --}}
        <div class="grid gap-x-4 gap-y-4 p-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Pencarian --}}
            <div class="relative sm:col-span-2 lg:col-span-3">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-inquiry"
                       aria-label="Cari inquiry"
                       placeholder="Cari nama pembeli, perusahaan, email, atau kode negara…"
                       class="admin-control pl-11 pr-10">

                {{-- Kincir hanya muncul saat pencarian yang sedang berjalan,
                     bukan saat penyaring lain diubah. --}}
                <span wire:loading wire:target="search"
                      class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>

            {{-- Status --}}
            <x-admin.select model="selectedStatus" :value="$selectedStatus"
                            label="Saring menurut status" placeholder="Semua status"
                            :options="collect($sebutanStatus)
                                ->map(fn ($sebutan, $kunci) => ['nilai' => $kunci, 'label' => $sebutan])
                                ->values()->all()" />

            {{-- Produk --}}
            <x-admin.select model="selectedProduct" :value="$selectedProduct"
                            label="Saring menurut produk" placeholder="Semua produk"
                            :options="collect([['nilai' => 'general', 'label' => 'Tanpa produk tertentu']])
                                ->concat($products->map(fn ($p) => [
                                    'nilai' => $p->id,
                                    'label' => ($n = $p->translated_name) ? $n : $p->slug,
                                ]))->all()" />

            {{-- Rentang tanggal.

                 min/max saling mengunci: tanggal akhir tidak bisa dipilih
                 sebelum tanggal awal, jadi rentang yang mustahil tidak pernah
                 sempat terkirim dan tabelnya tidak pernah kosong tanpa sebab
                 yang terlihat. --}}
            <div class="admin-control-group sm:col-span-2 lg:col-span-1"
                 title="Saring menurut tanggal inquiry masuk">
                <input type="date" wire:model.live="dateFrom" aria-label="Inquiry masuk sejak tanggal"
                       max="{{ $dateTo ?: '' }}" class="admin-control-date">

                <span class="shrink-0 text-[13px] text-ink-faint" aria-hidden="true">–</span>

                <input type="date" wire:model.live="dateTo" aria-label="Inquiry masuk sampai tanggal"
                       min="{{ $dateFrom ?: '' }}" class="admin-control-date">
            </div>
        </div>

        {{-- ── Penyaring yang sedang menyala ─────────────────────────────
             Baris ini hanya ada saat memang ada yang menyala. Tanpanya,
             penyaring yang tergulung ke bawah layar diam-diam memotong
             tabel, dan yang terbaca cuma "datanya hilang". --}}
        @if($penyaringAktif->isNotEmpty())
            {{-- rounded-b-corner ditulis tegas: tanpa overflow-hidden di
                 kartunya, sudut siku strip ini menyembul keluar dari sudut
                 lengkung kartu. --}}
            <div class="flex flex-wrap items-center gap-2 rounded-b-corner border-t border-line
                        bg-mist/60 px-5 py-3">
                <span class="mr-1 inline-flex shrink-0 items-center gap-1.5 text-[11px] font-bold
                             uppercase tracking-[0.08em] text-ink-faint">
                    <x-icon.admin name="filter" size="h-3.5 w-3.5" />
                    Disaring
                </span>

                @foreach($penyaringAktif as $p)
                    <span class="inline-flex max-w-full items-center gap-1.5 rounded-full border border-line
                                 bg-canvas py-1 pl-3 pr-1.5 text-[12px] text-ink-muted">
                        <span class="min-w-0 truncate">
                            {{ $p['label'] }}: <span class="font-semibold text-ink">{{ $p['nilai'] }}</span>
                        </span>

                        <button type="button" x-on:click="{{ $bersihkan($p['props']) }}"
                                aria-label="Hapus penyaring {{ $p['label'] }}"
                                class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full
                                       text-ink-faint transition-colors hover:bg-mist-deep hover:text-ink">
                            <x-icon.admin name="close" size="h-3 w-3" />
                        </button>
                    </span>
                @endforeach

                @if($penyaringAktif->count() > 1)
                    <button type="button"
                            x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo']) }}"
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

        {{-- ── Kepala kartu ──────────────────────────────────────────────
             Jumlahnya ikut disebut di sini, bukan hanya di kaki tabel:
             saat penyaring dipasang, angka inilah yang menjawab "ketemu
             berapa" tanpa harus menggulung ke bawah dulu. --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="inquiry" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar inquiry</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut dari yang paling baru masuk.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($inquiries->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'inquiry' }}
            </span>
        </div>

        {{-- ── Isi ───────────────────────────────────────────────────────
             Diredupkan selama menunggu jawaban server. Tanpa ini, mengetik
             di kolom cari terasa seperti tidak terjadi apa-apa sampai
             tabelnya tiba-tiba berganti. --}}
        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedStatus, selectedProduct, dateFrom, dateTo, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        /* Lebar kolom dibagi menurut panjang isinya yang
                         * sebenarnya, bukan rata. "Pembeli" menampung dua baris
                         * — nama dan surel — dan surel kantor mudah mencapai
                         * tiga puluh huruf, jadi ia yang paling butuh ruang.
                         * "Aksi" cuma satu tombol 32px dan "Masuk" cuma satu
                         * tanggal pendek, jadi keduanya menyumbang sisanya. */
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

                    {{-- Lebar terkecilnya ikut turun dari 1120 ke 1040: satu
                         kolom hilang, jadi sisanya tidak perlu didesak sampai
                         tabelnya bergulir mendatar lebih cepat dari perlunya. --}}
                    <table class="w-full min-w-[1040px] table-fixed">
                        {{-- Kepala kolom disembunyikan saat tidak ada barisnya:
                             delapan judul kolom yang berdiri di atas ruang
                             kosong terbaca sebagai tabel yang rusak, bukan
                             sebagai jawaban "tidak ada yang cocok". --}}
                        @if($inquiries->isNotEmpty())
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

                        <tbody>
                            @forelse($inquiries as $inq)
                                @php
                                    $baru   = $inq->status === 'new';
                                    $sales  = $inq->assignedSales;
                                    $produk = $inq->product?->translated_name;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Pembeli. Garis hijau di tepi kiri menandai yang
                                         belum tersentuh — penanda kedua di samping
                                         pil "Baru", supaya barisnya bisa dikenali
                                         dari ujung mata tanpa membaca kolom status. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $baru,
                                        'border-transparent' => ! $baru,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$inq->name" size="sm" />

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $inq->name }}">{{ $inq->name }}</span>
                                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
                                                      title="{{ $inq->email }}">{{ $inq->email }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Perusahaan, dengan negaranya sebagai baris kedua.

                                         Keduanya menjawab pertanyaan yang sama —
                                         "pembeli ini dari mana" — jadi menaruhnya
                                         di satu sel menghemat satu kolom penuh dan
                                         menghapus satu lompatan mata. Negaranya
                                         dibuat lebih kecil dan pudar supaya jelas
                                         ia keterangan, bukan judul. --}}
                                    <td class="px-3 py-4 align-middle">
                                        {{-- Tebal hanya kalau isinya sungguh ada.
                                             "Perorangan" itu penjelasan kami sendiri
                                             untuk kolom yang kosong, bukan nama yang
                                             ditulis pembeli — kalau ikut ditebalkan, ia
                                             terbaca seolah ada perusahaan bernama itu. --}}
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'font-semibold text-ink' => filled($inq->company),
                                            'text-ink-faint'         => blank($inq->company),
                                        ]) title="{{ $inq->company }}">{{ $inq->company ?: 'Perorangan' }}</span>

                                        <x-admin.country :code="$inq->country_code" size="sm" class="mt-1" />
                                    </td>

                                    {{-- Produk + volume yang diminta --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'font-semibold text-ink' => filled($produk),
                                            'text-ink-faint'         => blank($produk),
                                        ]) title="{{ $produk }}">{{ $produk ?: 'Tanpa produk tertentu' }}</span>

                                        @if(filled($inq->volume))
                                            <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
                                                  title="Volume diminta: {{ $inq->volume }}">{{ $inq->volume }}</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$inq->status" />
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

                                    {{-- Masuk. Berdiri di sebelah kiri kolom aksi: tanggal
                                         masuk jarang jadi hal pertama yang dicari — barisnya
                                         sudah urut dari yang terbaru — sementara nama pembeli
                                         iya, jadi nama yang berhak atas tepi kiri tabel. --}}
                                    <td class="px-3 py-4 align-middle">
                                        <time datetime="{{ $inq->created_at->toIso8601String() }}" class="block">
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $inq->created_at->locale('id')->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $inq->created_at->format('H:i') }}
                                            </span>
                                        </time>
                                    </td>

                                    {{-- Aksi. Ikon saja, dengan keterangan yang muncul
                                         saat disorot: satu kolom "Kelola" berteks penuh
                                         memakan lebar yang lebih berguna untuk nama
                                         produk, dan tindakannya cuma satu. --}}
                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <button type="button" wire:click="viewDetails('{{ $inq->id }}')"
                                                title="Kelola inquiry dari {{ $inq->name }}"
                                                aria-label="Kelola inquiry dari {{ $inq->name }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                       border border-line bg-canvas text-ink-muted transition-colors
                                                       hover:border-brand hover:bg-brand hover:text-white">
                                            <x-icon.admin name="manage" size="h-4 w-4" />
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                {{-- Dua kalimat berbeda untuk dua keadaan yang berbeda.
                                     "Belum ada inquiry" saat memang kosong; "tidak ada
                                     yang cocok" saat penyaringnya yang menyembunyikan —
                                     karena yang kedua bisa diperbaiki, yang pertama
                                     tidak. --}}
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'inquiry'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada inquiry yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba longgarkan penyaringnya — misalnya lebarkan rentang tanggal
                                                atau kosongkan kata pencarian.
                                            </p>

                                            <button type="button"
                                                    x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus semua penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada inquiry yang masuk
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Permintaan penawaran dari formulir kontak di situs publik
                                                akan muncul di sini begitu terkirim.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Kaki: penomoran halaman ───────────────────────────────────
             Di dalam kartu, dipisah garis — bukan melayang di bawahnya —
             supaya jelas ia milik tabel ini, bukan halaman secara umum. --}}
        <div class="border-t border-line px-6 py-4">
            {{ $inquiries->links('vendor.pagination.admin', ['satuan' => 'inquiry']) }}
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         MODAL KELOLA INQUIRY
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal && $selectedInquiry)
        @php
            $pilihanStatus = collect($sebutanStatus)
                ->map(fn ($sebutan, $kunci) => ['nilai' => $kunci, 'label' => $sebutan])
                ->values()->all();

            $pilihanSales = $salesUsers
                ->map(fn ($u) => ['nilai' => $u->id, 'label' => $u->name])
                ->values()->all();

            $produkModal = $selectedInquiry->product?->translated_name;

            // Nomor telepon dibersihkan untuk tautan tel:. Spasi, tanda kurung,
            // dan tanda hubung sah ditulis manusia, tapi membuat tautannya
            // gagal dibuka di sebagian peramban.
            $telepon = preg_replace('/[^\d+]/', '', (string) $selectedInquiry->phone);

            /*
             * Tautan balas cepat.
             *
             * wa.me menerima angka saja — tanpa tanda tambah, tanpa spasi,
             * tanpa tanda kurung. Nomor yang ditulis pembeli hampir selalu
             * membawa ketiganya, dan wa.me diam saja kalau nomornya tidak
             * bersih: halamannya tetap terbuka, cuma tidak ada percakapan
             * yang dimulai.
             */
            $nomorWa = preg_replace('/\D/', '', (string) $selectedInquiry->phone);

            /*
             * Perihal surel diisi lebih dulu — itulah bagian "cepat"-nya.
             * Ditulis dalam bahasa Inggris karena yang membacanya pembeli di
             * luar negeri, bukan tim di panel ini.
             */
            $perihal = 'Re: Your inquiry'
                . ($selectedInquiry->product?->translated_name
                    ? ' - ' . $selectedInquiry->product->translated_name : '');

            $tautanSurel = 'mailto:' . $selectedInquiry->email
                . '?subject=' . rawurlencode($perihal);

        @endphp

        {{-- modal-open: kelas penanda yang dibaca aturan
             html:has(.modal-open) di app.css untuk mengunci gulungan halaman
             di belakangnya.

             overflow-clip, BUKAN overflow-y-auto atau overflow-hidden. Ini
             pelajaran mahal dari modal sebelumnya di proyek ini: dengan
             auto, roda tetikus menyeret panel yang tengah-tengah ini sampai
             keluar layar; dengan hidden, rodanya memang berhenti tapi
             gulungannya masih bisa dijalankan dari kode — dan fokus yang
             kembali ke kolom berkas sesudah penjelajah berkas ditutup
             melakukan persis itu, menggeser panelnya 866px ke atas.
             overflow-clip tidak membuat wadah gulung sama sekali. --}}
        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal">

            {{-- Latar sebagai penutup. Elemen tersendiri, bukan click.outside
                 di panelnya: click.outside juga menyala saat menu turun di
                 dalam panel diklik. --}}
            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[1000px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                {{-- ── Kepala ──────────────────────────────────────────── --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <x-admin.avatar :name="$selectedInquiry->name" size="sm" tone="brand" />

                        <div class="min-w-0">
                            <h2 id="judul-modal" class="truncate font-ui text-[15px] font-semibold text-ink">
                                {{ $selectedInquiry->name }}
                            </h2>
                            <p class="mt-0.5 flex items-center gap-2 text-[12px] text-ink-muted">
                                <span>Masuk {{ $selectedInquiry->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                                <span aria-hidden="true">·</span>
                                <x-admin.status-pill :status="$selectedInquiry->status" class="!py-0.5" />
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

                {{-- ── Dua kolom ───────────────────────────────────────────
                     Kiri keterangan pembeli, kanan tindakan — dan KEDUANYA
                     bergulir sendiri-sendiri.

                     Dulu kolom kanan sengaja tidak dibuat bergulir, karena
                     wadah gulung akan memotong menu turun di dalamnya. Alasan
                     itu sudah hilang sejak daftarnya dipasang dengan
                     position: fixed — ia melayang lepas dari induknya, jadi
                     tidak ada lagi tepi yang bisa memotongnya.

                     Di layar sempit susunannya bertumpuk, dan di sana justru
                     BARIS ini yang bergulir sebagai satu kesatuan: dua wadah
                     gulung yang bertumpuk membagi tinggi layar berdua, dan
                     masing-masing kebagian ruang yang terlalu pendek untuk
                     dipakai. --}}
                <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                            lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                    {{-- ══ KIRI ══
                         Dipecah jadi dua kartu, bukan satu daftar panjang
                         sembilan baris. Keduanya menjawab pertanyaan yang
                         berbeda: yang pertama "siapa dan bagaimana
                         menghubunginya", yang kedua "apa yang dia minta" —
                         dan itu dua pekerjaan yang jarang dilakukan
                         bersamaan. --}}
                    <div class="admin-scroll min-h-0 space-y-4 bg-mist/40 p-6
                                lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                        {{-- ── Kartu 1: profil pembeli ──────────────────
                             Judulnya duduk di dalam bantalan kartu tanpa garis
                             pemisah. Garis di bawah judul memisahkan dua hal
                             yang setara; di sini judulnya memayungi isinya,
                             dan jarak sudah cukup mengatakan itu. --}}
                        <section class="rounded-corner border border-line bg-canvas p-5">
                            <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                Profil pembeli
                            </h3>

                            {{-- Bertumpuk selebar kartunya, kecuali surel dan
                                 nomor telepon yang berbagi satu baris: keduanya
                                 sama-sama saluran menghubungi, dan mata mencari
                                 keduanya sekaligus. --}}
                            {{-- Kelimanya bertumpuk selebar kartunya. Sebelumnya
                                 surel dan nomor telepon berbagi satu baris, tapi
                                 setengah kartu memaksa alamat surel membungkus jadi
                                 dua baris — dan alamat yang patah di tengah lebih
                                 sulit dibaca daripada satu baris tambahan. --}}
                            <dl class="space-y-3.5">
                                <x-admin.datum label="Nama" :value="$selectedInquiry->name" />

                                <x-admin.datum label="Perusahaan" :value="$selectedInquiry->company"
                                               kosong="Perorangan" />

                                {{-- Keping kode di depan namanya, sama persis dengan
                                     yang ada di bawah nama perusahaan pada tabel
                                     inquiry. Kode itulah yang dipakai di berkas
                                     ekspor dan dokumen pengiriman, jadi ia yang
                                     berhak dibaca lebih dulu. --}}
                                <x-admin.datum label="Negara">
                                    <x-admin.country :code="$selectedInquiry->country_code" />
                                </x-admin.datum>

                                <x-admin.datum label="Email">
                                    {{-- break-all, bukan dipotong: alamat surel yang
                                         terpotong tidak bisa disalin, dan menyalinnya
                                         justru tujuan utama baris ini. --}}
                                    <a href="mailto:{{ $selectedInquiry->email }}"
                                       class="block break-all text-[13px] leading-5 text-brand
                                              underline-offset-4 hover:underline">{{ $selectedInquiry->email }}</a>
                                </x-admin.datum>

                                <x-admin.datum label="WhatsApp / Telepon">
                                    @if(filled($telepon))
                                        <a href="tel:{{ $telepon }}"
                                           class="block break-all text-[13px] leading-5 text-brand
                                                  underline-offset-4 hover:underline">{{ $selectedInquiry->phone }}</a>
                                    @else
                                        <span class="text-[13px] leading-5 text-ink-faint">Tidak diisi</span>
                                    @endif
                                </x-admin.datum>
                            </dl>
                        </section>

                        {{-- ── Kartu 2: rincian permintaan ──────────────── --}}
                        <section class="rounded-corner border border-line bg-canvas p-5">
                            <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                Rincian permintaan
                            </h3>

                            <dl class="space-y-3.5">
                                <x-admin.datum label="Produk" :value="$produkModal"
                                               kosong="Tanpa produk tertentu" />

                                <x-admin.datum label="Estimasi volume" :value="$selectedInquiry->volume" />

                                <x-admin.datum label="Incoterms" :value="$selectedInquiry->incoterms" />

                                {{-- blok: pembeli mengetik pesannya sendiri lengkap
                                     dengan gantian baris, dan tanpa whitespace-pre-line
                                     seluruhnya luruh jadi satu paragraf panjang. --}}
                                <x-admin.datum label="Pesan" :value="$selectedInquiry->message"
                                               kosong="Pembeli tidak menuliskan pesan."
                                               :blok="true" />
                            </dl>
                        </section>
                    </div>

                    {{-- ══ KANAN ══ --}}
                    <div class="admin-scroll min-h-0 border-t border-line p-6
                                lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                        {{-- ── Kartu 1: penanganan ─────────────────────
                             Bentuknya sengaja sama persis dengan kedua kartu
                             di kolom kiri: kartu berbingkai, judul kapital
                             14px, jarak 16px ke isinya. Dua kolom bersebelahan
                             yang aturan bentuknya berbeda terbaca seperti dua
                             layar yang kebetulan bertetangga. --}}
                        <section class="rounded-corner border border-line bg-canvas p-5">
                            <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                Penanganan
                            </h3>

                            {{-- Judul di atas kendali TETAP ada di sini, berbeda
                                 dari bilah penyaring. Di sana tulisan "Semua
                                 status" sudah menerangkan dirinya sendiri; di
                                 sini isinya "Diproses" — sebuah nilai, bukan
                                 keterangan — dan tanpa judul tidak ada yang
                                 memberitahu itu status apa. --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[12px] font-semibold text-ink-faint">
                                        Status inquiry
                                    </label>

                                    {{-- :nullable="false" — tiap inquiry pasti punya
                                         status, jadi tidak ada "belum diisi" untuk
                                         dipilih. Tanpa ini, baris kosongnya tergambar
                                         memakai teks cadangan dan "Baru" muncul dua
                                         kali berturut-turut di daftar yang sama. --}}
                                    <x-admin.select model="status" :value="$status" class="mt-2"
                                                    label="Status inquiry" placeholder="Baru"
                                                    :nullable="false" :options="$pilihanStatus" />
                                </div>

                                <div>
                                    <label class="block text-[12px] font-semibold text-ink-faint">
                                        Ditangani oleh
                                    </label>

                                    {{-- Di sini kekosongan justru sebuah jawaban yang
                                         sah: "Belum ditugaskan". --}}
                                    <x-admin.select model="assigned_to" :value="$assigned_to" class="mt-2"
                                                    label="Ditangani oleh" placeholder="Belum ditugaskan"
                                                    :options="$pilihanSales" />
                                </div>

                                <div>
                                    <label for="catatan-internal"
                                           class="block text-[12px] font-semibold text-ink-faint">
                                        Catatan internal
                                    </label>

                                    <textarea wire:model="internal_note" id="catatan-internal" rows="5"
                                              placeholder="Hasil percakapan, harga yang ditawarkan, langkah berikutnya…"
                                              class="admin-control mt-2 resize-none leading-relaxed"></textarea>

                                    <p class="mt-2 flex items-start gap-1.5 text-[12px] leading-relaxed text-ink-faint">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M8 7.4v3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            <circle cx="8" cy="5.2" r="0.75" fill="currentColor"/>
                                        </svg>
                                        Hanya terlihat oleh tim, tidak pernah dikirim ke pembeli.
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- ── Kartu 2: balas cepat ────────────────────────
                             Keduanya membuka jendela lain, jadi apa pun yang
                             sudah diketik di catatan internal tidak ikut
                             hilang — itu sebabnya keduanya tautan ber-target
                             _blank, bukan tombol yang menutup modalnya dulu. --}}
                        <section class="mt-4 rounded-corner border border-line bg-canvas p-5">
                            <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                Balas cepat
                            </h3>

                            <div class="space-y-2.5">
                                {{-- Perihalnya sudah terisi, tinggal menulis isinya. --}}
                                <a href="{{ $tautanSurel }}" target="_blank" rel="noopener"
                                   class="admin-btn admin-btn-quiet w-full">
                                    <x-icon.admin name="mail" size="h-4 w-4" class="shrink-0 text-brand" />
                                    Balas lewat email
                                </a>

                                @if(filled($nomorWa))
                                    <a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener"
                                       class="admin-btn admin-btn-quiet w-full">
                                        {{-- Hijau WhatsApp, bukan hijau merek: yang
                                             dikenali orang dari tombol ini justru warna
                                             dan bentuk lambangnya. --}}
                                        <x-icon.admin name="whatsapp" size="h-4 w-4"
                                                      class="shrink-0 text-[#25D366]" />
                                        Chat lewat WhatsApp
                                    </a>
                                @else
                                    {{-- Tombol mati, bukan disembunyikan: tombol yang
                                         kadang ada kadang tidak membuat orang mengira
                                         panelnya rusak. Yang mati menerangkan sebabnya. --}}
                                    <span aria-disabled="true"
                                          title="Pembeli tidak mengisi nomor telepon"
                                          class="admin-btn admin-btn-quiet w-full cursor-not-allowed opacity-50
                                                 hover:border-line hover:bg-canvas hover:text-ink-muted">
                                        <x-icon.admin name="whatsapp" size="h-4 w-4" class="shrink-0" />
                                        Nomor WhatsApp tidak diisi
                                    </span>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>

                {{-- ── Kaki ────────────────────────────────────────────── --}}
                <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="admin-btn admin-btn-quiet">
                        Batal
                    </button>

                    <button type="button" wire:click="updateStatus" wire:loading.attr="disabled"
                            wire:target="updateStatus"
                            class="admin-btn admin-btn-brand disabled:opacity-60">
                        <svg wire:loading wire:target="updateStatus"
                             class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        Simpan perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
