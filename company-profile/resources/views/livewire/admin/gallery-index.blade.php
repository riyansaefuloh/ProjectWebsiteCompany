<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * admin lainnya. Di sini cuma pencarian: album tidak punya status,
         * kategori, atau tanggal terbit yang bisa disaring.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
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
                Galeri
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Album foto dan video fasilitas yang tampil di situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah album
        </button>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════
         PESAN SETELAH TERSIMPAN

         Tidak digambar saat modalnya terbuka: di sana pesannya sudah tampil di
         dalam modal, dan spanduk kedua di halaman belakang cuma tertutup rapat
         tanpa pernah terbaca.
         ══════════════════════════════════════════════════════════════════ --}}
    @if(session()->has('message') && ! $isOpen)
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

        {{-- Satu kendali saja, jadi ia membentang selebar kartunya. Album
             belum punya keterangan lain yang masuk akal disaring — status,
             kategori, dan tanggal terbit tidak ada di tabelnya. --}}
        <div class="p-5">
            <div class="relative">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-album"
                       aria-label="Cari album"
                       placeholder="Cari nama album…"
                       class="admin-control pl-11 pr-10">

                <span wire:loading wire:target="search"
                      class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
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
                    <x-icon.admin name="gallery" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar album</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut dari yang paling baru diperbarui.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($galleries->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'album' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Album',      'lebar' => 'w-[52%]', 'rata' => 'text-left'],
                            ['label' => 'Diperbarui', 'lebar' => 'w-[24%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',       'lebar' => 'w-[24%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    {{-- Lebar minimalnya turun begitu kolom deretan ubin hilang;
                         kalau dibiarkan 820px, tiga kolom ini melar dan kolom
                         Aksi terlempar jauh dari Diperbarui. --}}
                    <table class="w-full min-w-[620px] table-fixed">
                        @if($galleries->isNotEmpty())
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
                            @forelse($galleries as $gallery)
                                @php
                                    $nama  = filled($gallery->name) ? $gallery->name : 'Album tanpa nama';
                                    $isi   = $gallery->items;
                                    $foto  = $isi->where('type', 'image')->count();
                                    $video = $isi->where('type', 'video')->count();

                                    /*
                                     * Satu petak sampul saja, bukan deretan ubin.
                                     *
                                     * Album bisa berisi puluhan foto, dan deretan yang
                                     * ikut memanjang menarik tinggi barisnya berubah-ubah
                                     * sampai tabelnya kehilangan iramanya. Jumlahnya sudah
                                     * disebut di baris kedua; petaknya cukup menjawab
                                     * "album yang mana", bukan "isinya apa saja" — itu
                                     * urusan modal kelolanya.
                                     */
                                    $sampul = $isi->first(function ($i) {
                                        return $i->type === 'image' && $i->getFirstMedia('gallery');
                                    });

                                    $berkas = $sampul?->getFirstMedia('gallery');

                                    /* getFirstMedia(), bukan getFirstMediaUrl(): yang
                                       kedua mengembalikan untai kosong saat tidak ada
                                       berkasnya, dan untai kosong di src membuat peramban
                                       memuat ulang HALAMAN ini sebagai gambar. */
                                    $alamatSampul = $berkas
                                        ? ($berkas->hasGeneratedConversion('thumb')
                                            ? $berkas->getUrl('thumb')
                                            : $berkas->getUrl())
                                        : null;

                                    /* Album yang isinya video semua tetap punya penanda
                                       sendiri, bukan petak kosong yang menyesatkan. */
                                    $sampulVideo = ! $alamatSampul && $video > 0;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Album. Garis hijau di tepi kiri menandai album
                                         yang sudah ada isinya — album kosong tidak
                                         menggambar apa pun di situs publik, dan itu
                                         perlu terlihat dari ujung mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $isi->isNotEmpty(),
                                        'border-transparent' => $isi->isEmpty(),
                                    ])>
                                        <div class="flex items-center gap-3">
                                            @if($alamatSampul)
                                                <img src="{{ $alamatSampul }}" alt=""
                                                     loading="lazy" width="40" height="40"
                                                     class="h-10 w-10 shrink-0 rounded-control border border-line
                                                            bg-mist object-cover">
                                            @elseif($sampulVideo)
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                             rounded-control border border-line bg-ink text-white"
                                                      title="Album ini berisi video">
                                                    <svg class="h-4 w-4" viewBox="0 0 16 16"
                                                         fill="currentColor" aria-hidden="true">
                                                        <path d="M5.5 3.8v8.4l7-4.2z"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                             rounded-control border border-dashed border-line-strong
                                                             bg-mist text-ink-faint"
                                                      title="{{ $isi->isEmpty()
                                                          ? 'Album ini belum ada isinya'
                                                          : 'Isi album ini belum punya berkas gambar' }}">
                                                    <x-icon.admin name="gallery" size="h-4 w-4" />
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $nama }}">{{ $nama }}</span>

                                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint">
                                                    @if($isi->isEmpty())
                                                        Belum ada isinya
                                                    @else
                                                        {{ collect([
                                                            $foto  ? $foto . ' foto'   : null,
                                                            $video ? $video . ' video' : null,
                                                        ])->filter()->implode(' · ') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($gallery->updated_at)
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $gallery->updated_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $gallery->updated_at->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Ikon "kelola", bukan pensil: tombol ini
                                                 bukan cuma mengubah namanya — ia juga
                                                 tempat menambah dan menghapus isi
                                                 albumnya. --}}
                                            <button type="button" wire:click="edit('{{ $gallery->id }}')"
                                                    title="Kelola {{ $nama }}"
                                                    aria-label="Kelola {{ $nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="manage" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasannya menyebut akibatnya, bukan
                                                 sekadar "yakin?": seluruh isi albumnya
                                                 ikut terhapus. --}}
                                            <button type="button" wire:click="delete('{{ $gallery->id }}')"
                                                    wire:confirm="Hapus album &quot;{{ $nama }}&quot;? {{ $isi->count() }} isi di dalamnya ikut terhapus, dan albumnya hilang dari galeri situs publik."
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
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'gallery'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada album yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya.
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada album
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Album mengelompokkan foto dan video fasilitas yang
                                                tampil di galeri situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah album pertama
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
            {{ $galleries->links('vendor.pagination.admin', ['satuan' => 'album']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL KELOLA ALBUM
         ══════════════════════════════════════════════════════════════════ --}}
    @if($isOpen)
        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.call('closeModal')"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-album">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.call('closeModal')"></div>

            {{-- 1100px seperti modal produk dan berita: petak medianya butuh
                 ruang, dan panel 900px cuma memuat dua ubin per baris. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[1100px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="store" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="gallery" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-album"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $gallery_id ? 'Kelola album' : 'Tambah album' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi.
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="closeModal"
                                aria-label="Tutup"
                                class="-mr-1 shrink-0 rounded-control p-1.5 text-ink-faint
                                       transition-colors hover:bg-mist hover:text-ink">
                            <x-icon.admin name="close" size="h-4 w-4" />
                        </button>
                    </div>

                    {{-- Pesan tersimpan digambar DI DALAM modalnya saat sedang
                         menyunting. store() sengaja tidak menutup modal di
                         keadaan itu, jadi pesan yang cuma tergambar di halaman
                         belakang tertutup rapat oleh modalnya sendiri —
                         menekan Simpan terasa seperti tidak terjadi apa-apa. --}}
                    @if($gallery_id && session()->has('message'))
                        <div class="flex shrink-0 items-center gap-2.5 border-b border-brand/25
                                    bg-brand-wash px-6 py-3" role="status">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                         bg-brand text-white">
                                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <p class="min-w-0 text-[12px] font-semibold text-brand-deep">
                                {{ session('message') }}
                            </p>
                        </div>
                    @endif

                    {{-- ── Dua kolom ───────────────────────────────────── --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: informasi album ───────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Informasi album
                                </h3>

                                <div>
                                    <label for="nama-album" class="block text-[12px] font-semibold text-ink-faint">
                                        Nama album <span class="text-brand">*</span>
                                    </label>

                                    <input type="text" wire:model="name" id="nama-album"
                                           placeholder="mis. Fasilitas Pengolahan Kopi"
                                           class="admin-control mt-2">

                                    <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                        Nama ini jadi judul albumnya di galeri situs publik.
                                    </p>

                                    @error('name')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            <section class="rounded-corner border border-line bg-canvas p-5">
                                @php
                                    $isiAlbum = $editingGallery?->items ?? collect();
                                @endphp

                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Isi album
                                    </h3>

                                    @if($isiAlbum->isNotEmpty())
                                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border
                                                     border-line bg-mist px-2.5 py-1 text-[12px] font-semibold text-ink-muted">
                                            <span class="tabular-nums text-ink">{{ $isiAlbum->count() }}</span> isi
                                        </span>
                                    @endif
                                </div>

                                {{-- Petak 4:3 — nisbah yang dipotong konversi 'thumb'
                                     (1200×900). Petak persegi akan menjanjikan bingkai
                                     yang bukan bingkai sebenarnya di situs publik. --}}
                                <div class="grid grid-cols-2 gap-3">

                                    @foreach($isiAlbum as $item)
                                        @php
                                            /* getFirstMedia(), bukan getFirstMediaUrl(): yang
                                               kedua mengembalikan untai kosong saat tidak ada
                                               berkasnya, dan untai kosong di src membuat
                                               peramban memuat ulang HALAMAN ini sebagai
                                               gambar. */
                                            $berkas = $item->getFirstMedia('gallery');

                                            $alamat = $berkas
                                                ? ($berkas->hasGeneratedConversion('thumb')
                                                    ? $berkas->getUrl('thumb')
                                                    : $berkas->getUrl())
                                                : null;
                                        @endphp

                                        <div class="group/isi relative aspect-[4/3] overflow-hidden
                                                    rounded-control border border-line bg-mist">

                                            @if($item->type === 'video')
                                                <a href="{{ $item->video_url }}" target="_blank" rel="noopener"
                                                   class="flex h-full w-full flex-col items-center justify-center
                                                          gap-1.5 bg-ink px-3 text-center text-white transition-opacity
                                                          hover:opacity-90"
                                                   title="Buka {{ $item->video_url }}">
                                                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 16 16"
                                                         fill="currentColor" aria-hidden="true">
                                                        <path d="M5.5 3.8v8.4l7-4.2z"/>
                                                    </svg>
                                                    <span class="w-full truncate text-[11px] text-white/70">
                                                        {{ $item->video_url }}
                                                    </span>
                                                </a>
                                            @elseif($alamat)
                                                <img src="{{ $alamat }}" alt=""
                                                     loading="lazy"
                                                     class="block h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full flex-col items-center justify-center
                                                             gap-1.5 px-3 text-center text-ink-faint">
                                                    <x-icon.admin name="gallery" size="h-5 w-5" />
                                                    <span class="text-[11px] leading-snug">Berkasnya tidak ada</span>
                                                </span>
                                            @endif

                                            <div class="absolute inset-x-0 bottom-0 flex justify-end bg-gradient-to-t
                                                        from-ink/80 to-transparent p-2 opacity-0 transition-opacity
                                                        group-hover/isi:opacity-100 group-focus-within/isi:opacity-100">
                                                <button type="button" wire:click="deleteItem('{{ $item->id }}')"
                                                        wire:confirm="Hapus isi ini dari albumnya? Berkasnya ikut terhapus."
                                                        aria-label="Hapus isi album"
                                                        class="inline-flex h-[26px] w-[26px] shrink-0 items-center
                                                               justify-center rounded-control bg-white/90 text-danger
                                                               transition-colors hover:bg-white">
                                                    <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Berkas yang baru dipilih tapi belum tersimpan.
                                         temporaryUrl() dibungkus try: ia melempar galat
                                         untuk berkas yang bukan gambar, dan atribut
                                         accept cuma menyaring tampilan penjelajah
                                         berkas — bukan jaminan. --}}
                                    @foreach($photos ?? [] as $foto)
                                        @php
                                            try {
                                                $pratinjau = $foto->temporaryUrl();
                                            } catch (\Throwable $e) {
                                                $pratinjau = null;
                                            }
                                        @endphp

                                        <div class="relative aspect-[4/3] overflow-hidden rounded-control
                                                    border border-dashed border-brand/50 bg-brand-wash">
                                            @if($pratinjau)
                                                <img src="{{ $pratinjau }}" alt=""
                                                     class="block h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center px-3
                                                             text-center text-[11px] leading-snug text-ink-muted">
                                                    {{ $foto->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-2 top-2 rounded-full bg-brand px-2 py-0.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endforeach

                                    <label title="Tambah foto"
                                           class="flex aspect-[4/3] cursor-pointer flex-col items-center justify-center
                                                  gap-2 rounded-control border-2 border-dashed border-line-strong
                                                  bg-mist/40 text-ink-faint transition-colors
                                                  hover:border-brand hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="photos" id="foto-album"
                                               multiple accept="image/jpeg,image/png,image/webp,image/gif"
                                               aria-label="Tambah foto ke album"
                                               class="sr-only">

                                        <span wire:loading.remove wire:target="photos"
                                              class="flex flex-col items-center gap-1.5">
                                            <svg class="h-7 w-7" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <span class="text-[11px] font-semibold">Tambah foto</span>
                                        </span>

                                        <svg wire:loading wire:target="photos"
                                             class="h-6 w-6 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                                    JPG, PNG, WebP, atau GIF; maksimal 5&nbsp;MB per foto. Bisa
                                    pilih beberapa sekaligus, dan semuanya diubah otomatis jadi WebP.
                                    Foto baru masuk begitu tombol simpan ditekan.
                                </p>

                                @error('photos.*')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror

                                @if(! $gallery_id)
                                    <p class="mt-3 rounded-control border border-dashed border-line-strong
                                              bg-mist/40 px-3.5 py-2.5 text-[12px] leading-relaxed text-ink-muted">
                                        Album ini belum tersimpan. Foto dan video yang dipilih sekarang
                                        ikut masuk begitu albumnya disimpan.
                                    </p>
                                @endif
                            </section>

                            {{-- ── Kartu: tambah video ──────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Tambah video
                                </h3>

                                <div>
                                    <label for="video-album" class="block text-[12px] font-semibold text-ink-faint">
                                        Tautan video
                                    </label>

                                    <input type="url" wire:model="videoUrl" id="video-album"
                                           placeholder="https://youtube.com/watch?v=…"
                                           class="admin-control mt-2">

                                    {{-- Satu tautan per simpan, bukan daftar: itulah yang
                                         dilakukan store() — ia membuat satu isi video lalu
                                         mengosongkan kotaknya. Menjanjikan lebih dari itu
                                         di layar cuma membuat isian yang diam-diam
                                         terbuang. --}}
                                    <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                        Tautan YouTube atau Vimeo. Satu tautan per simpan —
                                        kotaknya dikosongkan lagi sesudahnya, jadi video
                                        berikutnya bisa langsung ditambahkan.
                                    </p>

                                    @error('videoUrl')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                        {{-- "Selesai", bukan "Batal", saat menyunting: perubahannya
                             sudah tersimpan tiap kali tombol simpan ditekan, jadi
                             menutup modal tidak membatalkan apa pun. --}}
                        <button type="button" wire:click="closeModal"
                                class="admin-btn admin-btn-quiet">
                            {{ $gallery_id ? 'Selesai' : 'Batal' }}
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="store, photos"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="store"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $gallery_id ? 'Simpan perubahan' : 'Simpan album' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
