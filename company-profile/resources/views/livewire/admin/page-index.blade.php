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
                Halaman
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Susunan beranda dan halaman statis seperti Tentang Kami dan Kebijakan Privasi.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah halaman
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
         SUSUNAN BERANDA

         Beranda tidak punya baris di tabel di bawah — ia dirakit dari
         bagian-bagian tetap yang urutannya disimpan sebagai satu larik JSON.
         Karena itu bentuknya kartu tersendiri, dan letaknya di atas: beranda
         halaman yang paling sering dilihat, jadi ia yang pertama terbaca.
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="card mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="dashboard" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Susunan beranda</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urutan dan tampil-tidaknya tiap bagian di beranda situs publik.
                    </p>
                </div>
            </div>

            {{-- Kartu ini menyimpan sendiri tiap kali disentuh. Tanpa keterangan
                 ini, pemakai mengira perubahannya menunggu tombol Simpan di
                 suatu tempat, lalu menutup halaman tanpa menekannya. --}}
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-line
                         bg-mist px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <svg class="h-3.5 w-3.5 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Tersimpan otomatis
            </span>
        </div>

        <div class="p-5">
            <ul class="overflow-hidden rounded-corner border border-line">
                @foreach($home_sections as $i => $bagian)
                    <li @class([
                        'flex flex-wrap items-center gap-3 border-b border-line px-4 py-3 last:border-0',
                        'bg-canvas'   => $bagian['active'],
                        'bg-mist/40'  => ! $bagian['active'],
                    ])>
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-control
                                     bg-mist text-[12px] font-semibold tabular-nums text-ink-muted">
                            {{ $i + 1 }}
                        </span>

                        <span @class([
                            'min-w-0 flex-1 truncate text-[13px]',
                            'font-semibold text-ink' => $bagian['active'],
                            'text-ink-faint'         => ! $bagian['active'],
                        ])>{{ $bagian['name'] }}</span>

                        {{-- Naik/turun. Tombol di ujung daftarnya digambar mati,
                             bukan dihilangkan: kalau hilang, tombol-tombol di
                             baris lain ikut bergeser dan sasarannya meleset. --}}
                        <div class="flex shrink-0 items-center gap-1.5">
                            @foreach([
                                ['arah' => 'moveSectionUp',   'mati' => $i === 0,
                                 'nama' => 'Naikkan',  'jalur' => 'M10 15.5V5.4M5.4 10 10 5.4l4.6 4.6'],
                                ['arah' => 'moveSectionDown', 'mati' => $i === count($home_sections) - 1,
                                 'nama' => 'Turunkan', 'jalur' => 'M10 4.5v10.1M14.6 10 10 14.6 5.4 10'],
                            ] as $geser)
                                @if($geser['mati'])
                                    <span class="inline-flex h-8 w-8 cursor-not-allowed items-center justify-center
                                                 rounded-control border border-dashed border-line text-ink-faint/50"
                                          aria-hidden="true">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none">
                                            <path d="{{ $geser['jalur'] }}" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                @else
                                    <button type="button" wire:click="{{ $geser['arah'] }}('{{ $bagian['id'] }}')"
                                            title="{{ $geser['nama'] }} {{ $bagian['name'] }}"
                                            aria-label="{{ $geser['nama'] }} {{ $bagian['name'] }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                   border border-line bg-canvas text-ink-muted transition-colors
                                                   hover:border-brand hover:bg-brand hover:text-white">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                            <path d="{{ $geser['jalur'] }}" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                @endif
                            @endforeach

                            {{-- Sakelar tampil. wire:click, bukan wire:model:
                                 nilainya hidup di dalam larik JSON, dan
                                 toggleSectionActive() yang menyimpannya. --}}
                            <button type="button" wire:click="toggleSectionActive('{{ $bagian['id'] }}')"
                                    role="switch" aria-checked="{{ $bagian['active'] ? 'true' : 'false' }}"
                                    aria-label="{{ $bagian['active'] ? 'Sembunyikan' : 'Tampilkan' }} {{ $bagian['name'] }}"
                                    title="{{ $bagian['active'] ? 'Sembunyikan dari beranda' : 'Tampilkan di beranda' }}"
                                    class="relative ml-1 inline-flex shrink-0 items-center">
                                <span @class([
                                    'block h-6 w-11 rounded-full transition-colors',
                                    'bg-brand'      => $bagian['active'],
                                    'bg-mist-deep'  => ! $bagian['active'],
                                ])></span>

                                <span @class([
                                    'pointer-events-none absolute left-0.5 block h-5 w-5 rounded-full bg-white',
                                    'shadow-[0_1px_3px_rgba(26,29,27,0.28)] transition-transform',
                                    'translate-x-5' => $bagian['active'],
                                ])></span>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                Bagian yang dimatikan tetap tersimpan datanya — ia cuma tidak
                digambar di beranda.
            </p>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         PENYARING
         ══════════════════════════════════════════════════════════════════ --}}
    {{-- overflow-visible supaya menu turun penyaringnya tidak terpotong —
         .card membawa overflow-hidden. --}}
    <div class="card mb-6 overflow-visible">

        {{-- Dua kendali, jadi keduanya berbagi satu baris — pencarian dua
             pertiga, status sepertiga. Susunan yang sama dengan halaman
             Kategori, Sertifikasi, Berita, Unduhan, dan Pengguna. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-3">

            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-halaman"
                       aria-label="Cari halaman"
                       placeholder="Cari judul atau alamat halaman…"
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
                    <x-icon.admin name="page" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar halaman</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut dari yang paling baru diperbarui.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($pages->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'halaman' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedStatus, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Halaman',     'lebar' => 'w-[38%]', 'rata' => 'text-left'],
                            ['label' => 'Terjemahan',  'lebar' => 'w-[18%]', 'rata' => 'text-left'],
                            ['label' => 'Status',      'lebar' => 'w-[12%]', 'rata' => 'text-left'],
                            ['label' => 'Diperbarui',  'lebar' => 'w-[16%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',        'lebar' => 'w-[16%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[940px] table-fixed">
                        @if($pages->isNotEmpty())
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
                            @forelse($pages as $page)
                                @php
                                    $judul  = $page->getTranslation('title', 'en') ?: $page->slug;
                                    $terbit = $page->status === 'published';

                                    /*
                                     * Kelengkapan tiap bahasa dinilai dari ISI-nya, bukan
                                     * dari ada-tidaknya baris terjemahan.
                                     *
                                     * Baris terjemahan selalu dibuat berpasangan oleh
                                     * store(), jadi mengecek keberadaannya akan selalu
                                     * menjawab "lengkap" — termasuk untuk halaman yang
                                     * versi Indonesianya masih kosong melompong dan
                                     * tergambar sebagai halaman hampa di situs publik.
                                     */
                                    $lengkap = collect(['en', 'id'])->mapWithKeys(fn ($kode) => [
                                        $kode => filled($page->getTranslation('title', $kode))
                                              && filled($page->getTranslation('content', $kode)),
                                    ]);
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Halaman. Garis hijau di tepi kiri menandai yang
                                         sudah terbit — penanda kedua di samping pilnya,
                                         supaya draf langsung terlihat berbeda dari ujung
                                         mata. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $terbit,
                                        'border-transparent' => ! $terbit,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                         rounded-control border border-line bg-mist text-ink-muted">
                                                <x-icon.admin name="page" size="h-4 w-4" />
                                            </span>

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $judul }}">{{ $judul }}</span>

                                                {{-- Slug jadi baris kedua: itulah alamat
                                                     halamannya di situs publik, dan ia
                                                     dirangkai ulang dari judul Inggris tiap
                                                     kali disimpan. --}}
                                                <span class="mt-0.5 block truncate text-[12px] text-ink-faint"
                                                      title="Alamat: /page/{{ $page->slug }}">/page/{{ $page->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Terjemahan: bahasa yang isinya belum lengkap
                                         menggambar halaman hampa di situs publik, dan itu
                                         tidak kelihatan dari mana pun kecuali di sini. --}}
                                    <td class="px-3 py-4 align-middle">
                                        <div class="flex items-center gap-1.5">
                                            @foreach(['en' => 'EN', 'id' => 'ID'] as $kode => $sebutan)
                                                <span @class([
                                                    'inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold',
                                                    'bg-brand/10 text-brand'                     => $lengkap[$kode],
                                                    'bg-status-rejected/10 text-status-rejected' => ! $lengkap[$kode],
                                                ])
                                                      title="{{ $lengkap[$kode]
                                                          ? 'Judul dan isi bahasa ' . $sebutan . ' sudah terisi'
                                                          : 'Judul atau isi bahasa ' . $sebutan . ' masih kosong' }}">
                                                    {{ $sebutan }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$page->status" />
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($page->updated_at)
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $page->updated_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $page->updated_at->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Membuka halamannya di situs publik. Hanya
                                                 untuk yang sudah terbit: draf belum punya
                                                 alamat yang bisa dibuka siapa pun. --}}
                                            @if($terbit)
                                                <a href="{{ route('page.show', $page->slug) }}"
                                                   target="_blank" rel="noopener"
                                                   title="Lihat {{ $judul }} di situs publik"
                                                   aria-label="Lihat {{ $judul }} di situs publik"
                                                   class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                          border border-line bg-canvas text-ink-muted transition-colors
                                                          hover:border-line-strong hover:bg-mist hover:text-ink">
                                                    <x-icon.admin name="external" size="h-4 w-4" />
                                                </a>
                                            @endif

                                            <button type="button" wire:click="edit('{{ $page->id }}')"
                                                    title="Ubah {{ $judul }}"
                                                    aria-label="Ubah {{ $judul }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasannya menyebut akibatnya, bukan
                                                 sekadar "yakin?": tautannya di situs publik
                                                 jadi mati. --}}
                                            <button type="button" wire:click="delete('{{ $page->id }}')"
                                                    wire:confirm="Hapus halaman &quot;{{ $judul }}&quot;? Kedua terjemahannya ikut terhapus, dan alamat /page/{{ $page->slug }} di situs publik jadi mati."
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
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'page'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada halaman yang cocok
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
                                                Belum ada halaman
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Halaman statis seperti Tentang Kami dan Kebijakan
                                                Privasi ditulis di sini.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah halaman pertama
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
            {{ $pages->links('vendor.pagination.admin', ['satuan' => 'halaman']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    @if($isOpen)
        @php
            /* Titik merah di sakelar bahasa: menandai tab mana yang isian
               wajibnya belum beres, supaya galat di tab tersembunyi tidak
               berujung tombol Simpan yang seakan tidak bereaksi. */
            $galatEn = $errors->hasAny(['title_en', 'content_en']);
            $galatId = $errors->hasAny(['title_id', 'content_id']);

            /*
             * Alamat halamannya dirangkai ulang dari judul Inggris TIAP KALI
             * disimpan — lihat store(). Jadi mengganti judul Inggris diam-diam
             * memindahkan halamannya, dan tautan lama jadi mati. Itu perlu
             * terlihat sebelum tombol simpan ditekan, bukan sesudah.
             */
            $alamatBaru = \Illuminate\Support\Str::slug((string) $title_en);
            $alamatPindah = $page_id && filled($slug) && filled($alamatBaru) && $alamatBaru !== $slug;
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.call('closeModal')"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-halaman">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.call('closeModal')"></div>

            {{-- 1100px seperti modal Berita: isi halamannya butuh kotak tulis
                 yang benar-benar lebar. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[1100px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="store" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="page" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-halaman"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $page_id ? 'Ubah halaman' : 'Tambah halaman' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi,
                                    termasuk judul di kedua bahasa.
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

                    {{-- Peringatan alamat berpindah. --}}
                    @if($alamatPindah)
                        <div class="flex shrink-0 items-start gap-2.5 border-b border-status-new/25
                                    bg-status-new/5 px-6 py-3" role="status">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                         bg-status-new text-white">
                                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round"/>
                                </svg>
                            </span>
                            <p class="min-w-0 text-[12px] leading-relaxed text-ink-muted">
                                Alamat halamannya akan berpindah dari
                                <span class="font-semibold text-ink">/page/{{ $slug }}</span> ke
                                <span class="font-semibold text-ink">/page/{{ $alamatBaru }}</span>.
                                Tautan lama yang sudah tersebar jadi mati.
                            </p>
                        </div>
                    @endif

                    {{-- ── Dua kolom ───────────────────────────────────── --}}
                    <div class="admin-scroll flex min-h-0 flex-1 flex-col overflow-y-auto overscroll-contain
                                lg:flex-row lg:divide-x lg:divide-line lg:overflow-visible">

                        {{-- ══ KIRI ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 p-6
                                    lg:w-[58%] lg:overflow-y-auto lg:overscroll-contain">

                            <section class="rounded-corner border border-line bg-canvas p-5">

                                {{-- Sakelar bahasa di kepala kartu: ia mengatur dua
                                     isian sekaligus — judul dan isi — bukan menempel
                                     di salah satunya. --}}
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                        Isi halaman
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
                                            Judul halaman <span class="text-brand">*</span>
                                        </label>

                                        {{-- .live, satu-satunya di modal ini: pratinjau
                                             alamat di panel kanan baru berguna kalau ia
                                             menyusul sambil mengetik. --}}
                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model.live.debounce.500ms="title_en"
                                                   aria-label="Judul halaman dalam bahasa Inggris"
                                                   placeholder="mis. About Us"
                                                   class="admin-control">
                                            @error('title_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="title_id"
                                                   aria-label="Judul halaman dalam bahasa Indonesia"
                                                   placeholder="mis. Tentang Kami"
                                                   class="admin-control">
                                            @error('title_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Isi halaman — mengikuti tab.

                                         Kotak tulis biasa, bukan penyunting kaya: isinya
                                         memang tersimpan sebagai HTML mentah, dan proyek
                                         ini belum punya penyunting yang hidup. Sama seperti
                                         isi artikel di modal Berita. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-[12px] font-semibold text-ink-faint">Isi halaman</label>
                                            <span class="text-[12px] text-ink-faint">Ditulis sebagai HTML</span>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <textarea wire:model="content_en" rows="16"
                                                      aria-label="Isi halaman dalam bahasa Inggris"
                                                      placeholder="&lt;h1&gt;Judul&lt;/h1&gt;&#10;&lt;p&gt;Paragraf…&lt;/p&gt;"
                                                      class="admin-control resize-y font-mono text-[12px] leading-relaxed"></textarea>
                                            @error('content_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <textarea wire:model="content_id" rows="16"
                                                      aria-label="Isi halaman dalam bahasa Indonesia"
                                                      placeholder="&lt;h1&gt;Judul&lt;/h1&gt;&#10;&lt;p&gt;Paragraf…&lt;/p&gt;"
                                                      class="admin-control resize-y font-mono text-[12px] leading-relaxed"></textarea>
                                            @error('content_id')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Isi boleh kosong menurut store(), tapi halaman
                                             terbit yang isinya kosong tergambar hampa di
                                             situs publik. Disebut di sini, bukan dibiarkan
                                             jadi kejutan. --}}
                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Boleh dikosongkan, tapi halaman terbit yang isinya kosong
                                            tergambar hampa di situs publik.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: alamat ────────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Alamat
                                </h3>

                                <span class="block text-[12px] font-semibold text-ink-faint">
                                    Alamat di situs publik
                                </span>

                                <div class="mt-2 rounded-control border border-line bg-mist/40 px-3.5 py-2.5">
                                    @if(filled($alamatBaru))
                                        <span class="block break-all text-[13px] font-semibold text-ink">
                                            /page/{{ $alamatBaru }}
                                        </span>
                                    @else
                                        <span class="block text-[13px] text-ink-faint">
                                            Belum ada — isi judul bahasa Inggrisnya dulu
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                    Dirangkai otomatis dari judul bahasa Inggris, dan disusun
                                    ulang tiap kali halamannya disimpan.
                                </p>

                                @if($page_id && filled($slug) && $slug !== $alamatBaru)
                                    <p class="mt-2 text-[12px] leading-relaxed text-ink-muted">
                                        Alamat yang berlaku sekarang:
                                        <span class="font-semibold text-ink">/page/{{ $slug }}</span>
                                    </p>
                                @endif
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Penerbitan
                                </h3>

                                <div>
                                    <label class="block text-[12px] font-semibold text-ink-faint">Status</label>

                                    {{-- :nullable="false" — halaman selalu berada di salah
                                         satu dari dua keadaan ini. --}}
                                    <x-admin.select model="status" :value="$status" class="mt-2"
                                                    label="Status halaman" :nullable="false"
                                                    :options="[
                                                        ['nilai' => 'published', 'label' => 'Terbit'],
                                                        ['nilai' => 'draft',     'label' => 'Draf'],
                                                    ]" />

                                    <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                        Hanya halaman terbit yang bisa dibuka di situs publik.
                                    </p>

                                    @error('status')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line px-6 py-4">
                        <button type="button" wire:click="closeModal"
                                class="admin-btn admin-btn-quiet">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="store"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="store"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $page_id ? 'Simpan perubahan' : 'Simpan halaman' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
