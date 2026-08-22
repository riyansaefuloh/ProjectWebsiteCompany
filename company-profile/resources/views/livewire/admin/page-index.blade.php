<div class="mx-auto max-w-[1400px]">

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="font-ui text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[26px]">
                Halaman
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Susunan beranda, susunan halaman Profile, dan isi tiap halaman di situs publik.
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

         Beranda dirakit dari bagian-bagian tetap yang urutannya disimpan
         sebagai satu larik JSON, bukan dari satu tulisan panjang. Letaknya di
         atas karena beranda halaman yang paling sering dilihat, jadi ia yang
         pertama terbaca.
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

                            {{-- Ubah isi. Hanya digambar untuk bagian yang isinya
                                 memang bisa disunting — bagian lain merakit
                                 dirinya dari data (produk, sertifikasi, galeri),
                                 dan tombol yang membuka borang kosong cuma
                                 menjanjikan sesuatu yang belum ada. --}}
                            @if(array_key_exists($bagian['id'], \App\Livewire\Admin\PageIndex::BIDANG_BAGIAN))
                                <button type="button" wire:click="ubahIsiBagian('{{ $bagian['id'] }}')"
                                        title="Ubah isi {{ $bagian['name'] }}"
                                        aria-label="Ubah isi {{ $bagian['name'] }}"
                                        class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                               bg-canvas px-2.5 text-[12px] font-semibold text-ink-muted
                                               transition-colors hover:border-brand hover:text-brand">
                                    <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                    Ubah isi
                                </button>
                            @else
                                {{-- Ruang kosong selebar tombolnya, supaya sakelar
                                     di baris ini tetap sekolom dengan baris lain. --}}
                                <span class="inline-block h-8 w-[86px]" aria-hidden="true"></span>
                            @endif

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
         SUSUNAN HALAMAN PROFILE

         Profile dikeluarkan dari daftar halaman publik di bawah dan diberi
         kartu sendiri: ia bukan halaman berkepala-satu seperti tujuh lainnya,
         melainkan tersusun dari lima bagian yang masing-masing punya judul dan
         isinya sendiri. Satu modal berisi empat puluhan kolom tidak bisa
         dibaca; lima modal pendek bisa.
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="card mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="page" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Susunan halaman Profile</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urutan dan tampil-tidaknya tiap bagian di halaman Profile.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('about') }}" target="_blank" rel="noopener"
                   class="admin-btn shrink-0">
                    <x-icon.admin name="external" size="h-3.5 w-3.5" />
                    Lihat halaman
                </a>

                {{-- Sama seperti susunan beranda: kartu ini menyimpan sendiri
                     tiap kali disentuh. Tanpa keterangan ini, pemakai mengira
                     perubahannya menunggu tombol Simpan di suatu tempat. --}}
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-line
                             bg-mist px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                    <svg class="h-3.5 w-3.5 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Tersimpan otomatis
                </span>
            </div>
        </div>

        <div class="p-5">
            <ul class="overflow-hidden rounded-corner border border-line">
                @foreach($profile_sections as $i => $bagian)
                    <li @class([
                        'flex flex-wrap items-center gap-3 border-b border-line px-4 py-3 last:border-0',
                        'bg-canvas'  => $bagian['active'],
                        'bg-mist/40' => ! $bagian['active'],
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
                                ['arah' => 'moveProfilUp',   'mati' => $i === 0,
                                 'nama' => 'Naikkan',  'jalur' => 'M10 15.5V5.4M5.4 10 10 5.4l4.6 4.6'],
                                ['arah' => 'moveProfilDown', 'mati' => $i === count($profile_sections) - 1,
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

                            <button type="button" wire:click="ubahIsiProfil('{{ $bagian['id'] }}')"
                                    title="Ubah isi {{ $bagian['name'] }}"
                                    aria-label="Ubah isi {{ $bagian['name'] }}"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                           bg-canvas px-2.5 text-[12px] font-semibold text-ink-muted
                                           transition-colors hover:border-brand hover:text-brand">
                                <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                Ubah isi
                            </button>

                            <button type="button" wire:click="toggleProfilActive('{{ $bagian['id'] }}')"
                                    role="switch" aria-checked="{{ $bagian['active'] ? 'true' : 'false' }}"
                                    aria-label="{{ $bagian['active'] ? 'Sembunyikan' : 'Tampilkan' }} {{ $bagian['name'] }}"
                                    title="{{ $bagian['active'] ? 'Sembunyikan dari halaman Profile' : 'Tampilkan di halaman Profile' }}"
                                    class="relative ml-1 inline-flex shrink-0 items-center">
                                <span @class([
                                    'block h-6 w-11 rounded-full transition-colors',
                                    'bg-brand'     => $bagian['active'],
                                    'bg-mist-deep' => ! $bagian['active'],
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
                Bagian yang dimatikan tetap tersimpan isinya — ia cuma tidak digambar
                di halaman Profile.
            </p>
        </div>
    </section>


    {{-- ══════════════════════════════════════════════════════════════════
         HALAMAN PUBLIK

         Seluruh halaman publik selain beranda dan Profile, dalam satu daftar.

         Tujuh yang pertama halaman tetap: isinya dirakit dari data, dan yang
         bisa ditulis cuma kepalanya. Sisanya halaman statis yang dibuat sendiri
         — di situ seluruh halamannya memang tulisan. Keduanya digambar dengan
         bentuk baris yang sama supaya daftarnya terbaca sebagai satu daftar,
         bukan dua yang kebetulan bertetangga.
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="card mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-control bg-mist text-ink-muted">
                    <x-icon.admin name="page" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Halaman publik</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Judul dan deskripsi tiap halaman di situs publik, termasuk halaman statis.
                    </p>
                </div>
            </div>

            <span class="shrink-0 rounded-full border border-line bg-mist px-3 py-1
                         text-[12px] font-semibold tabular-nums text-ink-muted">
                {{ count(\App\Livewire\Admin\PageIndex::HALAMAN_PUBLIK) + $pages->count() }}
            </span>
        </div>

        <div class="p-5">
            <ul class="grid gap-3 sm:grid-cols-2">
                @foreach(\App\Livewire\Admin\PageIndex::HALAMAN_PUBLIK as $hal)
                    @php
                        /* Sudah ada isinya atau belum. Berguna sekilas: dari
                           daftar ini tidak ada cara lain membedakan halaman yang
                           masih memakai teks bawaan dari yang sudah ditulis. */
                        $isiHal = $halaman_publik[$hal['id']]['isi'] ?? [];

                        $bahasaTerisi = collect(['en', 'id'])->filter(
                            fn ($l) => collect($isiHal[$l] ?? [])->contains(
                                fn ($v) => filled(is_string($v) ? trim($v) : $v) && $v !== '<p><br></p>'
                            )
                        );
                    @endphp

                    <li class="flex flex-wrap items-center gap-3 rounded-control border border-line px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <span class="block truncate text-[13px] font-semibold text-ink">{{ $hal['nama'] }}</span>

                            <span class="mt-0.5 block truncate font-mono text-[11px] text-ink-faint">
                                {{ Str::of(route($hal['rute'], [], false))->start('/') }}
                            </span>
                        </div>

                        @if($bahasaTerisi->count() === 2)
                            <span class="shrink-0 rounded-full bg-brand/10 px-2.5 py-1
                                         text-[11px] font-bold text-brand"
                                  title="Kedua bahasa sudah diisi">
                                Ditulis
                            </span>
                        @elseif($bahasaTerisi->count() === 1)
                            <span class="shrink-0 rounded-full bg-status-new/10 px-2.5 py-1
                                         text-[11px] font-bold text-status-new"
                                  title="Baru bahasa {{ $bahasaTerisi->first() === 'en' ? 'Inggris' : 'Indonesia' }} yang diisi">
                                Satu bahasa
                            </span>
                        @else
                            <span class="shrink-0 rounded-full border border-line px-2.5 py-1
                                         text-[11px] font-semibold text-ink-faint"
                                  title="Masih memakai teks bawaan">
                                Bawaan
                            </span>
                        @endif

                        <div class="flex shrink-0 items-center gap-1.5">
                            <a href="{{ route($hal['rute']) }}" target="_blank" rel="noopener"
                               title="Lihat {{ $hal['nama'] }} di situs"
                               aria-label="Lihat {{ $hal['nama'] }} di situs"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                      border border-line bg-canvas text-ink-muted transition-colors
                                      hover:border-brand hover:text-brand">
                                <x-icon.admin name="external" size="h-4 w-4" />
                            </a>

                            <button type="button" wire:click="ubahIsiHalaman('{{ $hal['id'] }}')"
                                    title="Ubah isi {{ $hal['nama'] }}"
                                    aria-label="Ubah isi {{ $hal['nama'] }}"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                           bg-canvas px-2.5 text-[12px] font-semibold text-ink-muted
                                           transition-colors hover:border-brand hover:text-brand">
                                <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                Ubah isi
                            </button>
                        </div>
                    </li>
                @endforeach

                {{-- ── Halaman statis ────────────────────────────────────
                     Bentuk barisnya sama persis dengan ketujuh di atas, tapi
                     isinya berbeda jenis: seluruh halamannya memang tulisan,
                     bukan kepala di atas daftar yang dirakit dari data. Karena
                     itu tombolnya membuka penyunting halaman, dan penandanya
                     memakai status terbit, bukan "sudah ditulis atau belum". --}}
                @foreach($pages as $halaman)
                    <li class="flex flex-wrap items-center gap-3 rounded-control border border-line px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <span class="block truncate text-[13px] font-semibold text-ink">
                                {{ $halaman->translated_title ?: $halaman->slug }}
                            </span>

                            <span class="mt-0.5 block truncate font-mono text-[11px] text-ink-faint">
                                /page/{{ $halaman->slug }}
                            </span>
                        </div>

                        <x-admin.status-pill :status="$halaman->status" />

                        <div class="flex shrink-0 items-center gap-1.5">
                            <a href="{{ route('page.show', $halaman->slug) }}" target="_blank" rel="noopener"
                               title="Lihat di situs" aria-label="Lihat {{ $halaman->slug }} di situs"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                      border border-line bg-canvas text-ink-muted transition-colors
                                      hover:border-brand hover:text-brand">
                                <x-icon.admin name="external" size="h-4 w-4" />
                            </a>

                            <button type="button" wire:click="edit('{{ $halaman->id }}')"
                                    title="Ubah isi {{ $halaman->slug }}"
                                    aria-label="Ubah isi {{ $halaman->slug }}"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                           bg-canvas px-2.5 text-[12px] font-semibold text-ink-muted
                                           transition-colors hover:border-brand hover:text-brand">
                                <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                Ubah isi
                            </button>

                            <button type="button" wire:click="delete('{{ $halaman->id }}')"
                                    wire:confirm="Hapus halaman &quot;{{ $halaman->translated_title ?: $halaman->slug }}&quot;? Tautan /page/{{ $halaman->slug }} akan mati."
                                    title="Hapus {{ $halaman->slug }}"
                                    aria-label="Hapus {{ $halaman->slug }}"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                           border border-line bg-canvas text-ink-muted transition-colors
                                           hover:border-status-rejected hover:text-status-rejected">
                                <x-icon.admin name="trash" size="h-4 w-4" />
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                Isian yang dikosongkan memakai teks bawaan yang sudah diterjemahkan.
                Isi kedua bahasa, atau kosongkan keduanya.
            </p>
        </div>
    </section>



    {{-- ══════════════════════════════════════════════════════════════════
         MODAL ISI BAGIAN BERANDA / HALAMAN PUBLIK

         Satu modal untuk keduanya: bentuk isinya sama persis — teks per
         bahasa, kadang foto — dan dua modal kembar hanya berarti dua tempat
         yang harus diperbaiki tiap kali ada perubahan.
         ══════════════════════════════════════════════════════════════════ --}}
    @if($bagianDibuka)
        @php
            $daftarBidang = match ($jenisDibuka) {
                'bagian' => \App\Livewire\Admin\PageIndex::BIDANG_BAGIAN,
                'profil' => \App\Livewire\Admin\PageIndex::BIDANG_PROFIL,
                default  => \App\Livewire\Admin\PageIndex::BIDANG_HALAMAN,
            };

            $bidang  = $daftarBidang[$bagianDibuka] ?? [];
            $opsi    = match ($jenisDibuka) {
                'bagian' => \App\Livewire\Admin\PageIndex::OPSI_BAGIAN[$bagianDibuka] ?? [],
                'profil' => \App\Livewire\Admin\PageIndex::opsiProfil()[$bagianDibuka] ?? [],
                default  => [],
            };

            $berfoto = in_array($bagianDibuka, match ($jenisDibuka) {
                'bagian' => \App\Livewire\Admin\PageIndex::BAGIAN_BERFOTO,
                'profil' => \App\Livewire\Admin\PageIndex::PROFIL_BERFOTO,
                default  => \App\Livewire\Admin\PageIndex::HALAMAN_BERFOTO,
            }, true);

            $namaBagian = match ($jenisDibuka) {
                'bagian' => collect($home_sections)->firstWhere('id', $bagianDibuka)['name'] ?? $bagianDibuka,
                'profil' => collect($profile_sections)->firstWhere('id', $bagianDibuka)['name'] ?? $bagianDibuka,
                default  => collect(\App\Livewire\Admin\PageIndex::HALAMAN_PUBLIK)
                    ->firstWhere('id', $bagianDibuka)['nama'] ?? $bagianDibuka,
            };

            $sebutanJenis = match ($jenisDibuka) {
                'bagian' => 'Isi bagian',
                'profil' => 'Profile',
                default  => 'Isi halaman',
            };

            /* Foto yang tercatat belum tentu ada di disk. <img> beralamat mati
               menggambar ikon rusak, dan itu terbaca sebagai fotonya yang rusak. */
            $fotoAda = filled($gambarBagianLama)
                && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarBagianLama);
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.call('tutupIsiBagian')"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-bagian">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.call('tutupIsiBagian')"></div>

            <div class="relative flex max-h-[90vh] w-full max-w-[900px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="simpanIsiBagian" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin :name="$jenisDibuka === 'bagian' ? 'dashboard' : 'page'" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-bagian"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $sebutanJenis }} · {{ $namaBagian }}
                                </h2>
                                <p class="mt-0.5 text-[12px] text-ink-muted">
                                    Isian yang dikosongkan memakai teks bawaan yang tertulis
                                    sebagai contoh di dalamnya.
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="tutupIsiBagian" aria-label="Tutup"
                                class="-mr-1 shrink-0 rounded-control p-1.5 text-ink-faint
                                       transition-colors hover:bg-mist hover:text-ink">
                            <x-icon.admin name="close" size="h-[18px] w-[18px]" />
                        </button>
                    </div>

                    {{-- ── Badan ───────────────────────────────────────── --}}
                    <div class="admin-scroll min-h-0 flex-1 space-y-5 overflow-y-auto overscroll-contain p-6">

                        <section class="rounded-corner border border-line bg-canvas p-5">

                            {{-- Sakelar bahasa mengatur SELURUH isian di kartu ini
                                 sekaligus, jadi ia berdiri di kepala kartunya. --}}
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <h3 class="font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Teks
                                </h3>

                                <div class="inline-flex rounded-control border border-line bg-mist p-1">
                                    @foreach(['en' => 'English', 'id' => 'Indonesia'] as $kode => $sebutan)
                                        @php
                                            /* Titik penanda: bahasa ini masih kosong sementara
                                               bahasa satunya sudah diisi.

                                               Perlu terlihat, karena akibatnya tidak kentara —
                                               halaman berbahasa itu akan menampilkan teks
                                               bahasa satunya, bukan teks bawaan yang sudah
                                               diterjemahkan. Dari panel, keduanya sama-sama
                                               tampak "belum diisi". */
                                            $terisi = fn ($l) => collect($isiBagian[$l] ?? [])
                                                ->contains(fn ($v) => filled(is_string($v) ? trim($v) : $v)
                                                    && $v !== '<p><br></p>');

                                            $lain = $kode === 'en' ? 'id' : 'en';
                                            $timpang = ! $terisi($kode) && $terisi($lain);
                                        @endphp

                                        <button type="button" wire:click="$set('activeTab', '{{ $kode }}')"
                                                @class([
                                                    'inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5
                                                     text-[12px] font-semibold transition-colors',
                                                    'bg-canvas text-ink shadow-[0_1px_2px_rgba(26,29,27,0.08)]'
                                                        => $activeTab === $kode,
                                                    'text-ink-muted hover:text-ink' => $activeTab !== $kode,
                                                ])>
                                            {{ $sebutan }}

                                            @if($timpang)
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-status-new"
                                                      title="Belum diisi — halaman berbahasa ini akan memakai teks bahasa satunya"
                                                      aria-label="Belum diisi"></span>
                                            @endif
                                        </button>
                                    @endforeach
                                    <div class="ml-2 border-l border-line pl-2 flex items-center">
                                        <button type="button" wire:click="autoTranslateSection" wire:loading.attr="disabled" wire:target="autoTranslateSection"
                                            title="Terjemahkan ID ke EN otomatis"
                                            class="inline-flex items-center gap-1.5 rounded-[5px] px-3 py-1.5 text-[12px] font-semibold bg-brand/10 text-brand hover:bg-brand/20 transition-colors">
                                            <span wire:loading.remove wire:target="autoTranslateSection">🌐 Auto EN</span>
                                            <span wire:loading wire:target="autoTranslateSection">⏳ ...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @php
                                $adaTimpang = collect(['en', 'id'])->contains(function ($l) use ($isiBagian) {
                                    $isi = fn ($x) => collect($isiBagian[$x] ?? [])
                                        ->contains(fn ($v) => filled(is_string($v) ? trim($v) : $v)
                                            && $v !== '<p><br></p>');

                                    return ! $isi($l) && $isi($l === 'en' ? 'id' : 'en');
                                });
                            @endphp

                            @if($adaTimpang)
                                <div class="mb-4 flex items-start gap-2.5 rounded-control border border-status-new/30
                                            bg-status-new/5 px-3.5 py-2.5">
                                    <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center
                                                 rounded-full bg-status-new text-white">
                                        <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor"
                                                  stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>

                                    <p class="min-w-0 text-[12px] leading-relaxed text-ink-muted">
                                        Baru satu bahasa yang diisi. Bahasa yang kosong akan menampilkan
                                        teks bahasa satunya di situs — <span class="font-semibold text-ink">bukan</span>
                                        teks bawaan yang sudah diterjemahkan. Isi keduanya, atau kosongkan
                                        keduanya supaya bawaannya yang dipakai.
                                    </p>
                                </div>
                            @endif

                            {{-- Kedua bahasa tetap digambar, yang tidak aktif cuma
                                 disembunyikan: isian yang elemennya lenyap membuat
                                 Livewire kehilangan nilai yang sudah diketik. --}}
                            @foreach(['en', 'id'] as $bahasa)
                                <div @class(['space-y-4', 'hidden' => $activeTab !== $bahasa])>
                                    @foreach($bidang as $b)
                                        @php
                                            $kunci  = 'isiBagian.' . $bahasa . '.' . $b['nama'];
                                            $contoh = __($b['bawaan'], [], $bahasa);
                                        @endphp

                                        {{-- Pemisah kelompok: bagian About punya empat
                                             kartu pilar di bawah judulnya, dan sebelas
                                             kolom beruntun tanpa penanda sulit dibaca. --}}
                                        @if(! empty($b['kelompok']))
                                            <p class="border-t border-line pt-4 font-ui text-[12px] font-bold
                                                      uppercase tracking-[0.08em] text-ink-faint">
                                                {{ $b['kelompok'] }}
                                            </p>
                                        @endif

                                        <div>
                                            <label for="{{ $kunci }}"
                                                   class="block text-[12px] font-semibold text-ink-faint">
                                                {{ $b['label'] }}
                                            </label>

                                            @if($b['jenis'] === 'kaya')
                                                <x-admin.editor :model="$kunci"
                                                                :value="data_get($isiBagian, $bahasa . '.' . $b['nama'], '')"
                                                                :kunci="$bagianDibuka . '-' . $bahasa"
                                                                :label="$b['label']"
                                                                :placeholder="$contoh"
                                                                tinggi="min-h-[160px]" />
                                            @elseif($b['jenis'] === 'panjang')
                                                <textarea id="{{ $kunci }}" wire:model="{{ $kunci }}" rows="3"
                                                          placeholder="{{ $contoh }}"
                                                          class="admin-control mt-2 resize-y leading-relaxed"></textarea>
                                            @else
                                                <input type="text" id="{{ $kunci }}"
                                                       wire:model="{{ $kunci }}"
                                                       placeholder="{{ $contoh }}"
                                                       class="admin-control mt-2">
                                            @endif

                                            @if(! empty($b['catatan']))
                                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                                    {{ $b['catatan'] }}
                                                </p>
                                            @endif

                                            @error($kunci)
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </section>

                        {{-- Pengaturan bukan-teks. Berdiri di kartu sendiri, di luar
                             sakelar bahasa: nilainya sama di bahasa mana pun, dan
                             menaruhnya di dalam kartu berbahasa membuat orang
                             mengira ia perlu diisi dua kali. --}}
                        @if($opsi)
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Pengaturan
                                </h3>

                                <div class="space-y-4">
                                    @foreach($opsi as $o)
                                        <div>
                                            <label for="opsi-{{ $o['nama'] }}"
                                                   class="block text-[12px] font-semibold text-ink-faint">
                                                {{ $o['label'] }}
                                            </label>

                                            <input type="number" id="opsi-{{ $o['nama'] }}"
                                                   wire:model="opsiBagian.{{ $o['nama'] }}"
                                                   min="{{ $o['min'] }}" max="{{ $o['max'] }}"
                                                   class="admin-control mt-2 max-w-[140px] tabular-nums">

                                            @if(! empty($o['catatan']))
                                                <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                                    {{ $o['catatan'] }}
                                                </p>
                                            @endif

                                            @error('opsiBagian.' . $o['nama'])
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if($berfoto)
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Foto
                                </h3>

                                <div class="grid grid-cols-2 gap-3 sm:max-w-[420px]">
                                    @if($fotoAda)
                                        <div class="relative flex aspect-[16/9] items-center justify-center
                                                    overflow-hidden rounded-control border border-line bg-mist/40">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gambarBagianLama) }}"
                                                 alt="" class="h-full w-full object-cover">

                                            <button type="button" wire:click="hapusGambarBagian"
                                                    title="Hapus foto" aria-label="Hapus foto"
                                                    class="absolute right-1.5 top-1.5 inline-flex h-7 w-7 items-center
                                                           justify-center rounded-control bg-canvas/90 text-ink-muted
                                                           transition-colors hover:text-status-rejected">
                                                <x-icon.admin name="trash" size="h-4 w-4" />
                                            </button>
                                        </div>
                                    @elseif(filled($gambarBagianLama))
                                        <div class="flex aspect-[16/9] items-center justify-center rounded-control
                                                    border border-status-rejected/30 bg-status-rejected/5 px-2 text-center">
                                            <span class="text-[11px] font-bold text-status-rejected">Berkas hilang</span>
                                        </div>
                                    @endif

                                    @if($gambarBagian)
                                        @php
                                            try {
                                                $pratinjau = $gambarBagian->temporaryUrl();
                                            } catch (\Throwable $e) {
                                                $pratinjau = null;
                                            }
                                        @endphp

                                        <div class="relative flex aspect-[16/9] items-center justify-center
                                                    overflow-hidden rounded-control border border-dashed
                                                    border-brand/50 bg-brand-wash">
                                            @if($pratinjau)
                                                <img src="{{ $pratinjau }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <span class="px-2 text-center text-[11px] leading-snug text-ink-muted">
                                                    {{ $gambarBagian->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-1.5 top-1.5 rounded-full bg-brand px-1.5
                                                         text-[10px] font-bold text-white">Baru</span>
                                        </div>
                                    @endif

                                    <label title="{{ $fotoAda ? 'Ganti foto' : 'Pilih foto' }}"
                                           class="flex aspect-[16/9] cursor-pointer items-center justify-center
                                                  rounded-control border-2 border-dashed border-line-strong bg-mist/40
                                                  text-ink-faint transition-colors hover:border-brand
                                                  hover:bg-brand-wash hover:text-brand
                                                  focus-within:border-brand focus-within:text-brand">

                                        <input type="file" wire:model="gambarBagian" accept="image/*"
                                               aria-label="{{ $fotoAda ? 'Ganti foto' : 'Pilih foto' }}"
                                               class="sr-only">

                                        <span wire:loading.remove wire:target="gambarBagian">
                                            <svg class="h-7 w-7" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                                <path d="M18 9v18M9 18h18" stroke="currentColor"
                                                      stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <svg wire:loading wire:target="gambarBagian"
                                             class="h-6 w-6 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                                  stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </label>
                                </div>

                                <p class="mt-3 text-[12px] leading-relaxed text-ink-faint">
                                    {{ \App\Livewire\Admin\PageIndex::CATATAN_FOTO[$bagianDibuka] ?? '' }}
                                </p>

                                @error('gambarBagian')
                                    <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                @enderror
                            </section>
                        @endif
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-3 border-t border-line
                                bg-mist/40 px-6 py-4">
                        <button type="button" wire:click="tutupIsiBagian" class="admin-btn">Batal</button>

                        <button type="submit" wire:loading.attr="disabled"
                                wire:target="simpanIsiBagian, gambarBagian"
                                class="admin-btn admin-btn-brand disabled:opacity-60">
                            <svg wire:loading wire:target="simpanIsiBagian"
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            Simpan isi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


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

                                         Penyunting kaya, bukan lagi kotak HTML mentah.
                                         Keduanya tetap digambar dan yang tidak aktif cuma
                                         disembunyikan: isian yang elemennya lenyap membuat
                                         Livewire kehilangan nilainya. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-[12px] font-semibold text-ink-faint">Isi halaman</label>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <x-admin.editor model="content_en" :value="$content_en"
                                                            :kunci="$page_id ?? 'baru'"
                                                            label="Isi halaman dalam bahasa Inggris"
                                                            placeholder="Tulis isi halaman…" />

                                            @error('content_en')
                                                <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <x-admin.editor model="content_id" :value="$content_id"
                                                            :kunci="$page_id ?? 'baru'"
                                                            label="Isi halaman dalam bahasa Indonesia"
                                                            placeholder="Tulis isi halaman…" />

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
