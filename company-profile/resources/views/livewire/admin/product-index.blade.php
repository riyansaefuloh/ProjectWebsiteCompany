<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * Inquiry. Tiap keping membawa nama properti yang dikosongkan saat
         * kepingnya ditutup.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        $sebutanStatus = ['published' => 'Terbit', 'draft' => 'Draf'];

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $sebutanStatus[$selectedStatus] ?? $selectedStatus,
                'props' => ['selectedStatus'],
            ]);
        }

        /*
         * filled(), bukan sekadar cek kebenaran nilainya: '0' — pilihan
         * "bukan unggulan" — itu palsu di PHP, jadi kepingnya tidak akan
         * pernah muncul dan penyaring yang menyala jadi tak terlihat.
         */
        if (filled($selectedFeatured)) {
            $penyaringAktif->push([
                'label' => 'Unggulan',
                'nilai' => $selectedFeatured === '1' ? 'Ya' : 'Tidak',
                'props' => ['selectedFeatured'],
            ]);
        }

        if (filled($selectedCategory)) {
            $kategoriTerpilih = $categories->firstWhere('id', $selectedCategory);
            $namaKategori     = $kategoriTerpilih ? ($kategoriTerpilih->translated_name ?: $kategoriTerpilih->slug) : null;

            $penyaringAktif->push([
                'label' => 'Kategori', 'nilai' => $namaKategori ?: 'Kategori terpilih',
                'props' => ['selectedCategory'],
            ]);
        }

        /*
         * $wire.$set punya parameter ketiga: kirim-sekarang. Semua kecuali
         * yang terakhir disetel dengan false supaya nilainya menumpuk dulu di
         * peramban — kalau semuanya true, satu klik jadi beberapa kali muat
         * ulang tabel.
         */
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
                Produk
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Katalog produk ekspor yang tampil di situs publik.
            </p>
        </div>

        {{-- Hijau merek: di halaman ini menambah produk memang tindakan
             utamanya, berbeda dari halaman Inquiry yang tombol hijaunya
             dipakai untuk mengekspor. --}}
        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah produk
        </button>
    </div>


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
    {{-- overflow-visible: menu turun kategori melayang keluar dari kartunya,
         dan .card membawa overflow-hidden yang akan memotongnya. --}}
    <div class="card mb-6 overflow-visible">

        {{-- Pencarian berdiri sendiri selebar kartunya, lalu tiga penyaring
             berbagi baris di bawahnya — susunan yang sama dengan halaman
             Inquiry. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-3">

            <div class="relative lg:col-span-3">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-produk"
                       aria-label="Cari produk"
                       placeholder="Cari nama produk atau kode HS…"
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
                                ['nilai' => 'published', 'label' => 'Terbit'],
                                ['nilai' => 'draft',     'label' => 'Draf'],
                            ]" />

            {{-- Nilainya '1' dan '0', bukan true/false: menu pilih ini
                 mengirimkan untai, dan properti penyaringnya pun bertipe
                 untai supaya "belum dipilih" ('') bisa dibedakan dari
                 "bukan unggulan" ('0'). --}}
            <x-admin.select model="selectedFeatured" :value="$selectedFeatured"
                            label="Saring menurut unggulan" placeholder="Semua produk"
                            :options="[
                                ['nilai' => '1', 'label' => 'Unggulan saja'],
                                ['nilai' => '0', 'label' => 'Bukan unggulan'],
                            ]" />

            <x-admin.select model="selectedCategory" :value="$selectedCategory"
                            label="Saring menurut kategori" placeholder="Semua kategori"
                            :options="$categories->map(fn ($k) => [
                                'nilai' => $k->id,
                                'label' => ($n = $k->translated_name) ? $n : $k->slug,
                            ])->all()" />
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
                            x-on:click="{{ $bersihkan(['search', 'selectedCategory', 'selectedStatus', 'selectedFeatured']) }}"
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
                    <x-icon.admin name="product" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar produk</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">Urut dari yang paling baru ditambahkan.</p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($products->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'produk' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedCategory, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        /* Lima kolom, bukan tujuh. Kode HS pindah ke bawah nama
                           produknya, dan MOQ bergabung dengan kapasitas —
                           keduanya menjawab pertanyaan yang sama, "berapa banyak
                           yang bisa dipesan", jadi memisahkannya jadi dua kolom
                           memaksa mata melompat untuk merangkai satu jawaban. */
                        $kolom = [
                            ['label' => 'Produk',           'lebar' => 'w-[37%]', 'rata' => 'text-left'],
                            ['label' => 'Kategori',         'lebar' => 'w-[17%]', 'rata' => 'text-left'],
                            ['label' => 'MOQ & kapasitas',  'lebar' => 'w-[23%]', 'rata' => 'text-left'],
                            ['label' => 'Status',           'lebar' => 'w-[11%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',             'lebar' => 'w-[12%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[880px] table-fixed">
                        @if($products->isNotEmpty())
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
                            @forelse($products as $product)
                                @php
                                    $nama     = $product->translated_name ?: $product->slug;
                                    $kategori = $product->category?->translated_name;

                                    /*
                                     * Gambar yang ditampilkan adalah SAMPULNYA — berkas
                                     * yang ditandai is_cover lewat tombol "jadikan
                                     * sampul" di modal. Kalau belum ada yang ditandai,
                                     * yang pertama diunggah yang dipakai, sama dengan
                                     * yang tampil di katalog publik.
                                     *
                                     * getUrl('thumb') hanya dipakai kalau berkas
                                     * turunannya memang sudah jadi; kalau dipanggil
                                     * begitu saja, ia mengembalikan alamat berkas yang
                                     * belum tentu ada dan gambarnya jadi rusak.
                                     */
                                    $galeri = $product->getMedia('gallery');
                                    $sampul = $galeri->first(fn ($m) => $m->getCustomProperty('is_cover'))
                                              ?? $galeri->first();

                                    $alamatGambar = $sampul
                                        ? ($sampul->hasGeneratedConversion('thumb')
                                            ? $sampul->getUrl('thumb')
                                            : $sampul->getUrl())
                                        : null;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Produk. Garis hijau di tepi kiri menandai yang
                                         diunggulkan — penanda kedua di samping lencana
                                         bintangnya, supaya barisnya bisa dikenali dari
                                         ujung mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $product->is_featured,
                                        'border-transparent' => ! $product->is_featured,
                                    ])>
                                        <div class="flex items-center gap-3">

                                            {{-- Gambar sampul. Kotak penggantinya adalah
                                                 bagian sah dari rancangannya, bukan
                                                 keadaan rusak: ia berukuran sama persis
                                                 dengan gambarnya, jadi tinggi baris tidak
                                                 melompat-lompat antara produk yang sudah
                                                 bergambar dan yang belum. --}}
                                            @if($alamatGambar)
                                                <img src="{{ $alamatGambar }}" alt=""
                                                     loading="lazy" width="40" height="40"
                                                     class="h-10 w-10 shrink-0 rounded-control border border-line
                                                            bg-mist object-cover">
                                            @else
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                             rounded-control border border-dashed border-line-strong
                                                             bg-mist text-ink-faint"
                                                      title="Produk ini belum punya gambar">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="min-w-0 truncate text-[13px] font-semibold text-ink"
                                                          title="{{ $nama }}">{{ $nama }}</span>

                                                    @if($product->is_featured)
                                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full
                                                                     bg-brand/10 px-2 py-0.5 text-[10px] font-bold text-brand"
                                                              title="Ditampilkan sebagai produk unggulan">
                                                            <x-icon.admin name="star" size="h-2.5 w-2.5" />
                                                            Unggulan
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Kode HS jadi baris kedua. Angkanya
                                                     berlebar seragam karena yang dilakukan
                                                     orang dengan kode ini adalah
                                                     mencocokkannya digit demi digit. --}}
                                                <span class="mt-0.5 block truncate text-[12px] tabular-nums text-ink-faint"
                                                      title="Kode HS: {{ $product->hs_code }}">
                                                    {{ $product->hs_code ? 'HS ' . $product->hs_code : 'Tanpa kode HS' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        {{-- Tebal hanya kalau kategorinya sungguh ada.
                                             "Tanpa kategori" itu keterangan kami untuk
                                             kolom yang kosong, bukan nama kategori. --}}
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'font-semibold text-ink' => filled($kategori),
                                            'text-ink-faint'         => blank($kategori),
                                        ]) title="{{ $kategori }}">{{ $kategori ?: 'Tanpa kategori' }}</span>
                                    </td>

                                    {{-- MOQ di baris atas, kapasitas di bawahnya —
                                         urutan yang sama dengan judul kolomnya, dan
                                         bentuk dua baris yang sama dengan sel lain di
                                         panel ini: yang atas 13px, yang bawah 12px
                                         lebih pudar.

                                         Namanya tidak lagi ditulis di samping angkanya,
                                         tapi tetap dibawa atribut title — di layar yang
                                         dipakai berjam-jam, urutannya cepat hafal;
                                         yang baru sehari cukup menyorotnya. --}}
                                    <td class="px-3 py-4 align-middle">
                                        <span @class([
                                            'block truncate text-[13px]',
                                            'text-ink-muted' => filled($product->moq),
                                            'text-ink-faint' => blank($product->moq),
                                        ]) title="MOQ: {{ $product->moq }}">{{ $product->moq ?: '—' }}</span>

                                        <span @class([
                                            'mt-0.5 block truncate text-[12px] text-ink-faint',
                                        ]) title="Kapasitas: {{ $product->supply_capacity }}">{{ $product->supply_capacity ?: '—' }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$product->status" />
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $product->id }}')"
                                                    title="Ubah {{ $nama ?: $product->slug }}"
                                                    aria-label="Ubah {{ $nama ?: $product->slug }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- wire:confirm — menghapus produk ikut
                                                 menghapus terjemahan, spesifikasi, dan
                                                 gambarnya, dan tidak ada jalan
                                                 kembalinya. --}}
                                            <button type="button" wire:click="delete('{{ $product->id }}')"
                                                    wire:confirm="Hapus produk &quot;{{ $nama ?: $product->slug }}&quot;? Terjemahan, spesifikasi, dan gambarnya ikut terhapus."
                                                    title="Hapus {{ $nama ?: $product->slug }}"
                                                    aria-label="Hapus {{ $nama ?: $product->slug }}"
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
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'product'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada produk yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba longgarkan penyaringnya — kosongkan kata pencarian,
                                                atau kembalikan kategori, status, dan unggulan ke "semua".
                                            </p>

                                            <button type="button"
                                                    x-on:click="{{ $bersihkan(['search', 'selectedCategory', 'selectedStatus', 'selectedFeatured']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus semua penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada produk
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Produk yang ditambahkan di sini akan tampil di katalog
                                                situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah produk pertama
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
            {{ $products->links('vendor.pagination.admin', ['satuan' => 'produk']) }}
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH PRODUK
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            /*
             * Kolom yang wajib diisi menurut rules() di komponennya. Dirakit
             * sebagai daftar supaya tanda bintangnya tidak perlu ditulis satu
             * per satu — dan tidak bisa ketinggalan saat aturannya berubah.
             */
            $wajib = ['category_id', 'name_en', 'name_id', 'hs_code', 'moq',
                      'supply_capacity', 'packaging', 'origin', 'incoterms'];

            // Galat yang jatuh di tab yang sedang tertutup tidak terlihat sama
            // sekali; tabnya diberi titik merah supaya ketahuan.
            $galatEn = $errors->hasAny(['name_en', 'description_en']);
            $galatId = $errors->hasAny(['name_id', 'description_id']);

            /*
             * Incoterms disimpan sebagai satu untai berpemisah koma di basis
             * data — "FOB,CIF". Sebelumnya ia kolom ketik bebas, dan datanya
             * memang sudah tidak seragam: satu produk tertulis "FOB, CIF"
             * dengan spasi, yang lain tanpa spasi. Sebagai daftar centang,
             * bentuknya jadi satu macam saja.
             */
            $daftarIncoterms = [
                'FOB' => 'Free On Board',
                'CIF' => 'Cost, Insurance and Freight',
                'CFR' => 'Cost and Freight',
            ];

            $incotermsTerpilih = collect(explode(',', (string) $incoterms))
                ->map(fn ($x) => strtoupper(trim($x)))
                ->filter()->unique()->values();

            /*
             * Kode yang tidak ada di daftar baku tetap ikut ditampilkan dan
             * tetap tercentang. Tanpa ini, produk lama yang incoterms-nya
             * ditulis di luar ketiga pilihan itu akan kehilangan nilainya
             * diam-diam begitu centang lain disentuh.
             */
            $incotermsAsing = $incotermsTerpilih
                ->reject(fn ($k) => isset($daftarIncoterms[$k]))->values();

            $urutanIncoterms = collect(array_keys($daftarIncoterms))
                ->concat($incotermsAsing)->all();
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-produk">

            {{-- Latar sebagai penutup. Elemen tersendiri, bukan click.outside
                 di panelnya: click.outside juga menyala saat menu turun di
                 dalam panel diklik. --}}
            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[1100px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="product" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-produk"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah produk' : 'Tambah produk' }}
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

                    {{-- ── Dua kolom ───────────────────────────────────────
                         Kiri isi produknya, kanan penerbitan dan berkasnya —
                         pembagian yang sama dengan modal inquiry. Keduanya
                         bergulir sendiri-sendiri; di layar sempit barisnya
                         yang bergulir sebagai satu kesatuan. --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: informasi produk ──────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">

                                {{-- Sakelar bahasa naik ke kepala kartu, bukan
                                     menempel di satu isian. Ia memang mengatur dua
                                     isian sekaligus — nama dan deskripsi — sementara
                                     Asal di antara keduanya tidak ikut berganti,
                                     karena nama tempat tidak diterjemahkan. --}}
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Informasi produk
                                    </h3>

                                    <div class="inline-flex rounded-control border border-line bg-mist p-1">
                                        @foreach(['en' => 'English', 'id' => 'Indonesia'] as $kode => $sebutan)
                                            <button type="button" wire:click="$set('activeTab', '{{ $kode }}')"
                                                    @class([
                                                        'inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5
                                                         text-[12px] font-semibold transition-colors',
                                                        'bg-canvas text-ink shadow-[0_1px_2px_rgba(26,29,27,0.08)]'
                                                            => $activeTab === $kode,
                                                        'text-ink-muted hover:text-ink' => $activeTab !== $kode,
                                                    ])>
                                                {{ $sebutan }}

                                                {{-- Titik merah: tab ini menyimpan galat
                                                     yang tidak terlihat karena tertutup. --}}
                                                @if(($kode === 'en' && $galatEn) || ($kode === 'id' && $galatId))
                                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-danger"
                                                          title="Ada isian yang perlu diperbaiki di sini"></span>
                                                @endif
                                            </button>
                                        @endforeach
                                        <div class="ml-2 border-l border-line pl-2 flex items-center">
                                            <button type="button" wire:click="autoTranslate" wire:loading.attr="disabled" wire:target="autoTranslate"
                                                title="Terjemahkan ID ke EN otomatis"
                                                class="inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5 text-[12px] font-semibold bg-brand/10 text-brand hover:bg-brand/20 transition-colors">
                                                <span wire:loading.remove wire:target="autoTranslate">🌐 Auto EN</span>
                                                <span wire:loading wire:target="autoTranslate">⏳ ...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">

                                    {{-- Nama produk — mengikuti tab. Keduanya tetap ada
                                         di DOM, yang tidak aktif disembunyikan: isian
                                         yang diketik lalu elemennya lenyap membuat
                                         Livewire kehilangan nilainya. --}}
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">
                                            Nama produk <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="name_en"
                                                   aria-label="Nama produk dalam bahasa Inggris"
                                                   placeholder="Nama produk dalam bahasa Inggris"
                                                   class="admin-control">
                                            @error('name_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="name_id"
                                                   aria-label="Nama produk dalam bahasa Indonesia"
                                                   placeholder="Nama produk dalam bahasa Indonesia"
                                                   class="admin-control">
                                            @error('name_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Deskripsi — mengikuti tab. --}}
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Deskripsi</label>

                                        <textarea wire:model="description_en" rows="5"
                                                  aria-label="Deskripsi dalam bahasa Inggris"
                                                  placeholder="Deskripsi dalam bahasa Inggris…"
                                                  @class(['admin-control mt-2 resize-none leading-relaxed',
                                                          'hidden' => $activeTab !== 'en'])></textarea>

                                        <textarea wire:model="description_id" rows="5"
                                                  aria-label="Deskripsi dalam bahasa Indonesia"
                                                  placeholder="Deskripsi dalam bahasa Indonesia…"
                                                  @class(['admin-control mt-2 resize-none leading-relaxed',
                                                          'hidden' => $activeTab !== 'id'])></textarea>
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: syarat dagang & ekspor ────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Syarat dagang &amp; ekspor
                                </h3>

                                @php
                                    /* Contoh isian ditulis sebagai placeholder, bukan
                                       keterangan di bawah kolomnya: keterangan yang
                                       menetap ikut terbaca meski kolomnya sudah diisi,
                                       sementara placeholder hilang sendiri. */
                                    /* Asal berdiri paling atas: di dokumen bea cukai
                                       pun asal dan kode HS memang dibaca berpasangan —
                                       barangnya dari mana, dan digolongkan sebagai apa. */
                                    $dagang = [
                                        ['origin',          'Asal',             'mis. Manggarai, Flores, NTT (1.300 – 1.700 mdpl)'],
                                        ['hs_code',         'Kode HS',          '0901.11.10'],
                                        ['moq',             'MOQ',              'mis. 1 x 20ft container'],
                                        ['supply_capacity', 'Kapasitas suplai', 'mis. 100 ton / bulan'],
                                        ['packaging',       'Kemasan',          'mis. Karung goni 60 kg'],
                                    ];
                                @endphp

                                {{-- Bertumpuk selebar kartunya, bukan berpetak dua
                                     kolom. Alasannya kerapian sebaris: daftar centang
                                     incoterms di bawahnya membentang penuh, dan kolom
                                     setengah lebar di atasnya membuat tepi kanan kartu
                                     ini patah di tengah. --}}
                                <div class="space-y-4">
                                    @foreach($dagang as [$kolom, $sebutan, $contoh])
                                        <div>
                                            <label for="produk-{{ $kolom }}"
                                                   class="block text-[12px] font-semibold text-ink-faint">
                                                {{ $sebutan }} <span class="text-brand">*</span>
                                            </label>

                                            <input type="text" wire:model="{{ $kolom }}" id="produk-{{ $kolom }}"
                                                   placeholder="{{ $contoh }}" class="admin-control mt-2">

                                            @error($kolom)
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach

                                    {{-- ── Harga indikatif ──────────────────────
                                         Kolom indicative_price dan currency sudah lama
                                         ada di basis data, punya aturan validasi, dan
                                         ikut disimpan — tapi tidak pernah punya isian di
                                         sini. Akibatnya seluruh produk permanen bernilai
                                         null / USD, tak peduli apa harganya.

                                         Opsional, sesuai PRD 7.5: tidak semua komoditas
                                         pantas dipasang harganya di halaman terbuka. --}}
                                    <div>
                                        <label for="produk-indicative_price"
                                               class="block text-[12px] font-semibold text-ink-faint">
                                            Harga indikatif
                                        </label>

                                        <div class="mt-2 flex gap-2">
                                            <div class="w-[110px] shrink-0">
                                                <x-admin.select model="currency" :value="$currency"
                                                                label="Mata uang" :nullable="false"
                                                                :options="collect(['USD', 'EUR', 'IDR', 'SGD', 'JPY', 'CNY'])
                                                                    ->map(fn ($k) => ['nilai' => $k, 'label' => $k])->all()" />
                                            </div>

                                            <input type="number" step="0.01" min="0"
                                                   wire:model="indicative_price" id="produk-indicative_price"
                                                   placeholder="mis. 4250.00"
                                                   class="admin-control tabular-nums">
                                        </div>

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Perkiraan harga per satuan, dasar FOB. Dikosongkan berarti
                                            halaman produk publik tidak menampilkan harga sama sekali —
                                            pembeli diarahkan mengirim inquiry.
                                        </p>

                                        @error('indicative_price')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror

                                        @error('currency')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ── Incoterms ────────────────────────────────
                                     Daftar centang, bukan kolom ketik bebas. Kode
                                     incoterms itu istilah baku — ada sebelas
                                     seluruhnya — dan mengetiknya sendiri berarti
                                     salah ketik masuk ke katalog publik tanpa ada
                                     yang menahan.

                                     Nilainya tetap disimpan persis seperti
                                     sebelumnya: satu untai berpemisah koma di
                                     kolom incoterms, jadi tidak ada yang berubah
                                     di sisi basis data. --}}
                                <div class="mt-4"
                                     x-data="{
                                         urutan: @js($urutanIncoterms),
                                         pilihan: @js($incotermsTerpilih->all()),

                                         ubah(kode, aktif) {
                                             const set = new Set(this.pilihan)
                                             aktif ? set.add(kode) : set.delete(kode)

                                             /* Diurutkan ulang menurut urutan bakunya,
                                                bukan menurut urutan mencentang — supaya
                                                dua produk dengan pilihan yang sama
                                                tersimpan dengan untai yang sama pula. */
                                             this.pilihan = this.urutan.filter(k => set.has(k))

                                             $wire.$set('incoterms', this.pilihan.join(','))
                                         },
                                     }">

                                    <span class="block text-[12px] font-semibold text-ink-faint">
                                        Incoterms yang dilayani <span class="text-brand">*</span>
                                    </span>

                                    <div class="mt-2 space-y-2">
                                        @foreach($daftarIncoterms as $kode => $kepanjangan)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-control border
                                                          border-line px-3.5 py-2.5 transition-colors
                                                          hover:border-line-strong
                                                          has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">
                                                <input type="checkbox" value="{{ $kode }}"
                                                       x-bind:checked="pilihan.includes('{{ $kode }}')"
                                                       x-on:change="ubah('{{ $kode }}', $event.target.checked)"
                                                       @checked($incotermsTerpilih->contains($kode))
                                                       class="h-4 w-4 shrink-0 cursor-pointer accent-brand">

                                                <span class="min-w-0">
                                                    <span class="text-[13px] font-semibold text-ink">{{ $kode }}</span>
                                                    <span class="text-[13px] text-ink-muted"> — {{ $kepanjangan }}</span>
                                                </span>
                                            </label>
                                        @endforeach

                                        {{-- Kode di luar daftar baku, dibawa dari data
                                             lama. Tetap bisa dilepas, tapi tidak bisa
                                             dipasang lagi lewat layar ini. --}}
                                        @foreach($incotermsAsing as $kode)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-control border
                                                          border-line px-3.5 py-2.5 transition-colors
                                                          hover:border-line-strong
                                                          has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">
                                                <input type="checkbox" value="{{ $kode }}"
                                                       x-bind:checked="pilihan.includes('{{ $kode }}')"
                                                       x-on:change="ubah('{{ $kode }}', $event.target.checked)"
                                                       @checked(true)
                                                       class="h-4 w-4 shrink-0 cursor-pointer accent-brand">

                                                <span class="min-w-0">
                                                    <span class="text-[13px] font-semibold text-ink">{{ $kode }}</span>
                                                    <span class="text-[13px] text-ink-faint"> — di luar daftar baku</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('incoterms')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>

                            {{-- ── Kartu: spesifikasi teknis ────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Spesifikasi teknis
                                    </h3>

                                    <span class="text-[12px] text-ink-faint">Opsional</span>
                                </div>

                                @if(count($specifications) > 0)
                                    {{-- Judul kolomnya ditulis sekali di atas, bukan
                                         diulang sebagai label di tiap baris: di daftar
                                         yang bisa jadi sepuluh baris, label yang
                                         berulang lebih panjang dari isinya sendiri. --}}
                                    <div class="mb-2 hidden gap-3 px-1 sm:flex">
                                        <span class="flex-1 text-[11px] font-bold uppercase tracking-[0.06em] text-ink-faint">Nama</span>
                                        <span class="flex-1 text-[11px] font-bold uppercase tracking-[0.06em] text-ink-faint">Nilai</span>
                                        <span class="w-8 shrink-0"></span>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($specifications as $index => $spec)
                                            <div class="flex items-start gap-3" wire:key="spec-{{ $index }}">
                                                <input type="text" wire:model="specifications.{{ $index }}.key"
                                                       aria-label="Nama spesifikasi baris {{ $index + 1 }}"
                                                       placeholder="mis. Kadar air" class="admin-control flex-1">

                                                <input type="text" wire:model="specifications.{{ $index }}.value"
                                                       aria-label="Nilai spesifikasi baris {{ $index + 1 }}"
                                                       placeholder="mis. Maks 12%" class="admin-control flex-1">

                                                <button type="button" wire:click="removeSpecification({{ $index }})"
                                                        aria-label="Hapus baris spesifikasi {{ $index + 1 }}"
                                                        class="inline-flex h-[42px] w-8 shrink-0 items-center justify-center
                                                               rounded-control text-ink-faint transition-colors
                                                               hover:bg-danger/10 hover:text-danger">
                                                    <x-icon.admin name="close" size="h-4 w-4" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="rounded-control border border-dashed border-line-strong bg-mist
                                              px-4 py-3.5 text-[13px] leading-relaxed text-ink-faint">
                                        Belum ada spesifikasi. Baris yang namanya atau nilainya kosong
                                        tidak akan tersimpan.
                                    </p>
                                @endif

                                <button type="button" wire:click="addSpecification"
                                        class="admin-btn admin-btn-quiet mt-3">
                                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                              stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Tambah spesifikasi
                                </button>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: kategori & sertifikasi ────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Kategori &amp; sertifikasi
                                </h3>

                                {{-- Keduanya menjawab pertanyaan yang sama: produk ini
                                     masuk golongan apa, dan dijamin oleh siapa. --}}
                                <div>
                                    <label class="block text-[12px] font-semibold text-ink-faint">
                                        Kategori <span class="text-brand">*</span>
                                    </label>

                                    <x-admin.select model="category_id" :value="$category_id" class="mt-2"
                                                    label="Kategori produk" placeholder="Pilih kategori"
                                                    :options="$categories->map(fn ($k) => [
                                                        'nilai' => $k->id,
                                                        'label' => ($n = $k->translated_name) ? $n : $k->slug,
                                                    ])->all()" />

                                    @error('category_id')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Tanpa garis pembatas: keduanya sudah dipisahkan jarak dan
                                     judulnya sendiri, dan garis di dalam kartu yang
                                     sudah berbingkai membuat satu kartu terbaca
                                     seperti dua. --}}
                                <div class="mt-5">
                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                                        <span class="text-[12px] font-semibold text-ink-faint">Sertifikasi terkait</span>

                                        <span class="text-[12px] text-ink-faint">
                                            {{ count($selectedCertifications) }} dipilih
                                        </span>
                                    </div>

                                    @if($certifications->isEmpty())
                                        <p class="rounded-control border border-dashed border-line-strong bg-mist
                                                  px-4 py-3.5 text-[13px] leading-relaxed text-ink-faint">
                                            Belum ada sertifikasi yang bisa ditautkan.
                                        </p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($certifications as $cert)
                                                @php $namaCert = $cert->translated_name; @endphp

                                                <label class="flex cursor-pointer items-center gap-3 rounded-control border
                                                              border-line px-3.5 py-2.5 transition-colors
                                                              hover:border-line-strong
                                                              has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">
                                                    {{-- in_array longgar, bukan ketat: nilai yang
                                                         kembali dari peramban selalu untai, sedang
                                                         yang dari basis data bisa bertipe lain. --}}
                                                    <input type="checkbox" wire:model="selectedCertifications"
                                                           value="{{ $cert->id }}"
                                                           @checked(in_array($cert->id, $selectedCertifications))
                                                           class="h-4 w-4 shrink-0 cursor-pointer accent-brand">

                                                    <span class="min-w-0 truncate text-[13px] text-ink"
                                                          title="{{ $namaCert ?: $cert->slug }}">{{ $namaCert ?: $cert->slug }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </section>

                            {{-- ── Kartu: gambar ────────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Gambar produk
                                </h3>

                                {{-- Petak berisi gambar yang sudah ada, gambar yang
                                     baru dipilih tapi belum tersimpan, dan sebuah
                                     ubin bergaris putus-putus untuk menambah.

                                     Tombol tambahnya duduk DI DALAM petak yang sama,
                                     bukan berdiri sebagai kolom berkas terpisah di
                                     bawahnya. Bentuknya jadi mengatakan sendiri apa
                                     yang akan terjadi: kotak kosong di deretan kotak
                                     berisi — yang akan mengisinya adalah gambar
                                     berikutnya. --}}
                                <div class="grid grid-cols-2 gap-3">

                                    @if($editingId && count($existingMedia) > 0)
                                        @foreach($existingMedia as $media)
                                            @php $sampul = (bool) $media->getCustomProperty('is_cover'); @endphp

                                            <div @class([
                                                'group relative aspect-square overflow-hidden rounded-control border',
                                                'border-brand ring-1 ring-brand/30' => $sampul,
                                                'border-line'                       => ! $sampul,
                                            ]) wire:key="media-{{ $media->id }}">

                                                <img src="{{ $media->getUrl() }}" alt=""
                                                     class="block h-full w-full bg-mist object-cover">

                                                @if($sampul)
                                                    <span class="absolute left-2 top-2 inline-flex items-center gap-1
                                                                 rounded-full bg-brand px-2 py-0.5 text-[10px]
                                                                 font-bold text-white">
                                                        <x-icon.admin name="star" size="h-2.5 w-2.5" />
                                                        Sampul
                                                    </span>
                                                @endif

                                                {{-- Tombolnya muncul saat disorot, tapi TETAP
                                                     terjangkau papan tik lewat focus-within —
                                                     kalau hanya bergantung pada hover, gambarnya
                                                     tidak bisa dikelola tanpa tetikus. --}}
                                                <div class="absolute inset-x-0 bottom-0 flex gap-1.5 bg-gradient-to-t
                                                            from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                            group-hover:opacity-100 group-focus-within:opacity-100">
                                                    @unless($sampul)
                                                        <button type="button" wire:click="setCoverMedia({{ $media->id }})"
                                                                class="flex-1 rounded-control bg-white/90 px-2 py-1
                                                                       text-[11px] font-semibold text-ink
                                                                       transition-colors hover:bg-white">
                                                            Jadikan sampul
                                                        </button>
                                                    @endunless

                                                    <button type="button" wire:click="deleteMedia({{ $media->id }})"
                                                            wire:confirm="Hapus gambar ini?"
                                                            aria-label="Hapus gambar"
                                                            class="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                   justify-center rounded-control bg-white/90 text-danger
                                                                   transition-colors hover:bg-white">
                                                        <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Berkas yang baru dipilih tapi belum tersimpan.
                                         Ditampilkan supaya memilih berkas tidak terasa
                                         seperti tidak terjadi apa-apa sampai Simpan
                                         ditekan.

                                         temporaryUrl() dibungkus try: ia melempar galat
                                         untuk berkas yang bukan gambar, dan atribut
                                         accept di kolom berkas cuma menyaring tampilan
                                         penjelajah berkas — bukan jaminan. --}}
                                    @foreach($imageFiles ?? [] as $i => $berkas)
                                        <div class="relative aspect-square overflow-hidden rounded-control
                                                    border border-dashed border-brand/50 bg-brand-wash"
                                             wire:key="baru-{{ $i }}">
                                            @php
                                                $pratinjau = null;
                                                try {
                                                    $pratinjau = $berkas->temporaryUrl();
                                                } catch (\Throwable $e) {
                                                    $pratinjau = null;
                                                }
                                            @endphp

                                            @if($pratinjau)
                                                <img src="{{ $pratinjau }}" alt=""
                                                     class="block h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center px-3
                                                             text-center text-[11px] leading-snug text-ink-muted">
                                                    {{ $berkas->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endforeach

                                    {{-- Ubin tambah. <label> yang membungkus kolom
                                         berkasnya: kotak berkas bawaan peramban tidak
                                         bisa ditata isinya, jadi ia disembunyikan dan
                                         ubin inilah yang jadi wajahnya. --}}
                                    <label title="Tambah gambar produk"
                                           class="flex aspect-square cursor-pointer items-center justify-center
                                                  rounded-control border-2 border-dashed border-line-strong
                                                  bg-mist/40 text-ink-faint transition-colors
                                                  hover:border-brand hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="imageFiles" id="gambar-produk"
                                               multiple accept="image/*"
                                               aria-label="Tambah gambar produk" class="sr-only">

                                        <span wire:loading.remove wire:target="imageFiles">
                                            <svg class="h-9 w-9" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <svg wire:loading wire:target="imageFiles"
                                             class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                                    Boleh beberapa sekaligus, maksimal 3 MB per berkas.
                                    Semuanya diubah otomatis ke WebP.
                                </p>

                                @error('imageFiles.*')
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

                                        {{-- :nullable="false" — produk selalu punya
                                             salah satu dari dua keadaan ini. --}}
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status penerbitan" placeholder="Terbit"
                                                        :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'published', 'label' => 'Terbit'],
                                                            ['nilai' => 'draft',     'label' => 'Draf'],
                                                        ]" />
                                    </div>

                                    {{-- Sakelar geser, bukan kotak centang.
                                         Keduanya sama-sama sah, tapi maknanya berbeda:
                                         kotak centang berarti "pilih ini", sakelar
                                         berarti "nyalakan ini" — dan produk unggulan
                                         memang sebuah keadaan yang menyala atau mati,
                                         bukan pilihan dari sederet pilihan.

                                         Di baliknya tetap kotak centang sungguhan yang
                                         disembunyikan, bukan tombol ber-wire:click.
                                         Dua alasannya: wire:model tidak perlu memanggil
                                         server tiap kali digeser, dan kotak centang
                                         asli sudah bisa dijangkau papan tik serta
                                         diumumkan pembaca layar tanpa dibuatkan
                                         peniruannya. role="switch" mengubah cara
                                         mengumumkannya jadi "menyala/mati", bukan
                                         "tercentang".

                                         Seluruh rona — jalur, bulatannya, dan latar
                                         barisnya — digerakkan peer-checked dan
                                         has-[:checked] di CSS, bukan oleh nilai di
                                         sisi PHP: wire:model di sini bersifat tunda,
                                         jadi nilai di server baru menyusul pada
                                         permintaan berikutnya. --}}
                                    <label class="flex cursor-pointer items-start justify-between gap-4
                                                  rounded-control border border-line p-3.5 transition-colors
                                                  hover:border-line-strong
                                                  has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">

                                        <span class="min-w-0">
                                            <span class="block text-[13px] font-semibold text-ink">Produk unggulan</span>
                                            <span class="mt-0.5 block text-[12px] leading-relaxed text-ink-muted">
                                                Ditampilkan lebih dulu di katalog situs publik.
                                            </span>
                                        </span>

                                        <span class="relative mt-0.5 inline-flex shrink-0 items-center">
                                            {{-- @checked() WAJIB. wire:model yang tertunda tidak
                                                 memancarkan atribut checked, sedangkan seluruh
                                                 gambar sakelar ini bergantung pada peer-checked.
                                                 Tanpa itu, membuka produk unggulan untuk diubah
                                                 menampilkan sakelar mati, lalu tersimpan mati. --}}
                                            <input type="checkbox" role="switch" wire:model="is_featured"
                                                   @checked($is_featured)
                                                   class="peer sr-only">

                                            {{-- Jalurnya --}}
                                            <span class="block h-6 w-11 rounded-full bg-mist-deep transition-colors
                                                         peer-checked:bg-brand
                                                         peer-focus-visible:ring-2 peer-focus-visible:ring-brand
                                                         peer-focus-visible:ring-offset-2"></span>

                                            {{-- Bulatannya --}}
                                            <span class="pointer-events-none absolute left-0.5 block h-5 w-5 rounded-full
                                                         bg-white shadow-[0_1px_3px_rgba(26,29,27,0.28)]
                                                         transition-transform peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>
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
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan produk' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
