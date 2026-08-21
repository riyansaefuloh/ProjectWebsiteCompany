<div class="mx-auto max-w-[1400px]">

    @php
        /*
         * Sebutan peran dalam bahasa Indonesia. Nama teknisnya ('super-admin')
         * yang tersimpan di basis data dan dipakai spatie/laravel-permission;
         * yang dibaca pemakai tidak perlu ikut bertanda hubung.
         */
        $sebutanPeran = [
            'super-admin' => 'Super Admin',
            'admin-cms'   => 'Admin CMS',
            'sales'       => 'Sales',
        ];

        /*
         * Daftar penyaring yang sedang menyala — pola yang sama dengan halaman
         * admin lainnya.
         */
        $penyaringAktif = collect();

        if (filled($search)) {
            $penyaringAktif->push(['label' => 'Cari', 'nilai' => $search, 'props' => ['search']]);
        }

        if (filled($filterRole)) {
            $penyaringAktif->push([
                'label' => 'Peran',
                'nilai' => $sebutanPeran[$filterRole] ?? $filterRole,
                'props' => ['filterRole'],
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
                Pengguna &amp; Peran
            </h1>
            <p class="mt-1.5 text-[13px] text-ink-muted">
                Akun yang bisa masuk ke panel admin, beserta peran yang menentukan aksesnya.
            </p>
        </div>

        <button type="button" wire:click="create" class="admin-btn admin-btn-brand shrink-0">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            Tambah pengguna
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

    {{-- Pesan penolakan. Halaman ini satu-satunya yang punya kegagalan yang
         disengaja — menghapus akun sendiri — dan itu perlu rona yang berbeda
         dari pesan berhasil, bukan spanduk hijau yang isinya kabar buruk. --}}
    @if(session()->has('error'))
        <div x-data="{ tampil: true }" x-show="tampil" x-collapse
             class="mb-6 flex items-start gap-3 rounded-corner border border-status-rejected/30
                    bg-status-rejected/5 px-5 py-4"
             role="alert">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                         bg-status-rejected text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 4.4v4.4M8 11.4v.2" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round"/>
                </svg>
            </span>

            <p class="min-w-0 flex-1 pt-1 text-[13px] font-semibold text-status-rejected">
                {{ session('error') }}
            </p>

            <button type="button" x-on:click="tampil = false" aria-label="Tutup pesan"
                    class="-mr-1 shrink-0 rounded-control p-1 text-status-rejected/70 transition-colors
                           hover:bg-status-rejected/10 hover:text-status-rejected">
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
             pertiga, peran sepertiga. Susunan yang sama dengan halaman
             Kategori, Sertifikasi, Berita, dan Unduhan. --}}
        <div class="grid gap-4 p-5 lg:grid-cols-3">

            <div class="relative lg:col-span-2">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <x-icon.admin name="search" size="h-[18px] w-[18px]" />
                </span>

                <input type="search" wire:model.live="search" id="cari-pengguna"
                       aria-label="Cari pengguna"
                       placeholder="Cari nama atau email…"
                       class="admin-control pl-11 pr-10">

                <span wire:loading wire:target="search"
                      class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-faint">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.25"/>
                        <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>

            {{-- Jumlah pemakainya ikut disebut di tiap pilihan: memilih peran
                 yang nol pemakainya jadi jelas berujung tabel kosong sebelum
                 diklik. --}}
            <x-admin.select model="filterRole" :value="$filterRole"
                            label="Saring menurut peran" placeholder="Semua peran"
                            :options="$roles->map(fn ($r) => [
                                'nilai' => $r->name,
                                'label' => ($sebutanPeran[$r->name] ?? $r->name) . ' (' . $r->users_count . ')',
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
                            x-on:click="{{ $bersihkan(['search', 'filterRole']) }}"
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
                    <x-icon.admin name="users" size="h-4 w-4" />
                </span>

                <div>
                    <h2 class="font-ui text-[15px] font-semibold text-ink">Daftar pengguna</h2>
                    <p class="mt-0.5 text-[12px] text-ink-muted">
                        Urut dari yang paling baru ditambahkan.
                    </p>
                </div>
            </div>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-mist
                         px-3 py-1.5 text-[12px] font-semibold text-ink-muted">
                <span class="tabular-nums text-ink">{{ number_format($users->total()) }}</span>
                {{ $penyaringAktif->isNotEmpty() ? 'hasil' : 'pengguna' }}
            </span>
        </div>

        <div class="p-5 transition-opacity duration-150"
             wire:loading.class="opacity-45"
             wire:target="search, filterRole, gotoPage, previousPage, nextPage">

            <div class="overflow-hidden rounded-corner border border-line">
                <div class="overflow-x-auto">

                    @php
                        $kolom = [
                            ['label' => 'Pengguna',  'lebar' => 'w-[42%]', 'rata' => 'text-left'],
                            ['label' => 'Peran',     'lebar' => 'w-[20%]', 'rata' => 'text-left'],
                            ['label' => 'Bergabung', 'lebar' => 'w-[20%]', 'rata' => 'text-left'],
                            ['label' => 'Aksi',      'lebar' => 'w-[18%]', 'rata' => 'text-right'],
                        ];
                    @endphp

                    <table class="w-full min-w-[820px] table-fixed">
                        @if($users->isNotEmpty())
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
                            @forelse($users as $usr)
                                @php
                                    $peran      = $usr->roles->first();
                                    $namaPeran  = $peran?->name;
                                    $labelPeran = $namaPeran ? ($sebutanPeran[$namaPeran] ?? $namaPeran) : null;

                                    $diriSendiri = $usr->id === auth()->id();
                                @endphp

                                <tr class="group border-b border-line transition-colors last:border-0 hover:bg-mist">

                                    {{-- Pengguna. Garis hijau di tepi kiri menandai akun
                                         yang sedang dipakai sekarang — barisnya satu-satunya
                                         yang tidak bisa dihapus, dan itu perlu terlihat
                                         sebelum tombolnya dicari. --}}
                                    <td @class([
                                        'py-4 pl-5 pr-3 align-middle border-l-[3px]',
                                        'border-brand'       => $diriSendiri,
                                        'border-transparent' => ! $diriSendiri,
                                    ])>
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$usr->name" size="lg"
                                                            :tone="$diriSendiri ? 'brand' : 'quiet'" />

                                            <div class="min-w-0">
                                                <span class="flex items-center gap-2">
                                                    <span class="min-w-0 truncate text-[13px] font-semibold text-ink"
                                                          title="{{ $usr->name }}">{{ $usr->name }}</span>

                                                    @if($diriSendiri)
                                                        <span class="inline-flex shrink-0 items-center rounded-full
                                                                     bg-brand/10 px-1.5 text-[10px] font-bold text-brand">
                                                            Akun Anda
                                                        </span>
                                                    @endif
                                                </span>

                                                {{-- Email jadi baris kedua: itulah yang
                                                     dipakai masuk, jadi ia keterangan
                                                     akunnya, bukan datum setara. --}}
                                                <a href="mailto:{{ $usr->email }}"
                                                   class="mt-0.5 block truncate text-[12px] text-ink-faint
                                                          underline-offset-4 transition-colors
                                                          hover:text-brand hover:underline"
                                                   title="{{ $usr->email }}">{{ $usr->email }}</a>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($labelPeran)
                                            {{-- Super Admin terisi penuh, peran lain bergaris.
                                                 Bukan gaya-gayaan: ia satu-satunya peran yang
                                                 bisa mengubah pengguna lain, jadi bedanya
                                                 harus terbaca dari ujung mata. --}}
                                            <span @class([
                                                'inline-flex max-w-full items-center rounded-full px-2.5 py-1 text-[11px] font-bold',
                                                'bg-brand/10 text-brand'                        => $namaPeran === 'super-admin',
                                                'border border-line-strong text-ink-muted'      => $namaPeran !== 'super-admin',
                                            ])>
                                                <span class="min-w-0 truncate">{{ $labelPeran }}</span>
                                            </span>
                                        @else
                                            {{-- Tanpa peran, akunnya bisa masuk tapi tidak
                                                 bisa apa-apa. Itu keadaan yang salah, bukan
                                                 sekadar kosong. --}}
                                            <span class="inline-flex items-center rounded-full bg-status-rejected/10
                                                         px-2.5 py-1 text-[11px] font-bold text-status-rejected"
                                                  title="Akun ini bisa masuk tapi tidak punya akses apa pun">
                                                Tanpa peran
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($usr->created_at)
                                            <span class="block text-[13px] tabular-nums text-ink-muted">
                                                {{ $usr->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="mt-0.5 block text-[12px] tabular-nums text-ink-faint">
                                                {{ $usr->created_at->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-[13px] text-ink-faint">&mdash;</span>
                                        @endif
                                    </td>

                                    <td class="py-4 pl-3 pr-5 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="edit('{{ $usr->id }}')"
                                                    title="Ubah {{ $usr->name }}"
                                                    aria-label="Ubah {{ $usr->name }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                           border border-line bg-canvas text-ink-muted transition-colors
                                                           hover:border-brand hover:bg-brand hover:text-white">
                                                <x-icon.admin name="edit" size="h-4 w-4" />
                                            </button>

                                            @if($diriSendiri)
                                                {{-- Tombolnya tetap digambar, tapi mati.
                                                     Menghilangkannya membuat kolom aksinya
                                                     melompat-lompat antarbaris, dan pemakai
                                                     mengira tombolnya belum tergambar. --}}
                                                <span class="inline-flex h-8 w-8 cursor-not-allowed items-center
                                                             justify-center rounded-control border border-dashed
                                                             border-line text-ink-faint/60"
                                                      title="Akun yang sedang dipakai tidak bisa dihapus"
                                                      aria-hidden="true">
                                                    <x-icon.admin name="trash" size="h-4 w-4" />
                                                </span>
                                            @else
                                                {{-- Penegasannya menyebut akibatnya, bukan
                                                     sekadar "yakin?": akunnya langsung
                                                     kehilangan akses ke panel admin. --}}
                                                <button type="button" wire:click="delete('{{ $usr->id }}')"
                                                        wire:confirm="Hapus pengguna &quot;{{ $usr->name }}&quot;? Akunnya langsung kehilangan akses ke panel admin, dan tindakan ini tidak bisa dibatalkan."
                                                        title="Hapus {{ $usr->name }}"
                                                        aria-label="Hapus {{ $usr->name }}"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-control
                                                               border border-line bg-canvas text-ink-muted transition-colors
                                                               hover:border-danger hover:bg-danger hover:text-white">
                                                    <x-icon.admin name="trash" size="h-4 w-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="{{ count($kolom) }}" class="px-6 py-16 text-center">
                                        <span class="mx-auto flex h-12 w-12 items-center justify-center
                                                     rounded-full bg-mist text-ink-faint">
                                            <x-icon.admin :name="$penyaringAktif->isNotEmpty() ? 'search' : 'users'"
                                                          size="h-5 w-5" />
                                        </span>

                                        @if($penyaringAktif->isNotEmpty())
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Tidak ada pengguna yang cocok
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Coba kosongkan kata pencariannya, atau kembalikan
                                                perannya ke "semua".
                                            </p>

                                            <button type="button" x-on:click="{{ $bersihkan(['search', 'filterRole']) }}"
                                                    class="admin-btn admin-btn-quiet mt-5">
                                                <x-icon.admin name="close" size="h-3.5 w-3.5" />
                                                Hapus penyaring
                                            </button>
                                        @else
                                            <p class="mt-4 text-[14px] font-semibold text-ink">
                                                Belum ada pengguna
                                            </p>
                                            <p class="mx-auto mt-1.5 max-w-[380px] text-[13px] leading-relaxed text-ink-muted">
                                                Pengguna yang ditambahkan di sini bisa masuk ke panel
                                                admin sesuai perannya.
                                            </p>

                                            <button type="button" wire:click="create" class="admin-btn admin-btn-brand mt-5">
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                    <path d="M10 4.2v11.6M4.2 10h11.6" stroke="currentColor"
                                                          stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                                Tambah pengguna pertama
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
            {{ $users->links('vendor.pagination.admin', ['satuan' => 'pengguna']) }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL TAMBAH / UBAH PENGGUNA
         ══════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
        @php
            /*
             * Sebutan izin dalam bahasa Indonesia. Nama teknisnya ('manage
             * products') yang tersimpan dan dipakai spatie/laravel-permission;
             * yang dibaca pemakai tidak perlu ikut berbahasa Inggris.
             */
            $sebutanIzin = [
                'manage users'            => 'Kelola pengguna',
                'manage products'         => 'Kelola produk',
                'manage categories'       => 'Kelola kategori',
                'manage news'             => 'Kelola berita',
                'manage galleries'        => 'Kelola galeri',
                'manage certifications'   => 'Kelola sertifikasi',
                'manage export markets'   => 'Kelola pasar ekspor',
                'manage downloads'        => 'Kelola unduhan',
                'manage pages'            => 'Kelola halaman',
                'view inquiries'          => 'Lihat inquiry',
                'manage inquiries'        => 'Kelola inquiry',
                'export inquiries'        => 'Ekspor inquiry',
                'manage global settings'  => 'Kelola pengaturan global',
                'manage partial settings' => 'Kelola sebagian pengaturan',
            ];

            $peranTerpilih = $roles->firstWhere('name', $selectedRole);

            /* Menyunting akun sendiri: menurunkan peran diri sendiri langsung
               memutus akses ke halaman ini juga. Perlu disebut sebelum, bukan
               sesudah. */
            $menyuntingDiri = $editingId && $editingId === auth()->id();
        @endphp

        <div class="modal-open fixed inset-0 z-[100] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-data
             x-on:keydown.escape.window="$wire.$set('showModal', false)"
             role="dialog" aria-modal="true" aria-labelledby="judul-modal-pengguna">

            <div class="absolute inset-0" aria-hidden="true"
                 x-on:click="$wire.$set('showModal', false)"></div>

            {{-- 900px seperti modal Kategori, Pasar Ekspor, dan Unduhan:
                 isiannya cuma empat. --}}
            <div class="relative flex max-h-[90vh] w-full max-w-[900px] flex-col overflow-clip
                        rounded-corner border border-line bg-canvas
                        shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <form wire:submit.prevent="save" class="flex min-h-0 flex-1 flex-col">

                    {{-- ── Kepala ──────────────────────────────────────── --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-6 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                                         bg-brand/10 text-brand">
                                <x-icon.admin name="users" size="h-[18px] w-[18px]" />
                            </span>

                            <div class="min-w-0">
                                <h2 id="judul-modal-pengguna"
                                    class="truncate font-ui text-[15px] font-semibold text-ink">
                                    {{ $editingId ? 'Ubah pengguna' : 'Tambah pengguna' }}
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

                    {{-- Peringatan menyunting akun sendiri. --}}
                    @if($menyuntingDiri)
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
                                Ini akun yang sedang Anda pakai. Menurunkan perannya bisa langsung
                                memutus akses Anda sendiri ke halaman ini.
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
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Informasi akun
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="nama-pengguna" class="block text-[12px] font-semibold text-ink-faint">
                                            Nama lengkap <span class="text-brand">*</span>
                                        </label>

                                        <input type="text" wire:model="name" id="nama-pengguna"
                                               autocomplete="off"
                                               placeholder="mis. Rina Wijaya"
                                               class="admin-control mt-2">

                                        @error('name')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email-pengguna" class="block text-[12px] font-semibold text-ink-faint">
                                            Email <span class="text-brand">*</span>
                                        </label>

                                        <input type="email" wire:model="email" id="email-pengguna"
                                               autocomplete="off"
                                               placeholder="nama@perusahaan.com"
                                               class="admin-control mt-2">

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            Email ini yang dipakai untuk masuk ke panel admin.
                                        </p>

                                        @error('email')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="sandi-pengguna" class="block text-[12px] font-semibold text-ink-faint">
                                            Kata sandi
                                            @unless($editingId)
                                                <span class="text-brand">*</span>
                                            @endunless
                                        </label>

                                        {{-- Sakelar lihat/sembunyi. Kata sandi yang tidak
                                             bisa dibaca sama sekali membuat salah ketik cuma
                                             ketahuan saat pemakainya gagal masuk — dan yang
                                             mengetik di sini bukan pemilik akunnya. --}}
                                        <div x-data="{ tampak: false }" class="relative mt-2">
                                            <input x-bind:type="tampak ? 'text' : 'password'" type="password"
                                                   wire:model="password" id="sandi-pengguna"
                                                   autocomplete="new-password"
                                                   placeholder="{{ $editingId ? 'Kosongkan kalau tidak diubah' : 'Minimal 6 karakter' }}"
                                                   class="admin-control pr-11">

                                            <button type="button" x-on:click="tampak = ! tampak"
                                                    x-bind:aria-label="tampak ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                                                    class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2
                                                           items-center justify-center rounded-control text-ink-faint
                                                           transition-colors hover:bg-mist hover:text-ink">
                                                <svg x-show="! tampak" class="h-4 w-4" viewBox="0 0 20 20"
                                                     fill="none" aria-hidden="true">
                                                    <path d="M2 10s3-5.2 8-5.2S18 10 18 10s-3 5.2-8 5.2S2 10 2 10Z"
                                                          stroke="currentColor" stroke-width="1.5"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="10" cy="10" r="2.2" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>

                                                <svg x-show="tampak" x-cloak class="h-4 w-4" viewBox="0 0 20 20"
                                                     fill="none" aria-hidden="true">
                                                    <path d="M4.2 4.2 15.8 15.8M8.3 8.4a2.2 2.2 0 0 0 3.1 3.1"
                                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                    <path d="M6.2 6.3C3.9 7.7 2 10 2 10s3 5.2 8 5.2c1.3 0 2.5-.36 3.5-.9"
                                                          stroke="currentColor" stroke-width="1.5"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M16.3 12.4C17.4 11.3 18 10 18 10s-3-5.2-8-5.2c-.5 0-1 .05-1.4.15"
                                                          stroke="currentColor" stroke-width="1.5"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <p class="mt-2 text-[12px] leading-relaxed text-ink-faint">
                                            @if($editingId)
                                                Dikosongkan berarti kata sandinya tidak diubah. Diisi
                                                berarti sandi lamanya langsung diganti.
                                            @else
                                                Minimal 6 karakter. Sampaikan sandi ini ke pemiliknya
                                                lewat jalur yang aman.
                                            @endif
                                        </p>

                                        @error('password')
                                            <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- ══ KANAN ══ --}}
                        <div class="admin-scroll min-h-0 space-y-4 border-t border-line p-6
                                    lg:w-[42%] lg:border-t-0 lg:overflow-y-auto lg:overscroll-contain">

                            <section class="rounded-corner border border-line bg-canvas p-5">
                                <h3 class="mb-4 font-ui text-[14px] font-bold uppercase tracking-[0.1em] text-ink">
                                    Peran
                                </h3>

                                <div>
                                    <label class="block text-[12px] font-semibold text-ink-faint">
                                        Peran akun <span class="text-brand">*</span>
                                    </label>

                                    {{-- :nullable="false" — akun tanpa peran bisa masuk tapi
                                         tidak bisa apa-apa, jadi kekosongan di sini bukan
                                         jawaban yang sah. --}}
                                    <x-admin.select model="selectedRole" :value="$selectedRole" class="mt-2"
                                                    label="Peran akun" :nullable="false"
                                                    :options="$roles->map(fn ($r) => [
                                                        'nilai' => $r->name,
                                                        'label' => $sebutanPeran[$r->name] ?? $r->name,
                                                    ])->all()" />

                                    @error('selectedRole')
                                        <span class="mt-1.5 block text-[12px] text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Daftar izin peran yang sedang dipilih.

                                     Memilih peran tanpa tahu akibatnya adalah cara paling
                                     gampang memberi akses yang tidak dimaksudkan — dan
                                     satu-satunya tempat perbedaan ketiga peran itu bisa
                                     dilihat adalah di sini. --}}
                                <div class="mt-4">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <span class="text-[12px] font-semibold text-ink-faint">Aksesnya</span>

                                        @if($peranTerpilih)
                                            <span class="text-[12px] text-ink-faint">
                                                {{ $peranTerpilih->permissions->count() }} izin
                                            </span>
                                        @endif
                                    </div>

                                    @if($peranTerpilih && $peranTerpilih->permissions->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach($peranTerpilih->permissions as $izin)
                                                <span class="inline-flex max-w-full items-center rounded-full border
                                                             border-line bg-mist px-2.5 py-1 text-[11px] font-semibold
                                                             text-ink-muted"
                                                      title="{{ $izin->name }}">
                                                    <span class="min-w-0 truncate">
                                                        {{ $sebutanIzin[$izin->name] ?? $izin->name }}
                                                    </span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mt-2 rounded-control border border-dashed border-line-strong
                                                  bg-mist/40 px-3.5 py-2.5 text-[12px] leading-relaxed text-ink-muted">
                                            Peran ini belum punya izin apa pun. Akunnya bisa masuk,
                                            tapi tidak bisa membuka apa-apa.
                                        </p>
                                    @endif
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
                            {{ $editingId ? 'Simpan perubahan' : 'Simpan pengguna' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
