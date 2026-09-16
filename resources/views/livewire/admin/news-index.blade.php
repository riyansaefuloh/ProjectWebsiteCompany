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
            <h1 class="text-admin-display text-heading">
                Berita
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
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
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="news" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-heading">Daftar artikel</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Urut dari yang paling baru ditambahkan.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($newsList->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'artikel' }}
            </span>
        </div>

        {{-- ══ PENYARING — berdiri di DALAM kartu, tepat di atas tabelnya,
             berbingkai sendiri seperti tabelnya. ══ --}}
        <div class="px-5 pt-5">
            <div class="rounded-corner border border-line">

                {{-- Tiga kendali: pencarian separuh lebar, lalu status dan
                     kategori. --}}
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

                    {{-- Selalu tergambar, tidak lagi bersyarat ada-tidaknya
                         kategori: bilah penyaring yang jumlah kendalinya
                         berubah membuat lebar kotak cari ikut melompat. --}}
                    <x-admin.select model="selectedCategory" :value="$selectedCategory"
                                    label="Saring menurut kategori" placeholder="Semua kategori"
                                    :options="$daftarKategori->map(fn ($k) => [
                                        'nilai' => $k->id, 'label' => $k->name,
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
                                    x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedCategory']) }}"
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
                            @forelse($newsList as $news)
                                @php
                                    $judul  = $news->translated_title ?: $news->slug;

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

                                    {{-- Garis tepi kiri dihapus — keadaan
                                         terbit sudah terbaca dari pil status
                                         di kolom Status, dan dua penanda
                                         untuk satu keadaan tidak menambah
                                         kejelasan. --}}
                                    <td class="py-4 pl-5 pr-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            {{-- Petak 40x40, sama dengan
                                                 Produk, Kategori, dan
                                                 Sertifikasi. Sampulnya 3:2,
                                                 jadi petak persegi ini
                                                 memangkas sisi kiri-kanannya. --}}
                                            @if($alamatSampul)
                                                <img src="{{ $alamatSampul }}" alt=""
                                                     loading="lazy" width="40" height="40"
                                                     class="h-10 w-10 shrink-0 rounded-control border border-line
                                                            bg-mist object-cover">
                                            @else
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                             rounded-control border border-dashed border-line-strong
                                                             bg-mist text-ink-faint"
                                                      title="Artikel ini belum punya sampul">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <span class="block truncate text-admin-strong text-ink"
                                                      title="{{ $judul }}">{{ $judul }}</span>

                                                <span class="mt-0.5 flex items-center gap-2 text-admin-caption text-ink-faint">
                                                    {{-- Slug jadi baris
                                                         kedua: itulah yang
                                                         muncul di alamat
                                                         artikelnya, jadi itu
                                                         yang dicocokkan saat
                                                         menelusuri tautan. --}}
                                                    <span class="min-w-0 truncate"
                                                          title="Slug: {{ $news->slug }}">{{ $news->slug }}</span>

                                                    @if($news->tags->isNotEmpty())
                                                        <span class="admin-pill bg-mist-deep font-semibold text-ink-muted"
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
                                                         border-line bg-mist px-2 py-1 text-admin-label font-semibold text-ink-muted"
                                                  title="{{ $news->category->name }}">
                                                <span class="min-w-0 truncate">{{ $news->category->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-admin-body text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($news->author)
                                            <div class="flex min-w-0 items-center gap-2">
                                                <x-admin.avatar :name="$news->author->name" size="sm" />
                                                <span class="min-w-0 truncate text-admin-body text-ink-muted"
                                                      title="{{ $news->author->name }}">{{ $news->author->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-admin-body text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($news->published_at)
                                            <span class="block text-admin-body tabular-nums text-ink-muted">
                                                {{ $news->published_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-admin-caption tabular-nums text-ink-faint">
                                                {{ $news->published_at->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-admin-body text-ink-faint">&mdash;</span>
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

                                            {{-- Penegasan menyebut akibatnya,
                                                 bukan sekadar "yakin?":
                                                 artikel lenyap dari situs
                                                 publik beserta sampul dan
                                                 kedua terjemahannya. --}}
                                            <x-admin.confirm-delete metode="delete"
                                                                    :id="$news->id"
                                                                    :nama="$judul"
                                                                    judul="Hapus artikel?"
                                                                    tombol="Ya, hapus artikel">
                                                Sampul dan kedua terjemahannya ikut terhapus, dan tautannya di situs publik jadi mati.
                                            </x-admin.confirm-delete>
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
                                            <p class="mt-4 text-admin-title text-heading">
                                                Tidak ada artikel yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                statusnya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'selectedStatus', 'selectedCategory']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-admin-title text-heading">
                                                Belum ada artikel
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
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
                                         bg-brand-wash text-brand">
                                <x-icon.admin name="news" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-berita"
                                    class="truncate text-admin-title text-heading">
                                    {{ $editingId ? 'Ubah artikel' : 'Tulis artikel' }}
                                </h2>
                                <p class="mt-0.5 text-admin-label text-ink-muted">
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
                            {{-- Kartu INI SAJA yang tidak memakai
                                 overflow-hidden: ia membungkus penyunting
                                 teks, dan kotak alamat tautan milik Quill
                                 akan terpotong olehnya. --}}
                            <section class="rounded-corner border border-line bg-canvas">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="news" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-heading">Isi artikel</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Judul, ringkasan, dan isi artikel dalam dua bahasa.</p>
                                        </div>
                                    </div>

                                    {{-- Tab bahasa dan tombol Terjemahkan
                                         berdiri TERPISAH: kendali bersegmen
                                         menjanjikan "pilih salah satu", dan
                                         tombol tindakan di dalam bingkai yang
                                         sama mengingkari janji itu. --}}
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
    {{-- Pesan gagal-terjemah, tepat di bawah tombol yang memicunya —
         sebelumnya ia gagal tanpa mengatakan apa pun. --}}
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

                                        {{-- Judul mengikuti tab. Keduanya
                                             TETAP di DOM dan yang tidak aktif
                                             hanya disembunyikan: isian yang
                                             elemennya lenyap membuat Livewire
                                             kehilangan nilainya. --}}
                                        <div>
                                            <label class="block text-admin-label text-ink-faint">
                                                Judul artikel <span class="text-brand">*</span>
                                            </label>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                                <input type="text" wire:model="title_en"
                                                       aria-label="Judul artikel dalam bahasa Inggris"
                                                       placeholder="Judul artikel dalam bahasa Inggris"
                                                       class="admin-control">
                                                @error('title_en')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                                <input type="text" wire:model="title_id"
                                                       aria-label="Judul artikel dalam bahasa Indonesia"
                                                       placeholder="Judul artikel dalam bahasa Indonesia"
                                                       class="admin-control">
                                                @error('title_id')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Slug-nya dirangkai dari judul Inggris di save(),
                                                 jadi judul itulah yang menentukan alamat
                                                 artikelnya di situs publik. --}}
                                            
                                        </div>

                                        {{-- Ringkasan — mengikuti tab. --}}
                                        <div>
                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <label class="text-admin-label text-ink-faint">Ringkasan</label>
                                                <span class="text-admin-label text-ink-faint">Maksimal 500 karakter</span>
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                                <textarea wire:model="excerpt_en" rows="3"
                                                          aria-label="Ringkasan dalam bahasa Inggris"
                                                          placeholder="Kalimat pembuka yang tampil di kartu daftar berita…"
                                                          class="admin-control resize-none leading-relaxed"></textarea>
                                                @error('excerpt_en')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                                <textarea wire:model="excerpt_id" rows="3"
                                                          aria-label="Ringkasan dalam bahasa Indonesia"
                                                          placeholder="Kalimat pembuka yang tampil di kartu daftar berita…"
                                                          class="admin-control resize-none leading-relaxed"></textarea>
                                                @error('excerpt_id')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Isi artikel, penyunting kaya.
                                             Skrip yang disisipkan Livewire
                                             lewat morph DOM tidak pernah
                                             dijalankan peramban — karena itu
                                             editornya dibundel Vite, lihat
                                             resources/js/editor.js. --}}
                                        <div>
                                            <label class="block text-admin-label text-ink-faint">
                                                Isi artikel <span class="text-brand">*</span>
                                            </label>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                                <x-admin.editor model="content_en" :value="$content_en"
                                                                :kunci="$editingId ?? 'baru'"
                                                                label="Isi artikel dalam bahasa Inggris"
                                                                placeholder="Tulis isi artikelnya di sini…" />

                                                @error('content_en')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                                <x-admin.editor model="content_id" :value="$content_id"
                                                                :kunci="$editingId ?? 'baru'"
                                                                label="Isi artikel dalam bahasa Indonesia"
                                                                placeholder="Tulis isi artikelnya di sini…" />

                                                @error('content_id')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: SEO ───────────────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                     rounded-control bg-brand-wash text-brand">
                                            <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                                        </span>

                                        <div class="min-w-0">
                                            <h3 class="text-admin-title text-heading">SEO</h3>
                                            <p class="mt-0.5 text-admin-label text-ink-muted">Judul dan deskripsi untuk mesin pencari.</p>
                                        </div>
                                    </div>

                                    {{-- Per bahasa, tapi sakelarnya tidak
                                         digandakan: dua sakelar berisi sama
                                         persis terbaca sebagai kerusakan. --}}
                                    <span class="shrink-0 text-admin-label text-ink-faint">Mengikuti bahasa di kartu atas</span>
                                </div>

                                <div class="p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-admin-label text-ink-faint">Judul meta</label>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                                <input type="text" wire:model="meta_title_en"
                                                       aria-label="Judul meta dalam bahasa Inggris"
                                                       placeholder="Kosongkan untuk memakai judul artikelnya"
                                                       class="admin-control">
                                                <p class="admin-hint">
                                                    Dikosongkan berarti judul artikelnya yang dipakai.
                                                </p>

                                                @error('meta_title_en')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                                <input type="text" wire:model="meta_title_id"
                                                       aria-label="Judul meta dalam bahasa Indonesia"
                                                       placeholder="Kosongkan untuk memakai judul artikelnya"
                                                       class="admin-control">
                                                <p class="admin-hint">
                                                    Dikosongkan berarti judul artikelnya yang dipakai.
                                                </p>

                                                @error('meta_title_id')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <label class="text-admin-label text-ink-faint">Deskripsi meta</label>
                                                <span class="text-admin-label text-ink-faint">Maksimal 500 karakter</span>
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                                <textarea wire:model="meta_description_en" rows="3"
                                                          aria-label="Deskripsi meta dalam bahasa Inggris"
                                                          placeholder="Kalimat yang muncul di bawah judul pada hasil pencarian…"
                                                          class="admin-control resize-none leading-relaxed"></textarea>
                                                <p class="admin-hint">
                                                    Dikosongkan berarti 160 huruf pertama isi artikelnya yang dipakai.
                                                </p>

                                                @error('meta_description_en')
                                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                                <textarea wire:model="meta_description_id" rows="3"
                                                          aria-label="Deskripsi meta dalam bahasa Indonesia"
                                                          placeholder="Kalimat yang muncul di bawah judul pada hasil pencarian…"
                                                          class="admin-control resize-none leading-relaxed"></textarea>
                                                <p class="admin-hint">
                                                    Dikosongkan berarti 160 huruf pertama isi artikelnya yang dipakai.
                                                </p>

                                                @error('meta_description_id')
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

                            {{-- ── Kartu: kategori & tag ────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="category" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-heading">Kategori &amp; tag</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Penanda yang mengelompokkan artikel ini.</p>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-admin-label text-ink-faint">Kategori</label>

                                            {{-- Kategori dibuat DAN dihapus
                                                 dari sini: baris tambah di
                                                 kaki menu, tong sampah di
                                                 tiap baris saat kursor lewat. --}}
                                            <x-admin.select model="news_category_id"
                                                            :value="$news_category_id" class="mt-2"
                                                            label="Kategori artikel" placeholder="Tanpa kategori"
                                                            aksiTambah="tambahKategori"
                                                            labelTambah="Tambah kategori"
                                                            petunjukTambah="Nama kategori baru…"
                                                            aksiHapus="hapusKategori"
                                                            labelHapus="Hapus kategori"
                                                            :options="$categories->map(fn ($k) => [
                                                                'nilai' => $k->id, 'label' => $k->name,
                                                            ])->all()" />

                                            @error('news_category_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                            
                                        </div>

                                        <div x-data="{
                                                 tambah: false,
                                                 teksBaru: '',
                                                 menyimpan: false,

                                                 async simpan() {
                                                     const nama = this.teksBaru.trim()
                                                     if (! nama || this.menyimpan) return

                                                     this.menyimpan = true
                                                     try {
                                                         await $wire.call('tambahTag', nama)
                                                     } finally {
                                                         this.menyimpan = false
                                                         this.teksBaru  = ''
                                                         this.tambah    = false
                                                     }
                                                 },
                                             }">

                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <label class="text-admin-label text-ink-faint">Tag</label>

                                                {{-- Tombolnya di kepala,
                                                     bukan sebagai keping
                                                     terakhir: keping di deret
                                                     itu semuanya berarti
                                                     "pilih", dan satu keping
                                                     yang membuka kotak ketik
                                                     merusak artinya. --}}
                                                <button type="button" x-show="! tambah"
                                                        x-on:click="tambah = true; $nextTick(() => $refs.isian.focus())"
                                                        class="inline-flex shrink-0 items-center gap-1.5 text-admin-label
                                                               font-semibold text-brand underline-offset-4 hover:underline">
                                                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <path d="M8 3.4v9.2M3.4 8h9.2" stroke="currentColor"
                                                              stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                    Tambah tag
                                                </button>
                                            </div>

                                            {{-- .prevent.stop di Enter WAJIB
                                                 — tanpanya Enter ikut
                                                 mengirim borang modal yang
                                                 membungkusnya, dan artikelnya
                                                 tersimpan setengah jadi. --}}
                                            <div x-show="tambah" x-cloak class="mt-2 flex items-center gap-1.5">
                                                <input type="text" x-ref="isian" x-model="teksBaru"
                                                       placeholder="Nama tag baru…" aria-label="Nama tag baru"
                                                       maxlength="50"
                                                       x-on:keydown.enter.prevent.stop="simpan()"
                                                       x-on:keydown.escape.prevent.stop="tambah = false; teksBaru = ''"
                                                       class="admin-control min-w-0 flex-1 !py-1.5 text-admin-body">

                                                <button type="button" x-on:click="simpan()"
                                                        x-bind:disabled="! teksBaru.trim() || menyimpan"
                                                        aria-label="Simpan tag"
                                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-control
                                                               bg-brand text-white transition-colors hover:bg-brand-deep
                                                               disabled:cursor-not-allowed disabled:opacity-40">
                                                    <svg x-show="! menyimpan" class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="1.8"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <svg x-show="menyimpan" x-cloak class="h-3.5 w-3.5 animate-spin"
                                                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.35"/>
                                                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                              stroke-width="1.6" stroke-linecap="round"/>
                                                    </svg>
                                                </button>

                                                <button type="button" x-on:click="tambah = false; teksBaru = ''"
                                                        aria-label="Batal menambah tag"
                                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-control
                                                               border border-line text-ink-faint transition-colors
                                                               hover:border-line-strong hover:text-ink">
                                                    <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                </button>
                                            </div>

                                            @error('selectedTags')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror

                                            @if($tags->isNotEmpty())
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach($tags as $tag)
                                                        {{-- BUKAN satu
                                                             <label> utuh:
                                                             tombol di dalam
                                                             label adalah HTML
                                                             tidak sah, dan
                                                             menekan tong
                                                             sampahnya akan
                                                             ikut menggerakkan
                                                             kotak centangnya. --}}
                                                        <span class="group/keping inline-flex items-center gap-2
                                                                     rounded-full border border-line bg-canvas py-1.5 pl-3 pr-1.5
                                                                     text-admin-label font-semibold text-ink-muted
                                                                     transition-colors hover:border-line-strong
                                                                     has-[:checked]:border-brand/40
                                                                     has-[:checked]:bg-brand-wash
                                                                     has-[:checked]:text-brand-deep">

                                                            <label class="inline-flex cursor-pointer items-center gap-2">
                                                                {{-- @checked()
                                                                     WAJIB —
                                                                     tanpanya
                                                                     tag yang
                                                                     sudah
                                                                     melekat
                                                                     tampil
                                                                     tak
                                                                     tercentang
                                                                     saat
                                                                     dibuka,
                                                                     lalu
                                                                     tersapu
                                                                     habis
                                                                     begitu
                                                                     disimpan. --}}
                                                                <x-admin.checkbox wire:model="selectedTags"
                                                                                  value="{{ $tag->id }}"
                                                                                  :checked="in_array($tag->id, $selectedTags)" />

                                                                {{ $tag->name }}
                                                            </label>

                                                            {{-- Penegas
                                                                 biasa, bukan
                                                                 penegasan
                                                                 dalam baris:
                                                                 keping ini
                                                                 tidak berdiri
                                                                 di daftar
                                                                 melayang,
                                                                 jadi tidak
                                                                 ada lapisan
                                                                 yang perlu
                                                                 didahului. --}}
                                                            <x-admin.confirm-delete metode="hapusTag"
                                                                                    :id="$tag->id"
                                                                                    :nama="$tag->name"
                                                                                    label="Hapus tag {{ $tag->name }}"
                                                                                    judul="Hapus tag?"
                                                                                    tombol="Ya, hapus tag"
                                                                                    ikon="h-3 w-3"
                                                                                    kelas="inline-flex h-5 w-5 shrink-0 items-center justify-center
                                                                                           rounded-full text-ink-faint opacity-0 transition
                                                                                           hover:bg-danger/10 hover:text-danger
                                                                                           focus-visible:opacity-100
                                                                                           group-hover/keping:opacity-100">
                                                                Tag ini dilepas dari semua artikel yang memakainya, lalu dihapus.
                                                            </x-admin.confirm-delete>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="mt-2 rounded-control border border-dashed border-line-strong
                                                          bg-mist/40 px-3.5 py-2.5 text-admin-label text-ink-muted">
                                                    Belum ada tag. Pakai <span class="font-semibold text-ink">Tambah tag</span>
                                                    di atas untuk membuat yang pertama.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ── Kartu: sampul ────────────────────────── --}}
                            <section class="overflow-hidden rounded-corner border border-line bg-canvas">
                                <div class="flex items-center gap-2.5 border-b border-line px-5 py-3.5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                                 rounded-control bg-brand-wash text-brand">
                                        <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                                    </span>

                                    <div class="min-w-0">
                                        <h3 class="text-admin-title text-heading">Sampul</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Gambar yang tampil di kartu daftar berita.</p>
                                    </div>
                                </div>

                                <div class="p-5">
                                    {{-- Dua ubin bersebelahan — yang ada
                                         sekarang dan penggantinya. Berita
                                         hanya menyimpan SATU sampul, jadi
                                         petaknya berhenti di dua. --}}
                                    <div class="grid grid-cols-2 gap-3">

                                        @if($editingId && filled($existingCoverUrl))
                                            <div class="group relative aspect-square overflow-hidden
                                                        rounded-control border border-line">
                                                <img src="{{ $existingCoverUrl }}" alt=""
                                                     class="block h-full w-full bg-mist object-cover">

                                                <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                            from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                            group-hover:opacity-100 group-focus-within:opacity-100">
                                                    <x-admin.confirm-delete metode="deleteCover"
                                                                            label="Hapus sampul"
                                                                            judul="Hapus sampul artikel?"
                                                                            tombol="Ya, hapus sampul"
                                                                            ikon="h-3.5 w-3.5"
                                                                            kelas="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                                                   justify-center rounded-control bg-white/90 text-danger
                                                                                   transition-colors hover:bg-white">
                                                        Berkasnya ikut terhapus dari penyimpanan, dan artikel ini tampil tanpa gambar di daftar berita.
                                                    </x-admin.confirm-delete>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- temporaryUrl() dibungkus try: ia
                                             melempar galat untuk berkas yang
                                             bukan gambar, dan atribut accept
                                             hanya menyaring tampilan
                                             penjelajah berkas. --}}
                                        @if($coverFile)
                                            @php
                                                try {
                                                    $pratinjau = $coverFile->temporaryUrl();
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
                                                    <span class="flex h-full w-full items-center justify-center px-4
                                                                 text-center text-admin-caption text-ink-muted">
                                                        {{ $coverFile->getClientOriginalName() }}
                                                    </span>
                                                @endif

                                                <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                             text-admin-caption font-semibold text-white">Baru</span>
                                            </div>
                                        @endif

                                        <x-admin.upload-tile model="coverFile"
                                                             id="sampul-berita"
                                                             accept="image/jpeg,image/png,image/webp"
                                                             judul="{{ filled($existingCoverUrl) ? 'Ganti sampul' : 'Tambah sampul' }}"
                                                             label="{{ filled($existingCoverUrl) ? 'Ganti sampul' : 'Tambah sampul' }}" />
                                    </div>

                                    <p class="admin-hint">
                                        JPG, PNG, atau WebP, maksimal 3&nbsp;MB, diubah otomatis jadi
                                        WebP.
                                    </p>

                                    @error('coverFile')
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
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Status tayang dan waktu terbit artikel.</p>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-admin-label text-ink-faint">Status</label>

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
                                            <label for="berita-terbit" class="block text-admin-label text-ink-faint">
                                                Waktu terbit
                                            </label>

                                            <input type="datetime-local" wire:model="published_at" id="berita-terbit"
                                                   class="admin-control mt-2">
                                            
                                            @error('published_at')
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
