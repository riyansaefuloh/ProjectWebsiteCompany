<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * Inquiry dan Produk.
         */
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
            <h1 class="text-admin-display text-ink">
                Kategori
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
                Pengelompokan produk yang dipakai katalog di situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah kategori
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
    {{-- overflow-visible: menu turun penyaring di dalamnya melayang keluar
         dari bingkai kartu, dan .card membawa overflow-hidden yang akan
         memotongnya tepat di garis bawah kartu. --}}
    <div class="card overflow-visible">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                {{-- Keping lambang 36px berlatar hijau muda dengan ikon 18px — sama
                     persis dengan kepala kartu di dasbor, halaman Inquiry, dan
                     halaman Produk. --}}
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="category" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-ink">Daftar kategori</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Urut menurut nomor urutan tampilnya di katalog.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($categories->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'kategori' }}
            </span>
        </div>

        {{-- ══ PENYARING — berdiri di DALAM kartu, tepat di atas tabelnya,
             berbingkai sendiri seperti tabelnya. ══ --}}
        <div class="px-5 pt-5">
            <div class="rounded-corner border border-line">

                {{-- Dua kendali berbagi satu baris — pencarian dua pertiga,
                     status sepertiga. Produk memberi pencarian baris sendiri
                     karena di sana penyaringnya tiga. --}}
                <div class="grid gap-4 p-5 lg:grid-cols-3">

                    <div class="relative lg:col-span-2">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                            <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                        </span>

                        <input type="search" wire:model.live="search" id="cari-kategori"
                               aria-label="Cari kategori"
                               placeholder="Cari nama kategori…"
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
                        $kolom = [
                            ['label' => 'Kategori', 'lebar' => 'w-[40%]', 'rata' => 'text-left'],
                            ['label' => 'Ikon',     'lebar' => 'w-[18%]', 'rata' => 'text-left'],
                            ['label' => 'Urutan',   'lebar' => 'w-[12%]', 'rata' => 'text-left'],
                            ['label' => 'Status',   'lebar' => 'w-[14%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',     'lebar' => 'w-[16%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[820px] table-fixed">
                        @if($categories->isNotEmpty())
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
                            @forelse($categories as $cat)
                                @php
                                    $nama = $cat->translated_name ?: $cat->slug;

                                    /* getFirstMedia(), bukan getFirstMediaUrl(): yang
                                       kedua mengembalikan untai kosong saat tidak ada
                                       berkasnya, dan untai kosong di src membuat
                                       peramban memuat ulang HALAMAN ini sebagai gambar. */
                                    $berkas = $cat->getFirstMedia('icon');

                                    $alamatGambar = $berkas
                                        ? ($berkas->hasGeneratedConversion('thumb')
                                            ? $berkas->getUrl('thumb')
                                            : $berkas->getUrl())
                                        : null;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Garis tepi kiri dihapus — keadaan
                                         aktif sudah terbaca dari pil status
                                         di kolom Status. --}}
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
                                                      title="Kategori ini belum punya gambar">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>

                                                {{-- Slug jadi baris kedua: itulah yang muncul
                                                     di alamat halaman katalog, jadi ia yang
                                                     dicocokkan saat menelusuri tautan. --}}
                                                <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                      title="Slug: {{ $cat->slug }}">{{ $cat->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if(filled($cat->icon))
                                            <span class="inline-flex max-w-full items-center rounded-control border
                                                         border-line bg-mist px-2.5 py-1 text-admin-caption text-ink-muted"
                                                  title="Kelas ikon: {{ $cat->icon }}">
                                                <span class="min-w-0 truncate">{{ $cat->icon }}</span>
                                            </span>
                                        @else
                                            <span class="text-admin-body text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-control
                                                     bg-mist px-2.5 text-admin-caption font-semibold tabular-nums text-ink-muted"
                                              title="Urutan tampil: {{ $cat->sort_order }}">{{ $cat->sort_order }}</span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$cat->status" />
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $cat->id }}')"
                                                    title="Ubah {{ $nama }}"
                                                    aria-label="Ubah {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasan menyebut akibatnya,
                                                 bukan sekadar "yakin?":
                                                 produk yang memakai kategori
                                                 ini ikut kehilangan
                                                 golongannya. --}}
                                            <x-admin.confirm-delete :label="'Hapus ' . $nama"
                                                                    judul="Hapus kategori ini?"
                                                                    tombol="Ya, hapus kategori"
                                                                    :id="$cat->id">
                                                Terjemahan dan gambarnya ikut terhapus, dan produk yang
                                                memakai kategori ini kehilangan kategorinya.
                                            </x-admin.confirm-delete>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'category'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-admin-title text-ink">
                                                Tidak ada kategori yang cocok
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
                                            <p class="mt-4 text-admin-title text-ink">
                                                Belum ada kategori
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Kategori dipakai untuk mengelompokkan produk di katalog
                                                situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah kategori pertama
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
            {{ $categories->links('vendor.pagination.admin', ['satuan' => 'kategori']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH KATEGORI
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            // Galat yang jatuh di tab yang sedang tertutup tidak terlihat sama
            // sekali; tabnya diberi titik merah supaya ketahuan.
            $galatEn = $errors->hasAny(['name_en', 'description_en']);
            $galatId = $errors->hasAny(['name_id', 'description_id']);
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-kategori">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            {{-- Lebarnya 900px, bukan 1100 seperti modal produk: isinya jauh
                 lebih sedikit, dan panel selebar itu cuma menyisakan rongga
                 kosong di kanan tiap kartunya. --}}
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
                                <x-icon.admin name="category" size="h-6 w-6" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-kategori"
                                    class="truncate text-admin-display text-ink">
                                    {{ $editingId ? 'Ubah kategori' : 'Tambah kategori' }}
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
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="category" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-ink">Informasi Kategori</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Nama dan deskripsi kategori dalam dua bahasa.</p>
                                        </div>
                                    </div>

                                    {{-- Tab bahasa dan tombol Terjemahkan
                                         berdiri TERPISAH: kendali bersegmen
                                         menjanjikan "pilih salah satu", dan
                                         tombol tindakan di bingkai yang sama
                                         mengingkari janji itu. --}}
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

                                                    {{-- Titik merah: tab ini menyimpan galat
                                                         yang tidak terlihat karena tertutup. --}}
                                                    @if($bergalat)
                                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-danger"
                                                              title="Ada isian yang perlu diperbaiki di sini"></span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>

                                        {{-- Tombol tindakan, bukan pilihan —
                                             memakai bentuk tombol panel yang
                                             baku. --}}
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

                                {{-- Pesan gagal-terjemah, tepat di bawah
                                     tombol yang memicunya — sebelumnya ia
                                     gagal tanpa mengatakan apa pun. --}}
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

                                    {{-- Nama kategori mengikuti tab. Keduanya
                                         TETAP di DOM dan yang tidak aktif
                                         hanya disembunyikan: isian yang
                                         elemennya lenyap membuat Livewire
                                         kehilangan nilainya. --}}
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">
                                            Nama kategori <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="name_en"
                                                   aria-label="Nama kategori dalam bahasa Inggris"
                                                   placeholder="Nama kategori dalam bahasa Inggris"
                                                   class="admin-control">
                                            @error('name_en')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="name_id"
                                                   aria-label="Nama kategori dalam bahasa Indonesia"
                                                   placeholder="Nama kategori dalam bahasa Indonesia"
                                                   class="admin-control">
                                            @error('name_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Deskripsi — mengikuti tab. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-admin-label text-ink-faint">Deskripsi</label>

                                            {{-- Batasnya disebut di depan, bukan menunggu
                                                 galat muncul sesudah menekan Simpan. --}}
                                            <span class="text-admin-label text-ink-faint">Maksimal 500 karakter</span>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <textarea wire:model="description_en" rows="6"
                                                      aria-label="Deskripsi dalam bahasa Inggris"
                                                      placeholder="Deskripsi dalam bahasa Inggris…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('description_en')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <textarea wire:model="description_id" rows="6"
                                                      aria-label="Deskripsi dalam bahasa Indonesia"
                                                      placeholder="Deskripsi dalam bahasa Indonesia…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('description_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: gambar & ikon ─────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-ink">Gambar &amp; Ikon</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Gambar sampul dan lambang kategori.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                {{-- Hanya SATU gambar — save() mengosongkan
                                     koleksinya sebelum menambah yang baru.
                                     Karena itu petaknya berhenti di dua ubin. --}}
                                <div class="grid grid-cols-2 gap-3">

                                    @if($editingId && filled($existingImage))
                                        <div class="group relative aspect-square overflow-hidden
                                                    rounded-control border border-line">
                                            <img src="{{ $existingImage }}" alt=""
                                                 class="block h-full w-full bg-mist object-cover">

                                            <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                        from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                        group-hover:opacity-100 group-focus-within:opacity-100">
                                                <x-admin.confirm-delete metode="deleteImage"
                                                                        label="Hapus gambar"
                                                                        judul="Hapus gambar kategori?"
                                                                        tombol="Ya, hapus gambar"
                                                                        ikon="h-3.5 w-3.5"
                                                                        kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                               justify-center rounded-control bg-white/90 text-danger
                                                                               transition-colors hover:bg-white">
                                                    Gambarnya dihapus dari penyimpanan begitu kategori ini disimpan,
                                                    dan kategori ini kembali tampil tanpa gambar di situs publik.
                                                </x-admin.confirm-delete>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- temporaryUrl() dibungkus try: ia
                                         melempar galat untuk berkas yang
                                         bukan gambar, dan atribut accept
                                         hanya menyaring tampilan penjelajah
                                         berkas. --}}
                                    @if($imageFile)
                                        @php
                                            try {
                                                $pratinjau = $imageFile->temporaryUrl();
                                            } catch (\Throwable $e) {
                                                $pratinjau = null;
                                            }
                                        @endphp

                                        <div class="relative aspect-square overflow-hidden rounded-control
                                                    border border-dashed border-brand/50 bg-brand-wash">
                                            @if($pratinjau)
                                                <img src="{{ $pratinjau }}" alt=""
                                                     class="block h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center px-3
                                                             text-center text-admin-caption text-ink-muted">
                                                    {{ $imageFile->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-admin-caption font-semibold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <x-admin.upload-tile model="imageFile"
                                                         id="gambar-kategori"
                                                         judul="{{ filled($existingImage) ? 'Ganti gambar kategori' : 'Tambah gambar kategori' }}"
                                                         label="{{ filled($existingImage) ? 'Ganti gambar' : 'Tambah gambar' }}" />
                                </div>

                                <p class="mt-3 text-admin-caption text-ink-faint">
                                    Maksimal 3 MB. Mengunggah yang baru
                                    menggantikan yang lama.
                                </p>

                                @error('imageFile')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror

                                <div class="mt-4">
                                    <label for="kategori-icon" class="block text-admin-label text-ink-faint">
                                        Kelas ikon
                                    </label>

                                    <input type="text" wire:model="icon" id="kategori-icon"
                                           placeholder="mis. coffee-icon" class="admin-control mt-2">

                                    <p class="mt-2 text-admin-caption text-ink-faint">
                                        Cadangan kalau kategori ini tidak punya gambar.
                                    </p>

                                    @error('icon')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
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
                                        <h3 class="text-admin-title text-ink">Publikasi Kategori</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Tentukan status dan urutan kategori produk.</p>
                                    </div>
                                </div>

                                <div class="p-5">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">Status</label>

                                        {{-- :nullable="false" — kategori selalu berada di
                                             salah satu dari dua keadaan ini. --}}
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status kategori" placeholder="Aktif"
                                                        :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'active',   'label' => 'Aktif'],
                                                            ['nilai' => 'inactive', 'label' => 'Nonaktif'],
                                                        ]" />
                                    </div>

                                    <div>
                                        <label for="kategori-urutan" class="block text-admin-label text-ink-faint">
                                            Urutan tampil
                                        </label>

                                        <input type="number" wire:model="sort_order" id="kategori-urutan"
                                               min="0" step="1" class="admin-control mt-2">
                                        
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
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan kategori' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
