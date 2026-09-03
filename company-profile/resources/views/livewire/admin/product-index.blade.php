<div class="mx-auto max-w-[1400px]">

    @php
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        $sebutanStatus = ['published' => 'Aktif', 'draft' => 'Draf'];

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $sebutanStatus[$selectedStatus] ?? $selectedStatus,
                'props' => ['selectedStatus'],
            ]);
        }
        
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
            <h1 class="text-admin-display text-ink">
                Produk
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
                Katalog produk ekspor yang tampil di situs publik.
            </p>
        </div>
        
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
    <div class="card overflow-visible">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="product" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-ink">Daftar produk</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">Kelola dan pantau produk yang tersedia.</p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($products->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'produk' }}
            </span>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             PENYARING
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="px-5 pt-5">
            <div class="rounded-corner border border-line">
                
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
                                        ['nilai' => 'published', 'label' => 'Aktif'],
                                        ['nilai' => 'draft',     'label' => 'Draf'],
                                    ]" />
                    
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
                                    x-on:click="{{ $bersihkan(['search', 'selectedCategory', 'selectedStatus', 'selectedFeatured']) }}"
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
             wire:target="search, selectedCategory, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        
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

                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $nama     = $product->translated_name ?: $product->slug;
                                    $kategori = $product->category?->translated_name;
                                    
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

                                    {{-- Produk --}}
                                    <td class="py-4 pl-5 pr-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            
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
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>
                                                
                                                <span class="mt-0.5 block truncate text-admin-caption tabular-nums text-ink-faint"
                                                      title="Kode HS: {{ $product->hs_code }}">
                                                    {{ $product->hs_code ? 'HS ' . $product->hs_code : 'Tanpa kode HS' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        
                                        <span @class([
                                            'block truncate',
                                            'text-admin-strong text-ink' => filled($kategori),
                                            'text-admin-body text-ink-faint' => blank($kategori),
                                        ]) title="{{ $kategori }}">{{ $kategori ?: 'Tanpa kategori' }}</span>
                                    </td>
                                    
                                    <td class="px-3 py-4 align-middle">
                                        
                                        <span @class([
                                            'block truncate tabular-nums',
                                            'text-admin-strong text-ink'     => filled($product->moq),
                                            'text-admin-body text-ink-faint' => blank($product->moq),
                                        ]) title="MOQ: {{ $product->moq }}">{{ $product->moq ?: '—' }}</span>

                                        <span class="mt-0.5 block truncate text-admin-caption tabular-nums text-ink-faint"
                                              title="Kapasitas: {{ $product->supply_capacity }}">{{ $product->supply_capacity ?: '—' }}</span>
                                    </td>
                                    
                                    <td class="px-3 py-4 align-middle">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <x-admin.status-pill :status="$product->status" konteks="produk" />
                                            
                                            @if($product->is_featured)
                                                <span class="flex h-6 w-6 shrink-0 items-center justify-center
                                                             rounded-full bg-brand text-white"
                                                      title="Ditampilkan sebagai produk unggulan di situs publik">
                                                    <x-icon.admin name="star" size="h-3.5 w-3.5" />
                                                    <span class="sr-only">Produk unggulan</span>
                                                </span>
                                            @endif
                                        </div>
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

<x-admin.confirm-delete metode="delete"
                                                                    :id="$product->id"
                                                                    :nama="$nama ?: $product->slug"
                                                                    judul="Hapus produk?"
                                                                    tombol="Ya, hapus produk">
                                                Terjemahan, spesifikasi, dan seluruh gambarnya ikut terhapus.
                                            </x-admin.confirm-delete>
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
                                            <p class="mt-4 text-admin-title text-ink">
                                                Tidak ada produk yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
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
                                            <p class="mt-4 text-admin-title text-ink">
                                                Belum ada produk
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
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
            
            $wajib = ['category_id', 'name_en', 'name_id', 'hs_code', 'moq',
                      'supply_capacity', 'packaging', 'origin', 'incoterms'];
            
            $galatEn = $errors->hasAny(['name_en', 'description_en']);
            $galatId = $errors->hasAny(['name_id', 'description_id']);
            
            $daftarIncoterms = [
                'FOB' => 'Free On Board',
                'CIF' => 'Cost, Insurance and Freight',
                'CFR' => 'Cost and Freight',
            ];

            $incotermsTerpilih = collect(explode(',', (string) $incoterms))
                ->map(fn ($x) => strtoupper(trim($x)))
                ->filter()->unique()->values();
            
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
            
            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[1000px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-5">
                        <div class="flex min-w-0 items-center gap-3.5">
                            
                            <span class="flex h-13 w-13 shrink-0 items-center justify-center rounded-corner
                                         bg-brand-wash text-brand">
                                <x-icon.admin name="product" size="h-6 w-6" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-produk"
                                    class="truncate text-admin-display text-ink">
                                    {{ $editingId ? 'Ubah produk' : 'Tambah produk' }}
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

                    {{-- ── Dua kolom ── --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: informasi produk ── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="product" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-ink">Informasi Produk</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Nama dan deskripsi produk dalam dua bahasa.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex shrink-0 flex-wrap items-center gap-2">

                                        <div class="inline-flex shrink-0 items-center gap-0.5 rounded-full
                                                    border border-line bg-mist p-0.5"
                                             role="group" aria-label="Bahasa yang sedang disunting">
                                            @foreach(['id' => 'Indonesia', 'en' => 'English'] as $kode => $sebutan)
                                                @php $bergalat = ($kode === 'en' && $galatEn) || ($kode === 'id' && $galatId); @endphp

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
                                                    
                                                    @if($bergalat)
                                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-danger"
                                                              title="Ada isian yang perlu diperbaiki di sini"></span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                        
                                        <button type="button" wire:click="autoTranslate"
                                                wire:loading.attr="disabled" wire:target="autoTranslate"
                                                title="Salin isian Indonesia ke English, lalu terjemahkan"
                                                class="admin-btn admin-btn-quiet shrink-0 !py-1.5 disabled:opacity-60">
                                            <svg wire:loading wire:target="autoTranslate"
                                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16"
                                                 fill="none" aria-hidden="true">
                                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                            </svg>

                                            <x-icon.admin name="send" size="h-3.5 w-3.5"
                                                          class="shrink-0 text-brand"
                                                          wire:loading.remove wire:target="autoTranslate" />

                                            Terjemahkan
                                        </button>
                                    </div>
                                </div>

                                <div class="p-5">
                                
                                @if($galatTerjemah)
                                    <p class="mb-4 flex items-start gap-1.5 text-admin-caption text-danger" role="alert">
                                        <svg class="mt-px h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                            <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                            <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                                        </svg>
                                        {{ $galatTerjemah }}
                                    </p>
                                @endif

                                <div class="space-y-4">

                                    
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">
                                            Nama produk <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="name_en"
                                                   aria-label="Nama produk dalam bahasa Inggris"
                                                   placeholder="Nama produk dalam bahasa Inggris"
                                                   class="admin-control">
                                            @error('name_en')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="name_id"
                                                   aria-label="Nama produk dalam bahasa Indonesia"
                                                   placeholder="Nama produk dalam bahasa Indonesia"
                                                   class="admin-control">
                                            @error('name_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Deskripsi — mengikuti tab. --}}
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">Deskripsi</label>

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
                            </div>
                            </section>

                            {{-- ── Kartu: syarat dagang & ekspor ────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="market" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-ink">Ketentuan Ekspor</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Detail produk untuk kebutuhan ekspor.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                @php
                                    
                                    $dagang = [
                                        ['origin',          'Asal',             'mis. Manggarai, Flores, NTT (1.300 – 1.700 mdpl)'],
                                        ['hs_code',         'Kode HS',          '0901.11.10'],
                                        ['moq',             'MOQ',              'mis. 1 x 20ft container'],
                                        ['supply_capacity', 'Kapasitas suplai', 'mis. 100 ton / bulan'],
                                        ['packaging',       'Kemasan',          'mis. Karung goni 60 kg'],
                                    ];
                                @endphp
                                
                                <div class="space-y-4">
                                    @foreach($dagang as [$kolom, $sebutan, $contoh])
                                        <div>
                                            <label for="produk-{{ $kolom }}"
                                                   class="block text-admin-label text-ink-faint">
                                                {{ $sebutan }} <span class="text-brand">*</span>
                                            </label>

                                            <input type="text" wire:model="{{ $kolom }}" id="produk-{{ $kolom }}"
                                                   placeholder="{{ $contoh }}" class="admin-control mt-2">

                                            @error($kolom)
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach

                                    {{-- ── Harga indikatif ── --}}
                                    <div>
                                        <label for="produk-indicative_price"
                                               class="block text-admin-label text-ink-faint">
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
                                        
                                        @error('indicative_price')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror

                                        @error('currency')
                                            <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ── Incoterms ── --}}
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

                                    <span class="block text-admin-label text-ink-faint">
                                        Incoterms yang dilayani <span class="text-brand">*</span>
                                    </span>
                                    
                                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                        @foreach($daftarIncoterms as $kode => $kepanjangan)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-control border
                                                          border-line px-3.5 py-2.5 transition-colors
                                                          hover:border-line-strong
                                                          has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">
                                                <x-admin.checkbox value="{{ $kode }}"
                                                                  x-bind:checked="pilihan.includes('{{ $kode }}')"
                                                                  x-on:change="ubah('{{ $kode }}', $event.target.checked)"
                                                                  :checked="$incotermsTerpilih->contains($kode)"
                                                                  class="cursor-pointer" />

                                                <span class="min-w-0">
                                                    <span class="text-admin-strong text-ink">{{ $kode }}</span>
                                                    <span class="text-admin-body text-ink-muted"> — {{ $kepanjangan }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                        
                                        @foreach($incotermsAsing as $kode)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-control border
                                                          border-line px-3.5 py-2.5 transition-colors
                                                          hover:border-line-strong
                                                          has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">
                                                <x-admin.checkbox value="{{ $kode }}"
                                                                  x-bind:checked="pilihan.includes('{{ $kode }}')"
                                                                  x-on:change="ubah('{{ $kode }}', $event.target.checked)"
                                                                  checked
                                                                  class="cursor-pointer" />

                                                <span class="min-w-0">
                                                    <span class="text-admin-strong text-ink">{{ $kode }}</span>
                                                    <span class="text-admin-body text-ink-faint"> — di luar daftar baku</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('incoterms')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            </section>

                            {{-- ── Kartu: spesifikasi teknis ── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="page" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-ink">Spesifikasi Teknis</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Informasi teknis dan karakteristik produk.</p>
                                        </div>
                                    </div>


                                    <span class="text-admin-label text-ink-faint">Opsional</span>
                                </div>

                                <div class="p-5">

                                @if(count($specifications) > 0)
                                    
                                    <div class="mb-2 hidden gap-3 px-1 sm:flex">
                                        <span class="flex-1 text-admin-overline uppercase text-ink-faint">Nama</span>
                                        <span class="flex-1 text-admin-overline uppercase text-ink-faint">Nilai</span>
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
                                              px-4 py-3.5 text-admin-body text-ink-faint">
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
                            </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: kategori & sertifikasi ── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="category" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-ink">Kategori &amp; Sertifikasi</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Pengelompokan dan sertifikasi produk.</p>
                                    </div>
                                </div>

                                <div class="p-5">
                                
                                <div>
                                    <label class="block text-admin-label text-ink-faint">
                                        Kategori <span class="text-brand">*</span>
                                    </label>

                                    <x-admin.select model="category_id" :value="$category_id" class="mt-2"
                                                    label="Kategori produk" placeholder="Pilih kategori"
                                                    :options="$categories->map(fn ($k) => [
                                                        'nilai' => $k->id,
                                                        'label' => ($n = $k->translated_name) ? $n : $k->slug,
                                                    ])->all()" />

                                    @error('category_id')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="mt-5">
                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                                        <span class="text-admin-label text-ink-faint">Sertifikasi terkait</span>

                                        <span class="text-admin-label text-ink-faint">
                                            {{ count($selectedCertifications) }} dipilih
                                        </span>
                                    </div>

                                    @if($certifications->isEmpty())
                                        <p class="rounded-control border border-dashed border-line-strong bg-mist
                                                  px-4 py-3.5 text-admin-body text-ink-faint">
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
                                                    
                                                    <x-admin.checkbox wire:model="selectedCertifications"
                                                                      value="{{ $cert->id }}"
                                                                      :checked="in_array($cert->id, $selectedCertifications)" />

                                                    <span class="min-w-0 truncate text-admin-body text-ink"
                                                          title="{{ $namaCert ?: $cert->slug }}">{{ $namaCert ?: $cert->slug }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            </section>

                            {{-- ── Kartu: gambar ── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-ink">Gambar</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Tambahkan gambar untuk menampilkan produk.</p>
                                    </div>
                                </div>

                                <div class="p-5">
                                
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
                                                                 rounded-full bg-brand px-2 py-0.5 text-admin-caption
                                                                 font-bold text-white">
                                                        <x-icon.admin name="star" size="h-3 w-3" />
                                                        Sampul
                                                    </span>
                                                @endif
                                                
                                                <div class="absolute inset-x-0 bottom-0 flex gap-1.5 bg-gradient-to-t
                                                            from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                            group-hover:opacity-100 group-focus-within:opacity-100">
                                                    @unless($sampul)
                                                        <button type="button" wire:click="setCoverMedia({{ $media->id }})"
                                                                class="flex-1 rounded-control bg-white/90 px-2 py-1
                                                                       text-admin-caption font-semibold text-ink
                                                                       transition-colors hover:bg-white">
                                                            Jadikan sampul
                                                        </button>
                                                    @endunless

<x-admin.confirm-delete metode="deleteMedia"
                                                                            :id="$media->id"
                                                                            judul="Hapus gambar produk?"
                                                                            tombol="Ya, hapus gambar"
                                                                            ikon="h-3.5 w-3.5"
                                                                            kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-control bg-white/90 text-danger transition-colors hover:bg-white">
                                                        Berkasnya ikut terhapus dari penyimpanan, dan produk ini kehilangan satu gambarnya di katalog.
                                                    </x-admin.confirm-delete>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
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
                                                             text-center text-admin-caption text-ink-muted">
                                                    {{ $berkas->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-admin-caption font-semibold text-white">Baru</span>
                                        </div>
                                    @endforeach
                                    
                                    <x-admin.upload-tile model="imageFiles"
                                                         id="gambar-produk"
                                                         multiple
                                                         judul="Tambah gambar produk"
                                                         label="Tambah gambar" />
                                </div>

                                <p class="mt-3 text-admin-caption text-ink-faint">
                                    Maksimal 3 MB.
                                    Semuanya diubah otomatis ke WebP.
                                </p>

                                @error('imageFiles.*')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            </section>

                            {{-- ── Kartu: penerbitan ── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="manage" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-ink">Publikasi Produk</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Tentukan status dan penayangan produk.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">Status</label>
                                        
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status penerbitan" placeholder="Aktif"
                                                        :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'published', 'label' => 'Aktif'],
                                                            ['nilai' => 'draft',     'label' => 'Draf'],
                                                        ]" />
                                    </div>
                                    
                                    <label class="group flex cursor-pointer items-center gap-3 rounded-control
                                                  border border-line px-3.5 py-3 transition-colors
                                                  hover:border-line-strong
                                                  has-[:checked]:border-brand has-[:checked]:bg-brand-wash">
                                        
                                        <x-admin.checkbox wire:model="is_featured" :checked="$is_featured"
                                                          class="cursor-pointer" />

                                        <span class="min-w-0 flex-1">
                                            <span class="block text-admin-strong text-ink">Produk unggulan</span>
                                        </span>

                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                                                     bg-mist-deep text-ink-faint transition-colors
                                                     group-has-[:checked]:bg-brand group-has-[:checked]:text-white"
                                              title="Beginilah produk ini tampil di daftar produk">
                                            <x-icon.admin name="star" size="h-3.5 w-3.5" />
                                        </span>
                                    </label>
                                </div>
                            </div>
                            </section>
                        </div>
                    </div>

                    {{-- ── Kaki ── --}}
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
