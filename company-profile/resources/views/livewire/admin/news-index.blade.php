<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * admin lainnya.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        if (filled($selectedStatus)) {
            $penyaringAktif->push([
                'label' => 'Status',
                'nilai' => $selectedStatus === 'published' ? 'Terbit' : 'Draf',
                'props' => ['selectedStatus'],
            ]);
        }

        if (filled($selectedCategory)) {
            $penyaringAktif->push([
                'label' => 'Kategori',
                'nilai' => optional($daftarKategori->firstWhere('id', $selectedCategory))->name
                    ?? 'Tidak dikenal',
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
            <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                Berita
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Artikel dan kabar perusahaan yang tampil di situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tulis artikel
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
    {{-- overflow-visible supaya menu turun penyaringnya tidak terpotong —
         .card membawa overflow-hidden. --}}
    <div class="card mb-6 overflow-visible">

        {{-- Tiga kendali: pencarian separuh, lalu status dan kategori.

             Penyaring kategori dulu sengaja ditunda karena satu pun kategori
             berita belum bisa dibuat — menu pilih yang isinya kosong cuma jadi
             kendali mati. Sekarang kategorinya punya halaman kelola sendiri,
             jadi penyaringnya ikut dipasang, dan tetap disembunyikan selama
             daftarnya masih kosong. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-4">

            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-berita"
                       aria-label="Cari artikel"
                       placeholder="Cari judul atau isi artikel…"
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

            @if($daftarKategori->isNotEmpty())
                <x-admin.select model="selectedCategory" :value="$selectedCategory"
                                label="Saring menurut kategori" placeholder="Semua kategori"
                                :options="$daftarKategori->map(fn ($k) => [
                                    'nilai' => $k->id, 'label' => $k->name,
                                ])->all()" />
            @endif
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
                            x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedCategory']) }}"
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
                    <x-icon.admin name="news" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar artikel</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut dari yang paling baru ditambahkan.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($newsList->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'artikel' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedStatus, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Artikel',  'lebar' => 'w-[34%]', 'rata' => 'text-left'],
                            ['label' => 'Kategori', 'lebar' => 'w-[13%]', 'rata' => 'text-left'],
                            /* 17%: nama + avatarnya butuh ruang lebih dari kolom
                               lain, dan "Super Admin User" terpotong di 14%. */
                            ['label' => 'Penulis',  'lebar' => 'w-[17%]', 'rata' => 'text-left'],
                            ['label' => 'Terbit',   'lebar' => 'w-[14%]', 'rata' => 'text-left'],
                            ['label' => 'Status',   'lebar' => 'w-[10%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',     'lebar' => 'w-[12%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[1000px] table-fixed">
                        @if($newsList->isNotEmpty())
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
                            @forelse($newsList as $news)
                                @php
                                    $judul  = $news->translated_title ?: $news->slug;
                                    $terbit = $news->status === 'published';

                                    /* getFirstMedia(), bukan getFirstMediaUrl(): yang
                                       kedua mengembalikan untai kosong saat tidak ada
                                       berkasnya, dan untai kosong di src membuat
                                       peramban memuat ulang HALAMAN ini sebagai gambar. */
                                    $berkas = $news->getFirstMedia('covers');

                                    $alamatSampul = $berkas
                                        ? ($berkas->hasGeneratedConversion('thumb')
                                            ? $berkas->getUrl('thumb')
                                            : $berkas->getUrl())
                                        : null;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Artikel. Garis hijau di tepi kiri menandai yang
                                         sudah terbit — penanda kedua di samping pilnya,
                                         supaya draf langsung terlihat berbeda dari ujung
                                         mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $terbit,
                                        'border-transparent' => ! $terbit,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            {{-- Sampulnya melintang 3:2, bukan bujur
                                                 sangkar: itu nisbah yang dipotong
                                                 konversi 'thumb' (600×400), jadi petak
                                                 persegi akan memangkas sisi kiri-kanannya
                                                 tanpa alasan. --}}
                                            @if($alamatSampul)
                                                <img src="{{ $alamatSampul }}" alt=""
                                                     loading="lazy" width="60" height="40"
                                                     class="h-10 w-[60px] shrink-0 rounded-control border border-line
                                                            bg-mist object-cover">
                                            @else
                                                <span class="flex h-10 w-[60px] shrink-0 items-center justify-center
                                                             rounded-control border border-dashed border-line-strong
                                                             bg-mist text-ink-faint"
                                                      title="Artikel ini belum punya sampul">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $judul }}">{{ $judul }}</span>

                                                <span class="mt-0.5 flex items-center gap-2 text-[12px] text-ink-faint">
                                                    {{-- Slug jadi baris kedua: itulah yang
                                                         muncul di alamat halaman artikelnya,
                                                         jadi ia yang dicocokkan saat
                                                         menelusuri tautan. --}}
                                                    <span class="min-w-0 truncate"
                                                          title="Slug: {{ $news->slug }}">{{ $news->slug }}</span>

                                                    @if($news->tags->isNotEmpty())
                                                        <span class="inline-flex shrink-0 items-center rounded-full
                                                                     bg-mist-deep px-1.5 text-[10px] font-bold
                                                                     text-ink-muted"
                                                              title="{{ $news->tags->pluck('name')->implode(', ') }}">
                                                            {{ $news->tags->count() }} tag
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($news->category)
                                            <span class="inline-flex max-w-full items-center rounded-control border
                                                         border-line bg-mist px-2 py-1 text-[12px] font-semibold text-ink-muted"
                                                  title="{{ $news->category->name }}">
                                                <span class="min-w-0 truncate">{{ $news->category->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($news->author)
                                            <div class="flex min-w-0 items-center gap-2">
                                                <x-admin.avatar :name="$news->author->name" size="sm" />
                                                <span class="min-w-0 truncate text-[13px] text-ink-muted"
                                                      title="{{ $news->author->name }}">{{ $news->author->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($news->published_at)
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $news->published_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $news->published_at->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$news->status" />
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $news->id }}')"
                                                    title="Ubah {{ $judul }}"
                                                    aria-label="Ubah {{ $judul }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasannya menyebut akibatnya, bukan
                                                 sekadar "yakin?": artikelnya lenyap dari
                                                 situs publik beserta sampul dan kedua
                                                 terjemahannya. --}}
                                            <button type="button" wire:click="delete('{{ $news->id }}')"
                                                    wire:confirm="Hapus artikel &quot;{{ $judul }}&quot;? Sampul dan kedua terjemahannya ikut terhapus, dan tautannya di situs publik jadi mati."
                                                    title="Hapus {{ $judul }}"
                                                    aria-label="Hapus {{ $judul }}"
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
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'news'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada artikel yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                statusnya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedCategory']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada artikel
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Artikel yang ditulis di sini muncul di halaman berita
                                                situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tulis artikel pertama
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
            {{ $newsList->links('vendor.pagination.admin', ['satuan' => 'artikel']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TULIS / UBAH ARTIKEL
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            /* Titik merah di sakelar bahasa: menandai tab mana yang isian
               wajibnya belum beres, supaya galat di tab tersembunyi tidak
               berujung tombol Simpan yang seakan tidak bereaksi. */
            $galatEn = $errors->hasAny(['title_en', 'excerpt_en', 'content_en',
                                        'meta_title_en', 'meta_description_en']);
            $galatId = $errors->hasAny(['title_id', 'excerpt_id', 'content_id',
                                        'meta_title_id', 'meta_description_id']);
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-berita">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            {{-- 1100px seperti modal produk, bukan 900 seperti kategori: isi
                 artikelnya butuh kotak tulis yang benar-benar lebar, dan panel
                 kanannya membawa tiga kartu. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[1100px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="news" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-berita"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah artikel' : 'Tulis artikel' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi,
                                    termasuk judul dan isi di kedua bahasa.
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

                            {{-- ── Kartu: isi artikel ───────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">

                                {{-- Sakelar bahasa di kepala kartu: ia mengatur seluruh
                                     isian terjemahan di kolom ini — judul, ringkasan,
                                     isi, dan kartu SEO di bawahnya. --}}
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Isi artikel
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

                                                @if(($kode === 'en' && $galatEn) || ($kode === 'id' && $galatId))
                                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-danger"
                                                          title="Ada isian yang perlu diperbaiki di sini"></span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="space-y-4">

                                    {{-- Judul — mengikuti tab. Keduanya tetap ada di DOM,
                                         yang tidak aktif disembunyikan: isian yang diketik
                                         lalu elemennya lenyap membuat Livewire kehilangan
                                         nilainya. --}}
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">
                                            Judul artikel <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="title_en"
                                                   aria-label="Judul artikel dalam bahasa Inggris"
                                                   placeholder="Judul artikel dalam bahasa Inggris"
                                                   class="admin-control">
                                            @error('title_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="title_id"
                                                   aria-label="Judul artikel dalam bahasa Indonesia"
                                                   placeholder="Judul artikel dalam bahasa Indonesia"
                                                   class="admin-control">
                                            @error('title_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Slug-nya dirangkai dari judul Inggris di save(),
                                             jadi judul itulah yang menentukan alamat
                                             artikelnya di situs publik. --}}
                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Alamat artikel di situs publik dirangkai dari judul
                                            bahasa Inggris.
                                        </p>
                                    </div>

                                    {{-- Ringkasan — mengikuti tab. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-[12px] font-semibold text-ink-faint">Ringkasan</label>
                                            <span class="text-[12px] text-ink-faint">Maksimal 500 karakter</span>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <textarea wire:model="excerpt_en" rows="3"
                                                      aria-label="Ringkasan dalam bahasa Inggris"
                                                      placeholder="Kalimat pembuka yang tampil di kartu daftar berita…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('excerpt_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <textarea wire:model="excerpt_id" rows="3"
                                                      aria-label="Ringkasan dalam bahasa Indonesia"
                                                      placeholder="Kalimat pembuka yang tampil di kartu daftar berita…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('excerpt_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Isi artikel — mengikuti tab.

                                         Penyunting kaya. Percobaan sebelumnya memuat TinyMCE
                                         lewat tag skrip di dalam modal dan tidak pernah hidup:
                                         skrip yang disisipkan Livewire lewat morph DOM tidak
                                         pernah dijalankan peramban.

                                         Yang sekarang dibundel lewat Vite dan didaftarkan
                                         sebagai komponen Alpine, jadi sudah termuat sejak awal
                                         halaman dan tidak ada skrip yang perlu disisipkan
                                         belakangan. Lihat resources/js/editor.js. --}}
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">
                                            Isi artikel <span class="text-brand">*</span>
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <x-admin.editor model="content_en" :value="$content_en"
                                                            :kunci="$editingId ?? 'baru'"
                                                            label="Isi artikel dalam bahasa Inggris"
                                                            placeholder="Tulis isi artikelnya di sini…" />

                                            @error('content_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <x-admin.editor model="content_id" :value="$content_id"
                                                            :kunci="$editingId ?? 'baru'"
                                                            label="Isi artikel dalam bahasa Indonesia"
                                                            placeholder="Tulis isi artikelnya di sini…" />

                                            @error('content_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: SEO ───────────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <div class="mb-4 flex flex-wrap items-baseline justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        SEO
                                    </h3>

                                    {{-- Kartu ini juga per bahasa, tapi sakelarnya tidak
                                         digandakan: dua sakelar yang isinya sama persis
                                         terbaca sebagai kerusakan, bukan sebagai dua
                                         kendali. --}}
                                    <span class="text-[12px] text-ink-faint">
                                        Mengikuti bahasa di kartu atas
                                    </span>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Judul meta</label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="meta_title_en"
                                                   aria-label="Judul meta dalam bahasa Inggris"
                                                   placeholder="Kosongkan untuk memakai judul artikelnya"
                                                   class="admin-control">
                                            @error('meta_title_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="meta_title_id"
                                                   aria-label="Judul meta dalam bahasa Indonesia"
                                                   placeholder="Kosongkan untuk memakai judul artikelnya"
                                                   class="admin-control">
                                            @error('meta_title_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-[12px] font-semibold text-ink-faint">Deskripsi meta</label>
                                            <span class="text-[12px] text-ink-faint">Maksimal 500 karakter</span>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <textarea wire:model="meta_description_en" rows="3"
                                                      aria-label="Deskripsi meta dalam bahasa Inggris"
                                                      placeholder="Kalimat yang muncul di bawah judul pada hasil pencarian…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('meta_description_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <textarea wire:model="meta_description_id" rows="3"
                                                      aria-label="Deskripsi meta dalam bahasa Indonesia"
                                                      placeholder="Kalimat yang muncul di bawah judul pada hasil pencarian…"
                                                      class="admin-control resize-none leading-relaxed"></textarea>
                                            @error('meta_description_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: sampul ────────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Sampul
                                </h3>

                                {{-- Petaknya melintang 3:2 — nisbah yang dipotong konversi
                                     'thumb' (600×400). Pratinjau persegi akan menjanjikan
                                     bingkai yang bukan bingkai sebenarnya di situs
                                     publik. --}}
                                @if($editingId && filled($existingCoverUrl))
                                    <div class="group relative mb-3 aspect-[3/2] overflow-hidden
                                                rounded-control border border-line">
                                        <img src="{{ $existingCoverUrl }}" alt=""
                                             class="block h-full w-full bg-mist object-cover">

                                        <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                    from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                    group-hover:opacity-100 group-focus-within:opacity-100">
                                            <button type="button" wire:click="deleteCover"
                                                    wire:confirm="Hapus sampul artikel ini?"
                                                    aria-label="Hapus sampul"
                                                    class="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                           justify-center rounded-control bg-white/90 text-danger
                                                           transition-colors hover:bg-white">
                                                <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                {{-- Berkas yang baru dipilih tapi belum tersimpan.
                                     temporaryUrl() dibungkus try: ia melempar galat untuk
                                     berkas yang bukan gambar, dan atribut accept cuma
                                     menyaring tampilan penjelajah berkas — bukan
                                     jaminan. --}}
                                @if($coverFile)
                                    @php
                                        try {
                                            $pratinjau = $coverFile->temporaryUrl();
                                        } catch (\Throwable $e) {
                                            $pratinjau = null;
                                        }
                                    @endphp

                                    <div class="relative mb-3 aspect-[3/2] overflow-hidden rounded-control
                                                border border-dashed border-brand/50 bg-brand-wash">
                                        @if($pratinjau)
                                            <img src="{{ $pratinjau }}" alt=""
                                                 class="block h-full w-full object-cover">
                                        @else
                                            <span class="flex h-full w-full items-center justify-center px-4
                                                         text-center text-[12px] leading-snug text-ink-muted">
                                                {{ $coverFile->getClientOriginalName() }}
                                            </span>
                                        @endif

                                        <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                     text-[10px] font-bold text-white">Baru</span>
                                    </div>
                                @endif

                                <label title="{{ filled($existingCoverUrl) ? 'Ganti sampul' : 'Tambah sampul' }}"
                                       class="flex aspect-[3/2] cursor-pointer flex-col items-center justify-center
                                              gap-2 rounded-control border-2 border-dashed border-line-strong
                                              bg-mist/40 text-ink-faint transition-colors
                                              hover:border-brand hover:bg-brand-wash hover:text-brand
                                              focus-within:border-brand focus-within:text-brand">

                                    <input type="file" wire:model="coverFile" id="sampul-berita"
                                           accept="image/jpeg,image/png,image/webp"
                                           aria-label="{{ filled($existingCoverUrl) ? 'Ganti sampul' : 'Tambah sampul' }}"
                                           class="sr-only">

                                    <span wire:loading.remove wire:target="coverFile"
                                          class="flex flex-col items-center gap-2">
                                        <svg class="h-8 w-8" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                            <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                  stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="text-[12px] font-semibold">
                                            {{ filled($existingCoverUrl) ? 'Ganti sampul' : 'Pilih gambar sampul' }}
                                        </span>
                                    </span>

                                    <svg wire:loading wire:target="coverFile"
                                         class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                              stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </label>

                                <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                                    JPG, PNG, atau WebP; maksimal 3&nbsp;MB. Diubah otomatis jadi
                                    WebP, dan mengunggah yang baru menggantikan yang lama.
                                </p>

                                @error('coverFile')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </section>

                            {{-- ── Kartu: kategori & tag ────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Kategori &amp; tag
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Kategori</label>

                                        {{-- Menunya selalu digambar, meski kategorinya masih
                                             kosong: baris "tambah kategori" di kakinya
                                             memungkinkan membuat kategori tanpa keluar dari
                                             modal ini. Untuk mengganti nama atau menghapus,
                                             tempatnya di menu Kategori &amp; Tag. --}}
                                        <x-admin.select model="news_category_id"
                                                        :value="$news_category_id" class="mt-2"
                                                        label="Kategori artikel" placeholder="Tanpa kategori"
                                                        aksiTambah="tambahKategori"
                                                        labelTambah="Tambah kategori"
                                                        petunjukTambah="Nama kategori baru…"
                                                        :options="$categories->map(fn ($k) => [
                                                            'nilai' => $k->id, 'label' => $k->name,
                                                        ])->all()" />

                                        @error('news_category_id')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            @if($categories->isEmpty())
                                                Belum ada kategori. Buka menunya dan pakai
                                                <span class="font-semibold text-ink-muted">Tambah kategori</span>
                                                untuk membuat yang pertama.
                                            @else
                                                Kategori baru bisa dibuat langsung dari menunya.
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Tag</label>

                                        @if($tags->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach($tags as $tag)
                                                    {{-- Kotak centang aslinya disembunyikan dan
                                                         ronanya digerakkan peer-checked serta
                                                         has-[:checked] di CSS — jadi kepingnya
                                                         menyala seketika, tanpa menunggu
                                                         server. --}}
                                                    <label class="inline-flex cursor-pointer items-center gap-2
                                                                  rounded-full border border-line bg-canvas px-3 py-1.5
                                                                  text-[12px] font-semibold text-ink-muted
                                                                  transition-colors hover:border-line-strong
                                                                  has-[:checked]:border-brand/40
                                                                  has-[:checked]:bg-brand-wash
                                                                  has-[:checked]:text-brand-deep">
                                                        {{-- @checked() WAJIB — tanpa itu tag yang sudah
                                                             melekat tampil tak tercentang saat berita
                                                             dibuka untuk diubah, lalu tersapu habis
                                                             begitu disimpan. --}}
                                                        <input type="checkbox" wire:model="selectedTags"
                                                               value="{{ $tag->id }}"
                                                               @checked(in_array($tag->id, $selectedTags))
                                                               class="peer sr-only">

                                                        <span class="flex h-3.5 w-3.5 shrink-0 items-center justify-center
                                                                     rounded-[4px] border border-line-strong
                                                                     transition-colors peer-checked:border-brand
                                                                     peer-checked:bg-brand">
                                                            <svg class="h-2.5 w-2.5 text-white opacity-0 transition-opacity
                                                                        peer-checked:opacity-100"
                                                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                                <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor"
                                                                      stroke-width="2.2" stroke-linecap="round"
                                                                      stroke-linejoin="round"/>
                                                            </svg>
                                                        </span>

                                                        {{ $tag->name }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="mt-2 rounded-control border border-dashed border-line-strong
                                                      bg-mist/40 px-3.5 py-2.5 text-[12px] leading-relaxed text-ink-muted">
                                                Belum ada tag yang dibuat.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Penerbitan
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[12px] font-semibold text-ink-faint">Status</label>

                                        {{-- :nullable="false" — artikel selalu berada di salah
                                             satu dari dua keadaan ini. --}}
                                        <x-admin.select model="status" :value="$status" class="mt-2"
                                                        label="Status artikel" :nullable="false"
                                                        :options="[
                                                            ['nilai' => 'published', 'label' => 'Terbit'],
                                                            ['nilai' => 'draft',     'label' => 'Draf'],
                                                        ]" />
                                    </div>

                                    <div>
                                        <label for="berita-terbit" class="block text-[12px] font-semibold text-ink-faint">
                                            Waktu terbit
                                        </label>

                                        <input type="datetime-local" wire:model="published_at" id="berita-terbit"
                                               class="admin-control mt-2">

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Dipakai untuk mengurutkan daftar berita. Dikosongkan
                                            berarti dicap waktu sekarang saat disimpan.
                                        </p>

                                        @error('published_at')
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

                        <button type="submit" wire:loading.attr="disabled" wire:target="save, coverFile"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="save"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan artikel' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
