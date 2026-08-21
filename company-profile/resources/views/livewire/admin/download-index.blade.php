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

        /*
         * filled(), bukan sekadar cek kebenaran nilainya: '0' — pilihan
         * "Terbuka" — itu palsu di PHP, jadi kepingnya tidak akan pernah
         * muncul dan penyaring yang menyala jadi tak terlihat.
         */
        if (filled($selectedGate)) {
            $penyaringAktif->push([
                'label' => 'Akses',
                'nilai' => $selectedGate === '1' ? 'Perlu email' : 'Terbuka',
                'props' => ['selectedGate'],
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
                Unduhan
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Katalog dan brosur PDF yang bisa diunduh pembeli dari situs publik.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah berkas
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

        {{-- Dua kendali, jadi keduanya berbagi satu baris — pencarian dua
             pertiga, akses sepertiga. Susunan yang sama dengan halaman
             Kategori, Sertifikasi, dan Berita. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-3">

            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-unduhan"
                       aria-label="Cari berkas unduhan"
                       placeholder="Cari judul berkas…"
                       class="admin-control pl-11 pr-10">

                <span wire:loading wire:target="search"
                      class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>

            {{-- Nilainya '1' dan '0', bukan true/false: menu pilih ini
                 mengirimkan untai, dan properti penyaringnya pun bertipe untai
                 supaya "belum dipilih" ('') bisa dibedakan dari "Terbuka"
                 ('0'). --}}
            <x-admin.select model="selectedGate" :value="$selectedGate"
                            label="Saring menurut akses" placeholder="Semua akses"
                            :options="[
                                ['nilai' => '1', 'label' => 'Perlu email'],
                                ['nilai' => '0', 'label' => 'Terbuka'],
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
                            x-on:click="{{ $bersihkan(['search', 'selectedGate']) }}"
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
                    <x-icon.admin name="download" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar berkas</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut menurut nomor urutan tampilnya di situs publik.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($downloads->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'berkas' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, selectedGate, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Berkas',  'lebar' => 'w-[42%]', 'rata' => 'text-left'],
                            ['label' => 'Akses',   'lebar' => 'w-[16%]', 'rata' => 'text-left'],
                            ['label' => 'Diunduh', 'lebar' => 'w-[14%]', 'rata' => 'text-left'],
                            ['label' => 'Urutan',  'lebar' => 'w-[10%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',    'lebar' => 'w-[18%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[900px] table-fixed">
                        @if($downloads->isNotEmpty())
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
                            @forelse($downloads as $dl)
                                @php
                                    $judul = filled($dl->title) ? $dl->title : 'Berkas tanpa judul';

                                    /*
                                     * Berkasnya benar-benar dicek ada di disk, bukan
                                     * cuma dicek kolomnya terisi.
                                     *
                                     * Baris yang menyimpan nama berkas tapi berkasnya
                                     * sudah tidak ada akan menawarkan tautan yang
                                     * berujung 404 — dan itu tautan yang dipakai
                                     * pembeli di situs publik. Lebih baik ketahuan di
                                     * sini daripada di sana.
                                     */
                                    $adaBerkas = filled($dl->file_path)
                                        && \Illuminate\Support\Facades\Storage::disk('public')->exists($dl->file_path);

                                    $namaBerkas = filled($dl->file_path) ? basename($dl->file_path) : null;
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Berkas. Garis hijau di tepi kiri menandai baris
                                         yang berkasnya benar-benar ada — tanpa berkas,
                                         barisnya tidak bisa diunduh siapa pun. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $adaBerkas,
                                        'border-transparent' => ! $adaBerkas,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            <span @class([
                                                'flex h-10 w-10 shrink-0 items-center justify-center rounded-control border',
                                                'border-line bg-mist text-ink-muted'                       => $adaBerkas,
                                                'border-dashed border-line-strong bg-mist text-ink-faint'  => ! $adaBerkas,
                                            ])>
                                                <x-icon.admin name="pdf" size="h-4 w-4" />
                                            </span>

                                            <div class="min-w-0">
                                                <span class="block truncate text-[13px] font-semibold text-ink"
                                                      title="{{ $judul }}">{{ $judul }}</span>

                                                <span class="mt-0.5 flex items-center gap-2 text-[12px]">
                                                    @if($adaBerkas)
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($dl->file_path) }}"
                                                           target="_blank" rel="noopener"
                                                           class="min-w-0 truncate text-ink-faint underline-offset-4
                                                                  transition-colors hover:text-brand hover:underline"
                                                           title="Buka {{ $namaBerkas }}">{{ $namaBerkas }}</a>
                                                    @elseif($namaBerkas)
                                                        {{-- Tersimpan di basis data, hilang di disk.

                                                             Tanpa py dan tanpa leading sendiri: keping yang
                                                             lebih tinggi dari teks 12px di sebelahnya akan
                                                             menarik tinggi barisnya jadi 75px, dan tabel ini
                                                             kehilangan irama 73px yang dipakai semua halaman
                                                             lain. --}}
                                                        <span class="inline-flex shrink-0 items-center rounded-full
                                                                     bg-status-rejected/10 px-1.5 text-[10px]
                                                                     font-bold text-status-rejected"
                                                              title="Tercatat sebagai {{ $dl->file_path }}, tapi berkasnya tidak ada di penyimpanan">
                                                            Berkas hilang
                                                        </span>
                                                    @else
                                                        <span class="text-ink-faint">Belum ada berkas</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <x-admin.status-pill :status="$dl->require_email ? 'gated' : 'open'" />
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="text-[13px] tabular-nums text-ink-muted">
                                            <span class="font-semibold text-ink">{{ number_format($dl->download_count) }}</span>
                                            kali
                                        </span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-control
                                                     bg-mist px-2 text-[13px] font-semibold tabular-nums text-ink-muted"
                                              title="Urutan tampil: {{ $dl->sort_order }}">{{ $dl->sort_order }}</span>
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $dl->id }}')"
                                                    title="Ubah {{ $judul }}"
                                                    aria-label="Ubah {{ $judul }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            {{-- Penegasannya menyebut akibatnya, bukan
                                                 sekadar "yakin?": berkas PDF-nya ikut
                                                 dihapus dari penyimpanan, dan tautan
                                                 unduhnya di situs publik jadi mati. --}}
                                            <button type="button" wire:click="delete('{{ $dl->id }}')"
                                                    wire:confirm="Hapus &quot;{{ $judul }}&quot;? Berkas PDF-nya ikut terhapus dari penyimpanan, dan tautan unduhnya di situs publik jadi mati."
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
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'download'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada berkas yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                aksesnya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'selectedGate']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada berkas unduhan
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Katalog dan brosur PDF yang ditambahkan di sini muncul
                                                di halaman unduhan situs publik.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah berkas pertama
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
            {{ $downloads->links('vendor.pagination.admin', ['satuan' => 'berkas']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH BERKAS
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            /*
             * Berkas lamanya benar-benar dicek ada di disk, bukan cuma dicek
             * kolomnya terisi — sama seperti di tabelnya. Menawarkan tombol
             * "Buka berkas" yang berujung 404 lebih buruk daripada mengaku
             * berkasnya hilang.
             */
            $adaBerkasLama = filled($existingFilePath)
                && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingFilePath);

            $namaBerkasLama = filled($existingFilePath) ? basename($existingFilePath) : null;
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-unduhan">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            {{-- 900px seperti modal Kategori dan Pasar Ekspor: isiannya cuma
                 empat, dan panel 1100px cuma menyisakan rongga kosong. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[900px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="download" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-unduhan"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah berkas' : 'Tambah berkas' }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian bertanda <span class="font-bold text-brand">*</span> wajib diisi.
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
                                    Informasi berkas
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="judul-unduhan" class="block text-[12px] font-semibold text-ink-faint">
                                            Judul berkas <span class="text-brand">*</span>
                                        </label>

                                        <input type="text" wire:model="title" id="judul-unduhan"
                                               placeholder="mis. Katalog Biji Kopi 2026"
                                               class="admin-control mt-2">

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Judul ini yang dibaca pembeli di halaman unduhan situs publik.
                                        </p>

                                        @error('title')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Sakelar dari kotak centang asli yang disembunyikan,
                                         pola yang sama dengan "Produk unggulan" dan "Pasar
                                         aktif". require_email itu boolean, jadi menu pilih
                                         tidak dipakai: x-admin.select selalu mengirim untai,
                                         dan untai yang jatuh ke properti bertipe bool
                                         melempar galat tipe.

                                         Seluruh rona digerakkan peer-checked dan
                                         has-[:checked] di CSS, bukan oleh nilai di sisi PHP:
                                         wire:model di sini bersifat tunda, jadi nilai di
                                         server baru menyusul pada permintaan berikutnya. --}}
                                    <div>
                                        <span class="block text-[12px] font-semibold text-ink-faint">Akses unduhan</span>

                                        <label class="mt-2 flex cursor-pointer items-start justify-between gap-4
                                                      rounded-control border border-line p-3.5 transition-colors
                                                      hover:border-line-strong
                                                      has-[:checked]:border-brand/40 has-[:checked]:bg-brand-wash">

                                            <span class="min-w-0">
                                                <span class="block text-[13px] font-semibold text-ink">Minta email dulu</span>
                                                <span class="mt-0.5 block text-[12px] leading-relaxed text-ink-muted">
                                                    Pembeli mengisi email sebelum berkasnya diberikan, dan
                                                    emailnya masuk sebagai prospek. Dimatikan berarti
                                                    berkasnya bisa diunduh langsung siapa saja.
                                                </span>
                                            </span>

                                            <span class="relative mt-0.5 inline-flex shrink-0 items-center">
                                                {{-- @checked() supaya keadaannya sudah benar di
                                                     gambar pertama. wire:model sendiri tidak
                                                     menuliskan atribut itu — ia baru menyetel
                                                     sifat .checked sesudah Livewire hidup. --}}
                                                <input type="checkbox" role="switch" wire:model="require_email"
                                                       @checked($require_email)
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
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            {{-- ── Kartu: berkas PDF ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Berkas PDF
                                </h3>

                                {{-- Berkas PDF tidak bisa dipratinjau sebagai gambar, jadi
                                     yang ditampilkan namanya. --}}
                                @if($editingId && $adaBerkasLama)
                                    <div class="mb-3 flex items-center gap-3 rounded-control border border-line
                                                bg-mist/40 p-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                     rounded-control border border-line bg-canvas text-ink-muted">
                                            <x-icon.admin name="pdf" size="h-4 w-4" />
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[12px] font-semibold text-ink"
                                                  title="{{ $namaBerkasLama }}">{{ $namaBerkasLama }}</span>

                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingFilePath) }}"
                                               target="_blank" rel="noopener"
                                               class="mt-0.5 inline-block text-[12px] font-semibold text-brand
                                                      underline-offset-4 hover:underline">
                                                Buka berkas
                                            </a>
                                        </span>
                                    </div>
                                @elseif($editingId && filled($existingFilePath))
                                    {{-- Tercatat di basis data, hilang di disk. --}}
                                    <div class="mb-3 flex items-start gap-3 rounded-control border border-status-rejected/30
                                                bg-status-rejected/5 p-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-control
                                                     border border-dashed border-status-rejected/40 text-status-rejected">
                                            <x-icon.admin name="pdf" size="h-4 w-4" />
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[12px] font-semibold text-status-rejected">
                                                Berkas hilang
                                            </span>
                                            <span class="mt-0.5 block break-all text-[12px] leading-relaxed text-ink-muted">
                                                Tercatat sebagai <span class="font-semibold">{{ $existingFilePath }}</span>,
                                                tapi berkasnya tidak ada di penyimpanan. Unggah ulang untuk
                                                memperbaikinya.
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                {{-- Berkas yang baru dipilih tapi belum tersimpan. --}}
                                @if($pdfFile)
                                    <div class="mb-3 flex items-center gap-3 rounded-control border border-dashed
                                                border-brand/50 bg-brand-wash p-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                                     rounded-control border border-brand/30 bg-canvas text-brand">
                                            <x-icon.admin name="pdf" size="h-4 w-4" />
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[12px] font-semibold text-ink"
                                                  title="{{ $pdfFile->getClientOriginalName() }}">
                                                {{ $pdfFile->getClientOriginalName() }}
                                            </span>
                                            <span class="mt-0.5 inline-block rounded-full bg-brand px-2 py-0.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </span>
                                    </div>
                                @endif

                                <label title="{{ $editingId ? 'Ganti berkas PDF' : 'Pilih berkas PDF' }}"
                                       class="flex cursor-pointer flex-col items-center justify-center gap-2
                                              rounded-control border-2 border-dashed border-line-strong
                                              bg-mist/40 px-4 py-7 text-ink-faint transition-colors
                                              hover:border-brand hover:bg-brand-wash hover:text-brand
                                              focus-within:border-brand focus-within:text-brand">

                                    <input type="file" wire:model="pdfFile" id="pdf-unduhan"
                                           accept="application/pdf"
                                           aria-label="{{ $editingId ? 'Ganti berkas PDF' : 'Pilih berkas PDF' }}"
                                           class="sr-only">

                                    <span wire:loading.remove wire:target="pdfFile"
                                          class="flex flex-col items-center gap-2">
                                        <svg class="h-8 w-8" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                            <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                  stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="text-[12px] font-semibold">
                                            {{ $editingId ? 'Ganti berkas PDF' : 'Pilih berkas PDF' }}
                                        </span>
                                    </span>

                                    <svg wire:loading wire:target="pdfFile"
                                         class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                              stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </label>

                                <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                                    @if($editingId)
                                        Hanya PDF, maksimal 10&nbsp;MB. Dikosongkan berarti berkas
                                        yang sekarang tetap dipakai; mengunggah yang baru
                                        menghapus yang lama.
                                    @else
                                        Hanya PDF, maksimal 10&nbsp;MB. Wajib diisi — tanpa berkas,
                                        tidak ada yang bisa diunduh.
                                    @endif
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

                                <div>
                                    <label for="urutan-unduhan" class="block text-[12px] font-semibold text-ink-faint">
                                        Urutan tampil
                                    </label>

                                    <input type="number" wire:model="sort_order" id="urutan-unduhan"
                                           min="0" step="1" class="admin-control mt-2">

                                    <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                        Angka lebih kecil tampil lebih dulu di situs publik.
                                    </p>

                                    @error('sort_order')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
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

                        <button type="submit" wire:loading.attr="disabled" wire:target="save, pdfFile"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="save"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan berkas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
