<div class="mx-auto max-w-[1400px]">

    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-admin-display text-heading">
                Halaman
            </h1>
            <p class="mt-1.5 text-admin-body text-ink-muted">
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

            <p class="min-w-0 flex-1 pt-1 text-admin-strong text-brand-deep">
                {{ session('message') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-brand/70 transition-colors hover:bg-brand/10 hover:text-brand-deep">
                <x-icon.admin name="close" size="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- ══ HALAMAN PUBLIK — kesepuluh halaman dalam SATU daftar, urut seperti
         pengunjung menemuinya: beranda dulu, footer terakhir. ══ --}}
    <section class="card mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="page" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-heading">Halaman publik</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Isi tiap halaman yang tampil di situs publik, urut seperti pengunjung menemuinya.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ count(\App\Livewire\Admin\PageIndex::DAFTAR_HALAMAN) }}</span>
                halaman
            </span>
        </div>

        <div class="p-5">
            <ul class="overflow-hidden rounded-corner border border-line">
                @foreach(\App\Livewire\Admin\PageIndex::DAFTAR_HALAMAN as $i => $hal)
                    @php
                        $berbagian = $hal['jenis'] === 'susunan';
                        $bagian    = $berbagian
                            ? ($hal['id'] === 'home' ? $home_sections : $profile_sections)
                            : [];

                        $terbentang = $susunanDibuka === $hal['id'];

                        /* Sudah ditulis sendiri atau masih memakai teks bawaan.
                           Dari daftar ini tidak ada cara lain membedakannya. */
                        $isiHal = $halaman_publik[$hal['id']]['isi'] ?? [];

                        $bahasaTerisi = collect(['en', 'id'])->filter(
                            fn ($l) => collect($isiHal[$l] ?? [])->contains(
                                fn ($v) => filled(is_string($v) ? trim($v) : $v) && $v !== '<p><br></p>'
                            )
                        );

                        $alamat = $hal['rute']
                            ? \Illuminate\Support\Str::of(route($hal['rute'], [], false))->start('/')
                            : null;
                    @endphp

                    <li @class(['border-b border-line last:border-0', 'bg-mist/40' => $terbentang])>

                        <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-control
                                         bg-mist text-admin-caption font-semibold tabular-nums text-ink-muted">
                                {{ $i + 1 }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <span class="block truncate text-admin-strong text-ink">{{ $hal['nama'] }}</span>

                                <span class="mt-0.5 block truncate text-admin-caption text-ink-faint">
                                    @if($alamat)
                                        <span class="admin-code">{{ $alamat }}</span>
                                    @else
                                        {{-- Footer tidak punya alamatnya sendiri. --}}
                                        Tergambar di semua halaman
                                    @endif
                                </span>
                            </div>

                            @if($berbagian)
                                {{-- Untuk halaman berbagian, yang berguna sekilas
                                     bukan "sudah ditulis atau belum" melainkan
                                     berapa bagiannya yang menyala. --}}
                                <span class="shrink-0 rounded-full border border-line bg-canvas px-2.5 py-1
                                             text-admin-caption font-semibold tabular-nums text-ink-muted"
                                      title="Bagian yang tampil di halaman ini">
                                    {{ collect($bagian)->where('active', true)->count() }}/{{ count($bagian) }} bagian
                                </span>
                            @elseif($bahasaTerisi->count() === 2)
                                <span class="shrink-0 rounded-full bg-brand/10 px-2.5 py-1
                                             text-admin-caption font-semibold text-brand"
                                      title="Kedua bahasa sudah diisi">Ditulis</span>
                            @elseif($bahasaTerisi->count() === 1)
                                <span class="shrink-0 rounded-full bg-status-new/10 px-2.5 py-1
                                             text-admin-caption font-semibold text-status-new"
                                      title="Baru bahasa {{ $bahasaTerisi->first() === 'en' ? 'Inggris' : 'Indonesia' }} yang diisi">
                                    Satu bahasa
                                </span>
                            @else
                                <span class="shrink-0 rounded-full border border-line px-2.5 py-1
                                             text-admin-caption font-semibold text-ink-faint"
                                      title="Masih memakai teks bawaan yang sudah diterjemahkan">Bawaan</span>
                            @endif

                            <div class="flex shrink-0 items-center gap-1.5">
                                @if($hal['rute'])
                                    <a href="{{ route($hal['rute']) }}" target="_blank" rel="noopener"
                                       title="Lihat {{ $hal['nama'] }} di situs"
                                       aria-label="Lihat {{ $hal['nama'] }} di situs"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                              border border-line bg-canvas text-ink-muted transition-colors
                                              hover:border-brand hover:text-brand">
                                        <x-icon.admin name="external" size="h-4 w-4" />
                                    </a>
                                @else
                                    {{-- Ruang kosong selebar tombolnya, supaya tombol
                                         di baris ini tetap sekolom dengan baris lain. --}}
                                    <span class="inline-block h-8 w-8" aria-hidden="true"></span>
                                @endif

                                @if($berbagian)
                                    <button type="button" wire:click="bukaSusunan('{{ $hal['id'] }}')"
                                            aria-expanded="{{ $terbentang ? 'true' : 'false' }}"
                                            title="{{ $terbentang ? 'Tutup' : 'Buka' }} susunan {{ $hal['nama'] }}"
                                            @class([
                                                'inline-flex h-8 items-center gap-1.5 rounded-control border px-2.5',
                                                'text-admin-label font-semibold transition-colors',
                                                'border-brand bg-brand-wash text-brand' => $terbentang,
                                                'border-line bg-canvas text-ink-muted hover:border-brand hover:text-brand' => ! $terbentang,
                                            ])>
                                        <svg @class(['h-3.5 w-3.5 shrink-0 transition-transform', 'rotate-180' => $terbentang])
                                             viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                            <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Susunan
                                    </button>
                                @else
                                    <button type="button" wire:click="ubahIsiHalaman('{{ $hal['id'] }}')"
                                            title="Ubah isi {{ $hal['nama'] }}"
                                            aria-label="Ubah isi {{ $hal['nama'] }}"
                                            class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                                   bg-canvas px-2.5 text-admin-label font-semibold text-ink-muted
                                                   transition-colors hover:border-brand hover:text-brand">
                                        <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                        Ubah isi
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- ── Daftar bagian, terbentang di tempat. Metodenya
                             berbeda antara beranda dan Profile, jadi namanya
                             diambil dari satu peta di bawah. ── --}}
                        @if($berbagian && $terbentang)
                            @php
                                $aksi = $hal['id'] === 'home'
                                    ? ['naik' => 'moveSectionUp', 'turun' => 'moveSectionDown',
                                       'sakelar' => 'toggleSectionActive', 'ubah' => 'ubahIsiBagian']
                                    : ['naik' => 'moveProfilUp', 'turun' => 'moveProfilDown',
                                       'sakelar' => 'toggleProfilActive', 'ubah' => 'ubahIsiProfil'];
                            @endphp

                            <div class="border-t border-line bg-canvas px-4 py-4">
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-admin-label text-ink-muted">
                                        Urutan dan tampil-tidaknya tiap bagian di {{ $hal['nama'] }}.
                                    </p>

                                    {{-- Daftar ini menyimpan sendiri tiap
                                         kali disentuh — tanpa keterangan ini
                                         orang mengira perubahannya menunggu
                                         tombol Simpan. --}}
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-line
                                                 bg-mist px-3 py-1 text-admin-caption font-semibold text-ink-muted">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-brand" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="m4 8.4 2.8 2.8L12 5.6" stroke="currentColor" stroke-width="1.8"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Tersimpan otomatis
                                    </span>
                                </div>

                                <ul class="overflow-hidden rounded-corner border border-line">
                                    @foreach($bagian as $j => $bg)
                                        <li @class([
                                            'flex flex-wrap items-center gap-3 border-b border-line px-4 py-3 last:border-0',
                                            'bg-canvas'  => $bg['active'],
                                            'bg-mist/40' => ! $bg['active'],
                                        ])>
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-control
                                                         bg-mist text-admin-caption font-semibold tabular-nums text-ink-muted">
                                                {{ $j + 1 }}
                                            </span>

                                            <span @class([
                                                'min-w-0 flex-1 truncate text-admin-body',
                                                'font-semibold text-ink' => $bg['active'],
                                                'text-ink-faint'         => ! $bg['active'],
                                            ])>{{ $bg['name'] }}</span>

                                            <div class="flex shrink-0 items-center gap-1.5">
                                                {{-- Tombol di ujung daftar
                                                     digambar MATI, bukan
                                                     dihilangkan: kalau
                                                     hilang, tombol di baris
                                                     lain ikut bergeser dan
                                                     sasarannya meleset. --}}
                                                @foreach([
                                                    ['arah' => $aksi['naik'],  'mati' => $j === 0,
                                                     'nama' => 'Naikkan',  'jalur' => 'M10 15.5V5.4M5.4 10 10 5.4l4.6 4.6'],
                                                    ['arah' => $aksi['turun'], 'mati' => $j === count($bagian) - 1,
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
                                                        <button type="button" wire:click="{{ $geser['arah'] }}('{{ $bg['id'] }}')"
                                                                title="{{ $geser['nama'] }} {{ $bg['name'] }}"
                                                                aria-label="{{ $geser['nama'] }} {{ $bg['name'] }}"
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

                                                {{-- Hanya untuk bagian yang
                                                     isinya memang bisa
                                                     disunting. Bagian lain
                                                     merakit diri dari data,
                                                     dan tombol yang membuka
                                                     borang kosong menjanjikan
                                                     yang tidak ada. --}}
                                                @if($hal['id'] === 'profile'
                                                    || array_key_exists($bg['id'], \App\Livewire\Admin\PageIndex::BIDANG_BAGIAN))
                                                    <button type="button" wire:click="{{ $aksi['ubah'] }}('{{ $bg['id'] }}')"
                                                            title="Ubah isi {{ $bg['name'] }}"
                                                            aria-label="Ubah isi {{ $bg['name'] }}"
                                                            class="inline-flex h-8 items-center gap-1.5 rounded-control border border-line
                                                                   bg-canvas px-2.5 text-admin-label font-semibold text-ink-muted
                                                                   transition-colors hover:border-brand hover:text-brand">
                                                        <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                                        Ubah isi
                                                    </button>
                                                @else
                                                    <span class="inline-block h-8 w-[86px]" aria-hidden="true"></span>
                                                @endif

                                                {{-- Sakelar tampil. wire:click, bukan wire:model:
                                                     nilainya hidup di dalam larik JSON, dan
                                                     metodenya yang menyimpannya. --}}
                                                <button type="button" wire:click="{{ $aksi['sakelar'] }}('{{ $bg['id'] }}')"
                                                        role="switch" aria-checked="{{ $bg['active'] ? 'true' : 'false' }}"
                                                        aria-label="{{ $bg['active'] ? 'Sembunyikan' : 'Tampilkan' }} {{ $bg['name'] }}"
                                                        title="{{ $bg['active'] ? 'Sembunyikan dari ' . $hal['nama'] : 'Tampilkan di ' . $hal['nama'] }}"
                                                        class="relative ml-1 inline-flex shrink-0 items-center">
                                                    <span @class([
                                                        'block h-6 w-11 rounded-full transition-colors',
                                                        'bg-brand'     => $bg['active'],
                                                        'bg-mist-deep' => ! $bg['active'],
                                                    ])></span>

                                                    <span @class([
                                                        'pointer-events-none absolute left-0.5 block h-5 w-5 rounded-full bg-white',
                                                        'shadow-[0_1px_3px_rgba(26,29,27,0.28)] transition-transform',
                                                        'translate-x-5' => $bg['active'],
                                                    ])></span>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <p class="admin-hint">
                                    Bagian yang dimatikan tetap tersimpan isinya — ia cuma tidak digambar
                                    di {{ $hal['nama'] }}.
                                </p>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <p class="admin-hint">
                Isian yang dikosongkan memakai teks bawaan yang sudah diterjemahkan.
                Isi kedua bahasa, atau kosongkan keduanya.
            </p>
        </div>
    </section>

    {{-- ══ HALAMAN BUATAN SENDIRI — kartu tersendiri, bukan menyambung di
         bawah kesepuluh halaman di atas. Keduanya berbeda jenis. ══ --}}
    <section class="card mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control bg-brand-wash text-brand">
                    <x-icon.admin name="news" size="h-[18px] w-[18px]" />
                </span>

                <div>
                    <h2 class="text-admin-title text-heading">Halaman buatan sendiri</h2>
                    <p class="mt-0.5 text-admin-label text-ink-muted">
                        Halaman tambahan yang alamatnya berawal <span class="admin-code">/page/</span>.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-admin-label font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ $pages->count() }}</span>
                halaman
            </span>
        </div>

        <div class="p-5">
            @if($pages->isEmpty())
                <div class="rounded-corner border border-dashed border-line-strong bg-mist/40 px-6 py-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-mist text-ink-faint">
                        <x-icon.admin name="page" size="h-5 w-5" />
                    </span>

                    <p class="mt-4 text-admin-title text-heading">Belum ada halaman buatan sendiri</p>

                    <p class="mx-auto mt-1.5 max-w-[380px] text-admin-body text-ink-muted">
                        Dipakai untuk halaman yang tidak ada di daftar atas — kebijakan privasi,
                        syarat dan ketentuan, dan sejenisnya.
                    </p>

                    <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                  stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        Tambah halaman pertama
                    </button>
                </div>
            @else
                <ul class="overflow-hidden rounded-corner border border-line">
                    @foreach($pages as $halaman)
                        <li wire:key="page-{{ $halaman->id }}" class="flex flex-wrap items-center gap-3 border-b border-line px-4 py-3 last:border-0">
                            <div class="min-w-0 flex-1">
                                <span class="block truncate text-admin-strong text-ink">
                                    {{ $halaman->translated_title ?: $halaman->slug }}
                                </span>

                                <span class="admin-code mt-0.5 block truncate text-admin-caption text-ink-faint">
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
                                               bg-canvas px-2.5 text-admin-label font-semibold text-ink-muted
                                               transition-colors hover:border-brand hover:text-brand">
                                    <x-icon.admin name="edit" size="h-3.5 w-3.5" />
                                    Ubah isi
                                </button>

                                <x-admin.confirm-delete metode="delete"
                                                        :id="$halaman->id"
                                                        :nama="$halaman->translated_title ?: $halaman->slug"
                                                        judul="Hapus halaman?"
                                                        tombol="Ya, hapus halaman">
                                    Tautan /page/{{ $halaman->slug }} akan mati, dan pengunjung yang membukanya menemukan halaman tidak ditemukan.
                                </x-admin.confirm-delete>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- ══ MODAL ISI BAGIAN — satu modal untuk beranda dan halaman publik:
         bentuk isinya sama persis, teks per bahasa dan kadang foto. ══ --}}
    @if($bagianDibuka)
        @php
            $daftarBidang = match ($jenisDibuka) {
                'bagian' => \App\Livewire\Admin\PageIndex::BIDANG_BAGIAN,
                'profil' => \App\Livewire\Admin\PageIndex::BIDANG_PROFIL,
                default  => \App\Livewire\Admin\PageIndex::BIDANG_HALAMAN,
            };

            $bidang = $daftarBidang[$bagianDibuka] ?? [];

            $opsi = match ($jenisDibuka) {
                'bagian' => \App\Livewire\Admin\PageIndex::OPSI_BAGIAN[$bagianDibuka] ?? [],
                'profil'  => \App\Livewire\Admin\PageIndex::opsiProfil()[$bagianDibuka] ?? [],
                'halaman' => \App\Livewire\Admin\PageIndex::opsiHalaman()[$bagianDibuka] ?? [],
                default   => [],
            };

            $skemaOpsi = collect($opsi)->keyBy('nama');

            /* Medan dipecah jadi kartu-kartu menurut 'kelompok'.
             *
             * 'kelompok' menandai medan PERTAMA sebuah kelompok; medan
             * sesudahnya ikut kelompok terakhir yang disebut. Karena itu
             * kelompok harus berupa deretan yang bersambung — dan karena
             * medan pertama tiap himpunan selalu membawa kelompok, tidak ada
             * medan yang bisa jatuh ke luar kartu mana pun.
             *
             * Dipakai daftar berurutan, bukan array berkunci nama kelompok:
             * nama yang kebetulan sama tidak boleh diam-diam menyatukan dua
             * kartu yang berjauhan letaknya.
             *
             * Slot ber-'jenis' => 'opsi' bukan medan teks melainkan penunjuk
             * tempat: ia diganti skema opsi bernama sama, supaya pengaturan
             * bukan-teks bisa berdiri DI TENGAH teks — tahun tiap tonggak
             * sejarah tepat di atas judul tonggaknya, bukan terlempar ke kartu
             * terpisah. 'bentuk' menandai mana yang mana saat menggambar. */
            $kartuBidang  = [];
            $opsiTerpakai = [];

            foreach ($bidang as $b) {
                if (($b['jenis'] ?? null) === 'opsi') {
                    /* Slot tanpa skema dilewati, bukan digambar kosong: yang
                       tergambar akan berupa kotak tanpa label yang tidak
                       tersimpan ke mana pun. */
                    if (! $skemaOpsi->has($b['nama'])) {
                        continue;
                    }

                    $medan = $skemaOpsi->get($b['nama']);
                    $medan['bentuk'] = 'opsi';
                    $opsiTerpakai[] = $b['nama'];
                } elseif (($b['jenis'] ?? null) === 'gambar') {
                    /* Slot gambar: satu unggahan tanpa versi per bahasa. Gambar
                       tonggak sejarah sama di kedua bahasa — yang berbeda cuma
                       kalimat di sebelahnya. */
                    $medan = $b;
                    $medan['bentuk'] = 'gambar';
                } else {
                    $medan = $b;
                    $medan['bentuk'] = 'teks';
                }

                if (! empty($b['kelompok']) || $kartuBidang === []) {
                    $kartuBidang[] = ['nama' => $b['kelompok'] ?? 'Teks', 'medan' => []];
                }

                $kartuBidang[array_key_last($kartuBidang)]['medan'][] = $medan;
            }

            /* Opsi yang tidak ditempatkan di kartu mana pun tetap perlu tempat:
               ia jatuh ke kartu "Pengaturan" tersendiri, seperti sebelumnya. */
            $opsiSisa = array_values(array_filter(
                $opsi,
                fn ($o) => ! in_array($o['nama'], $opsiTerpakai, true)
            ));

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
                                    class="truncate text-admin-title text-heading">
                                    {{ $sebutanJenis }} · {{ $namaBagian }}
                                </h2>
                                <p class="mt-0.5 text-admin-label text-ink-muted">
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

                        {{-- Kendali bahasa berdiri di LUAR kartu-kartu teks:
                             isian kini terbagi ke beberapa kartu, dan sakelar
                             di kepala salah satunya akan tampak hanya
                             mengurus kartu itu. --}}
                        {{-- justify-end, bukan justify-between: judul "Teks per
                             bahasa" di kiri sudah dilepas, dan tanpa penggantinya
                             justify-between akan melemparkan kendalinya ke tepi
                             kiri. --}}
                        <div class="flex flex-wrap items-center justify-end gap-3">

                            {{-- Tab bahasa dan tombol Terjemahkan berdiri
                                 TERPISAH: kendali bersegmen menjanjikan
                                 "pilih salah satu", dan tombol tindakan di
                                 dalam bingkai yang sama mengingkari janji
                                 itu. --}}
                            <div class="flex shrink-0 flex-wrap items-center gap-2">

                                <div class="inline-flex shrink-0 items-center gap-0.5 rounded-full
                                            border border-line bg-mist p-0.5"
                                     role="group" aria-label="Bahasa yang sedang disunting">
                                    @foreach(['id' => 'Indonesia', 'en' => 'English'] as $kode => $sebutan)
                                        @php
                                            /* Titik penanda: bahasa ini masih kosong sementara
                                               bahasa satunya sudah diisi.

                                               Perlu terlihat, karena akibatnya tidak kentara —
                                               halaman berbahasa itu akan menampilkan teks bahasa
                                               satunya, bukan teks bawaan yang sudah diterjemahkan.
                                               Dari panel, keduanya sama-sama tampak "belum
                                               diisi". */
                                            $terisi = fn ($l) => collect($isiBagian[$l] ?? [])
                                                ->contains(fn ($v) => filled(is_string($v) ? trim($v) : $v)
                                                    && $v !== '<p><br></p>');

                                            $lain    = $kode === 'en' ? 'id' : 'en';
                                            $timpang = ! $terisi($kode) && $terisi($lain);
                                        @endphp

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
                                            @if($timpang)
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-status-new"
                                                      title="Belum diisi — halaman berbahasa ini akan memakai teks bahasa satunya"
                                                      aria-label="Belum diisi"></span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>

                                <button type="button" wire:click="autoTranslateSection"
                                        wire:loading.attr="disabled" wire:target="autoTranslateSection"
                                        title="Salin isian Indonesia ke English, lalu terjemahkan"
                                        class="admin-btn admin-btn-quiet shrink-0 !py-1.5 disabled:opacity-60">
                                    <svg wire:loading wire:target="autoTranslateSection"
                                         class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 16 16"
                                         fill="none" aria-hidden="true">
                                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>

                                    <x-icon.admin name="send" size="h-3.5 w-3.5"
                                                  class="shrink-0 text-brand"
                                                  wire:loading.remove wire:target="autoTranslateSection" />

                                    Terjemahkan
                                </button>
                            </div>
                        </div>

                        {{-- Pesan gagal-terjemah, tepat di bawah tombol yang
                             memicunya — sebelumnya ia gagal tanpa mengatakan
                             apa pun. --}}
                        @if($galatTerjemah)
                            <p class="flex items-start gap-1.5 text-admin-caption text-danger" role="alert">
                                <svg class="mt-px h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                    <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                                </svg>
                                {{ $galatTerjemah }}
                            </p>
                        @endif

                        @php
                            $adaTimpang = collect(['en', 'id'])->contains(function ($l) use ($isiBagian) {
                                $isi = fn ($x) => collect($isiBagian[$x] ?? [])
                                    ->contains(fn ($v) => filled(is_string($v) ? trim($v) : $v)
                                        && $v !== '<p><br></p>');

                                return ! $isi($l) && $isi($l === 'en' ? 'id' : 'en');
                            });
                        @endphp

                        @if($adaTimpang)
                            <div class="flex items-start gap-2.5 rounded-control border border-status-new/30
                                        bg-status-new/5 px-3.5 py-2.5">
                                <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center
                                             rounded-full bg-status-new text-white">
                                    <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor"
                                              stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </span>

                                <p class="min-w-0 text-admin-label text-ink-muted">
                                    Baru satu bahasa yang diisi. Bahasa yang kosong akan menampilkan
                                    teks bahasa satunya di situs — <span class="font-semibold text-ink">bukan</span>
                                    teks bawaan yang sudah diterjemahkan. Isi keduanya, atau kosongkan
                                    keduanya supaya bawaannya yang dipakai.
                                </p>
                            </div>
                        @endif

                        {{-- Kartu digambar SEKALI, bukan sekali per bahasa.
                             Yang bertukar mengikuti sakelar hanya kotak
                             teksnya; kartu, judul, dan kolom angkanya tetap
                             berdiri. --}}
                        <div class="space-y-5">
                            @foreach($kartuBidang as $kartu)
                                <section class="rounded-corner border border-line bg-canvas p-5">
                                    <h3 class="mb-4 text-admin-title text-heading">
                                        {{ $kartu['nama'] }}
                                    </h3>

                                    <div class="space-y-4">
                                        @foreach($kartu['medan'] as $b)
                                            <div>
                                                @if($b['bentuk'] === 'gambar')
                                                    @php
                                                        $adaLama = filled($gambarTonggakLama[$b['nama']] ?? null)
                                                            && \Illuminate\Support\Facades\Storage::disk('public')
                                                                ->exists($gambarTonggakLama[$b['nama']]);
                                                        $baru = $gambarTonggak[$b['nama']] ?? null;
                                                    @endphp

                                                    <span class="block text-admin-label text-ink-faint">{{ $b['label'] }}</span>

                                                    <div class="mt-2 flex items-center gap-3">
                                                        {{-- Petak pratinjau 64px, bukan bidang unggah
                                                             selebar kartu: enam tonggak berarti enam
                                                             kotak di satu modal, dan yang selebar kartu
                                                             membuat daftar tonggaknya jadi enam layar. --}}
                                                        <span class="flex h-16 w-16 shrink-0 items-center justify-center
                                                                     overflow-hidden rounded-control border border-line bg-mist">
                                                            @if($baru)
                                                                <img src="{{ $baru->temporaryUrl() }}" alt=""
                                                                     class="h-full w-full object-cover">
                                                            @elseif($adaLama)
                                                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gambarTonggakLama[$b['nama']]) }}"
                                                                     alt="" class="h-full w-full object-cover">
                                                            @else
                                                                <svg class="h-5 w-5 text-ink-faint" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/>
                                                                    <circle cx="8.6" cy="10" r="1.7" stroke="currentColor" stroke-width="1.7"/>
                                                                    <path d="m4 17 5-4.6 3.6 3.2L16 12l4 4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            @endif
                                                        </span>

                                                        <div class="min-w-0">
                                                            <label class="admin-btn admin-btn-quiet cursor-pointer !py-1.5">
                                                                <input type="file" wire:model="gambarTonggak.{{ $b['nama'] }}"
                                                                       accept="image/*" class="sr-only">
                                                                <span wire:loading.remove wire:target="gambarTonggak.{{ $b['nama'] }}">
                                                                    {{ $adaLama || $baru ? 'Ganti gambar' : 'Pilih gambar' }}
                                                                </span>
                                                                <span wire:loading wire:target="gambarTonggak.{{ $b['nama'] }}">Mengunggah…</span>
                                                            </label>

                                                            @if($adaLama)
                                                                <button type="button"
                                                                        wire:click="hapusGambarTonggak('{{ $b['nama'] }}')"
                                                                        class="ml-1 text-admin-label text-danger hover:underline">
                                                                    Hapus
                                                                </button>
                                                            @endif

                                                            <p class="admin-hint">
                                                                JPG/PNG/WebP, maksimal 4 MB. Dikosongkan berarti
                                                                tonggak ini tampil tanpa gambar.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @error('gambarTonggak.' . $b['nama'])
                                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                    @enderror
                                                @elseif($b['bentuk'] === 'opsi')
                                                    <label for="opsi-{{ $b['nama'] }}"
                                                           class="block text-admin-label text-ink-faint">
                                                        {{ $b['label'] }}
                                                    </label>

                                                    {{-- Kotaknya mengikuti JENIS opsinya. Angka
                                                         memakai kotak angka berbatas; teks memakai
                                                         kotak biasa selebar penuh — alamat YouTube
                                                         tidak muat di kotak 140px, dan kotak angka
                                                         menolak huruf. --}}
                                                    @if(($b['jenis'] ?? 'angka') === 'teks')
                                                        <input type="text" id="opsi-{{ $b['nama'] }}"
                                                               wire:model="opsiBagian.{{ $b['nama'] }}"
                                                               maxlength="255" autocomplete="off"
                                                               placeholder="https://www.youtube.com/watch?v=…"
                                                               class="admin-control mt-2">
                                                    @else
                                                        <input type="number" id="opsi-{{ $b['nama'] }}"
                                                               wire:model="opsiBagian.{{ $b['nama'] }}"
                                                               min="{{ $b['min'] }}" max="{{ $b['max'] }}"
                                                               class="admin-control mt-2 max-w-[140px] tabular-nums">
                                                    @endif

                                                    @if(! empty($b['catatan']))
                                                        <p class="admin-hint">
                                                            {{ $b['catatan'] }}
                                                        </p>
                                                    @endif

                                                    @error('opsiBagian.' . $b['nama'])
                                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                    @enderror
                                                @else
                                                    @foreach(['en', 'id'] as $bahasa)
                                                        @php
                                                            $kunci  = 'isiBagian.' . $bahasa . '.' . $b['nama'];
                                                            $contoh = __($b['bawaan'], [], $bahasa);
                                                        @endphp

                                                        <div @class(['hidden' => $activeTab !== $bahasa])>
                                                            <label for="{{ $kunci }}"
                                                                   class="block text-admin-label text-ink-faint">
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
                                                                <p class="admin-hint">
                                                                    {{ $b['catatan'] }}
                                                                </p>
                                                            @endif

                                                            @error($kunci)
                                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>

                        {{-- Pengaturan bukan-teks, di kartu sendiri di luar
                             sakelar bahasa: nilainya sama di bahasa mana pun. --}}
                        @if($opsiSisa)
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <div class="mb-4">
                                    <h3 class="text-admin-title text-heading">Pengaturan</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">Pilihan tampilan bagian ini, di luar teksnya.</p>
                                </div>

                                <div class="space-y-4">
                                    @foreach($opsiSisa as $o)
                                        <div>
                                            <label for="opsi-{{ $o['nama'] }}"
                                                   class="block text-admin-label text-ink-faint">
                                                {{ $o['label'] }}
                                            </label>

                                            @if(($o['jenis'] ?? 'angka') === 'teks')
                                                <input type="text" id="opsi-{{ $o['nama'] }}"
                                                       wire:model="opsiBagian.{{ $o['nama'] }}"
                                                       maxlength="255" autocomplete="off"
                                                       class="admin-control mt-2">
                                            @else
                                                <input type="number" id="opsi-{{ $o['nama'] }}"
                                                       wire:model="opsiBagian.{{ $o['nama'] }}"
                                                       min="{{ $o['min'] }}" max="{{ $o['max'] }}"
                                                       class="admin-control mt-2 max-w-[140px] tabular-nums">
                                            @endif

                                            @if(! empty($o['catatan']))
                                                <p class="admin-hint">
                                                    {{ $o['catatan'] }}
                                                </p>
                                            @endif

                                            @error('opsiBagian.' . $o['nama'])
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if($berfoto)
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <div class="mb-4">
                                    <h3 class="text-admin-title text-heading">Foto</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">Gambar yang tampil di bagian ini.</p>
                                </div>

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
                                            <span class="text-admin-caption font-semibold text-status-rejected">Berkas hilang</span>
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
                                                <span class="px-2 text-center text-admin-caption text-ink-muted">
                                                    {{ $gambarBagian->getClientOriginalName() }}
                                                </span>
                                            @endif

                                            <span class="absolute left-1.5 top-1.5 rounded-full bg-brand px-1.5
                                                         text-admin-caption font-semibold text-white">Baru</span>
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

                                <p class="admin-hint">
                                    {{ \App\Livewire\Admin\PageIndex::CATATAN_FOTO[$bagianDibuka] ?? '' }}
                                </p>

                                @error('gambarBagian')
                                    <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                @enderror
                            </section>
                        @endif
                    </div>

                    {{-- ── Kaki ────────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-center justify-end gap-3 border-t border-line
                                bg-mist/40 px-6 py-4">
                        <button type="button" wire:click="tutupIsiBagian" class="admin-btn">Batal</button>

                        <button type="submit" wire:loading.attr="disabled"
                                wire:target="simpanIsiBagian, gambarBagian, gambarTonggak"
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
            $galatEn = $errors->hasAny(['label_en', 'title_en', 'content_en']);
            $galatId = $errors->hasAny(['label_id', 'title_id', 'content_id']);

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
                                    class="truncate text-admin-title text-heading">
                                    {{ $page_id ? 'Ubah halaman' : 'Tambah halaman' }}
                                </h2>
                                <p class="mt-0.5 text-admin-label text-ink-muted">
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
                            <p class="min-w-0 text-admin-label text-ink-muted">
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
                                    <div>
                                        <h3 class="text-admin-title text-heading">Isi halaman</h3>
                                        <p class="mt-0.5 text-admin-label text-ink-muted">Label, judul, dan isi halaman dalam dua bahasa.</p>
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
                                                    {{-- Titik merah: tab ini menyimpan galat yang
                                     tidak terlihat karena tertutup. --}}
                                @if(($kode === 'en' && $galatEn) || ($kode === 'id' && $galatId))
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

                                    {{-- Label: kata kecil di ATAS judul di
                                         halaman publik. Boleh kosong — yang
                                         kosong tidak menggambar apa pun di
                                         sana, bukan menggambar petak hampa.

                                         Ditaruh sebelum Judul karena di
                                         halaman jadinya pun ia lebih dulu
                                         dibaca. --}}
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">
                                            Label halaman
                                        </label>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <input type="text" wire:model="label_en" maxlength="60"
                                                   aria-label="Label halaman dalam bahasa Inggris"
                                                   placeholder="mis. Legal"
                                                   class="admin-control">
                                            @error('label_en')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="label_id" maxlength="60"
                                                   aria-label="Label halaman dalam bahasa Indonesia"
                                                   placeholder="mis. Legal"
                                                   class="admin-control">
                                            @error('label_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <p class="admin-hint">
                                            Tergambar sebagai tulisan kecil beraksen emas di atas judul halaman.
                                        </p>
                                    </div>

                                    {{-- Judul mengikuti tab. Keduanya TETAP
                                         di DOM dan yang tidak aktif hanya
                                         disembunyikan: isian yang elemennya
                                         lenyap membuat Livewire kehilangan
                                         nilainya. --}}
                                    <div>
                                        <label class="block text-admin-label text-ink-faint">
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
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <input type="text" wire:model="title_id"
                                                   aria-label="Judul halaman dalam bahasa Indonesia"
                                                   placeholder="mis. Tentang Kami"
                                                   class="admin-control">
                                            @error('title_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Isi halaman, penyunting kaya.
                                         Keduanya tetap digambar dan yang
                                         tidak aktif hanya disembunyikan —
                                         lihat catatan pada Judul di atas. --}}
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <label class="text-admin-label text-ink-faint">Isi halaman</label>
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'en'])>
                                            <x-admin.editor model="content_en" :value="$content_en"
                                                            :kunci="$page_id ?? 'baru'"
                                                            label="Isi halaman dalam bahasa Inggris"
                                                            placeholder="Tulis isi halaman…" />

                                            @error('content_en')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div @class(['mt-2', 'hidden' => $activeTab !== 'id'])>
                                            <x-admin.editor model="content_id" :value="$content_id"
                                                            :kunci="$page_id ?? 'baru'"
                                                            label="Isi halaman dalam bahasa Indonesia"
                                                            placeholder="Tulis isi halaman…" />

                                            @error('content_id')
                                                <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Isi boleh kosong menurut store(),
                                             tapi halaman terbit yang kosong
                                             tergambar hampa di situs publik —
                                             disebut di sini supaya bukan
                                             kejutan. --}}
                                        <p class="admin-hint">
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
                                <div class="mb-4">
                                    <h3 class="text-admin-title text-heading">Alamat</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">Alamat halaman ini di situs publik.</p>
                                </div>

                                <span class="block text-admin-label text-ink-faint">
                                    Alamat di situs publik
                                </span>

                                <div class="mt-2 rounded-control border border-line bg-mist/40 px-3.5 py-2.5">
                                    @if(filled($alamatBaru))
                                        <span class="block break-all text-admin-strong text-ink">
                                            /page/{{ $alamatBaru }}
                                        </span>
                                    @else
                                        <span class="block text-admin-body text-ink-faint">
                                            Belum ada — isi judul bahasa Inggrisnya dulu
                                        </span>
                                    @endif
                                </div>

                                <p class="admin-hint">
                                    Dirangkai otomatis dari judul bahasa Inggris, dan disusun
                                    ulang tiap kali halamannya disimpan.
                                </p>

                                @if($page_id && filled($slug) && $slug !== $alamatBaru)
                                    <p class="mt-2 text-admin-label text-ink-muted">
                                        Alamat yang berlaku sekarang:
                                        <span class="font-semibold text-ink">/page/{{ $slug }}</span>
                                    </p>
                                @endif
                            </section>

                            {{-- ── Kartu: penerbitan ────────────────────── --}}
                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <div class="mb-4">
                                    <h3 class="text-admin-title text-heading">Penerbitan</h3>
                                    <p class="mt-0.5 text-admin-label text-ink-muted">Status tayang halaman di situs publik.</p>
                                </div>

                                <div>
                                    <label class="block text-admin-label text-ink-faint">Status</label>

                                    {{-- :nullable="false" — halaman selalu berada di salah
                                         satu dari dua keadaan ini. --}}
                                    <x-admin.select model="status" :value="$status" class="mt-2"
                                                    label="Status halaman" :nullable="false"
                                                    :options="[
                                                        ['nilai' => 'published', 'label' => 'Terbit'],
                                                        ['nilai' => 'draft',     'label' => 'Draf'],
                                                    ]" />

                                    <p class="admin-hint">
                                        Hanya halaman terbit yang bisa dibuka di situs publik.
                                    </p>

                                    @error('status')
                                        <span class="mt-1.5 block text-admin-label text-danger">{{ $message }}</span>
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
