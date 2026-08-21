@php
    $pengaturan     = \App\Models\Setting::pluck('value', 'key')->toArray();
    $namaPerusahaan = $pengaturan['company_name'] ?? config('app.name');
    $logo           = $pengaturan['logo'] ?? '';
    $favicon        = $pengaturan['favicon'] ?? '';

    $pengguna = auth()->user();

    /*
     * Susunan menu.
     *
     * Ditulis sebagai data, bukan sebagai belasan <a> berturut-turut: dengan
     * begini penyaringan izin, penandaan menu aktif, judul halaman di topbar,
     * dan versi sempitnya semua membaca daftar yang sama. Menambah satu menu
     * berarti menambah satu baris, bukan menyunting empat tempat.
     *
     * 'izin' null berarti cukup sudah masuk — dashboard tidak digerbangi izin
     * apa pun di routes/web.php.
     */
    $kelompokMenu = [
        'Utama' => [
            ['label' => 'Dashboard', 'ikon' => 'dashboard', 'rute' => 'admin.dashboard',         'izin' => null],
            ['label' => 'Inquiry',   'ikon' => 'inquiry',   'rute' => 'admin.inquiries.index',   'izin' => 'view inquiries'],
        ],
        'Katalog' => [
            ['label' => 'Produk',       'ikon' => 'product',       'rute' => 'admin.products.index',       'izin' => 'manage products'],
            ['label' => 'Kategori',     'ikon' => 'category',      'rute' => 'admin.categories.index',     'izin' => 'manage products'],
            ['label' => 'Sertifikasi',  'ikon' => 'certification', 'rute' => 'admin.certifications.index', 'izin' => 'manage certifications'],
            ['label' => 'Pasar Ekspor', 'ikon' => 'market',        'rute' => 'admin.export-markets.index', 'izin' => 'manage export markets'],
        ],
        'Konten' => [
            ['label' => 'Berita',   'ikon' => 'news',     'rute' => 'admin.news.index',      'izin' => 'manage news'],
            ['label' => 'Kategori & Tag', 'ikon' => 'category', 'rute' => 'admin.news-taxonomy.index', 'izin' => 'manage news'],
            ['label' => 'Galeri',   'ikon' => 'gallery',  'rute' => 'admin.galleries.index', 'izin' => 'manage galleries'],
            ['label' => 'Halaman',  'ikon' => 'page',     'rute' => 'admin.pages.index',     'izin' => 'manage pages'],
            ['label' => 'Unduhan',  'ikon' => 'download', 'rute' => 'admin.downloads.index', 'izin' => 'manage downloads'],
        ],
        'Sistem' => [
            ['label' => 'Pengguna & Peran', 'ikon' => 'users',    'rute' => 'admin.users.index',    'izin' => 'manage users'],
            ['label' => 'Pengaturan',       'ikon' => 'settings', 'rute' => 'admin.settings.index', 'izin' => 'manage global settings'],
        ],
    ];

    // Menu yang izinnya tidak dimiliki dibuang sama sekali, bukan dinonaktifkan:
    // menu yang selalu berujung 403 hanya menjanjikan sesuatu yang tidak ada.
    $kelompokMenu = collect($kelompokMenu)
        ->map(fn ($menu) => array_values(array_filter(
            $menu,
            fn ($m) => $m['izin'] === null || $pengguna?->can($m['izin'])
        )))
        ->filter(fn ($menu) => count($menu) > 0)
        ->all();

    /*
     * Kelompok "Sistem" dikeluarkan dari daftar yang menggulung dan dipasang
     * tetap di kaki sidebar.
     *
     * Alasannya: Pengguna dan Pengaturan bukan tempat kerja sehari-hari — ia
     * dibuka sesekali, dan justru karena itu harus selalu bisa ditemukan di
     * tempat yang sama tanpa perlu menggulung dulu. Daftar menu di atas boleh
     * memanjang seiring bertambahnya menu; kaki ini tidak ikut bergeser.
     *
     * Tetap disatukan lagi di $seluruhMenu supaya judul halaman dan remah
     * jejak di topbar tidak kehilangan jejak halaman-halaman itu.
     */
    $menuSistem   = $kelompokMenu['Sistem'] ?? [];
    $kelompokNav  = collect($kelompokMenu)->except('Sistem')->all();
    $seluruhMenu  = $kelompokMenu;

    $ruteAktif = request()->route()?->getName();

    // Judul halaman untuk topbar, dibaca dari daftar yang sama.
    $kelompokAktif = null;
    $judulHalaman  = $title ?? 'Panel Admin';

    foreach ($seluruhMenu as $namaKelompok => $menu) {
        foreach ($menu as $m) {
            if ($m['rute'] === $ruteAktif) {
                $kelompokAktif = $namaKelompok;
                $judulHalaman  = $m['label'];
            }
        }
    }

    // Inquiry yang belum dilihat — satu-satunya angka di panel ini yang benar
    // benar menuntut tindakan, jadi ia yang jadi lencana dan isi lonceng.
    $inquiryBaru = $pengguna?->can('view inquiries')
        ? \App\Models\Inquiry::where('status', 'new')->latest()->take(5)->get()
        : collect();

    $jumlahInquiryBaru = $pengguna?->can('view inquiries')
        ? \App\Models\Inquiry::where('status', 'new')->count()
        : 0;

    $inisial = collect(preg_split('/\s+/', trim((string) $pengguna?->name)))
        ->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->implode('') ?: '?';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $judulHalaman }} · {{ $namaPerusahaan }}</title>

    @if($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif

    {{-- Keadaan sidebar dipasang SEBELUM halaman digambar.
         Kalau dikerjakan Alpine setelah muat, sidebar lebar sempat berkedip
         sekejap di tiap perpindahan halaman sebelum menyempit kembali. --}}
    <script>
        if (localStorage.getItem('sidebar-rail') === '1') {
            document.documentElement.classList.add('sidebar-rail');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Dua huruf, masing-masing satu tugas: Plus Jakarta Sans untuk seluruh
         antarmuka, Nunito hanya untuk nama perusahaan — lambang merek, disusun
         sama dengan header situs publik. --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Nunito:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-mist font-ui text-ink"
      x-data="{
          laciTerbuka: false,
          sempit: document.documentElement.classList.contains('sidebar-rail'),

          lipat() {
              this.sempit = ! this.sempit;
              document.documentElement.classList.toggle('sidebar-rail', this.sempit);
              localStorage.setItem('sidebar-rail', this.sempit ? '1' : '0');
          },
      }">

    {{-- Tautan lewati: pertama yang dijangkau Tab, tersembunyi sampai difokus.
         Tanpa ini, pengguna papan ketik harus melewati dua belas menu setiap
         kali berpindah halaman sebelum sampai ke isinya. --}}
    <a href="#isi-utama"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60]
              focus:rounded-control focus:bg-ink focus:px-4 focus:py-2 focus:text-[13px]
              focus:font-semibold focus:text-white">
        Lewati ke isi halaman
    </a>

    {{-- Latar gelap laci di layar sempit --}}
    <div x-show="laciTerbuka" x-cloak x-on:click="laciTerbuka = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SIDEBAR
         ══════════════════════════════════════════════════════════════════════ --}}
    <aside x-bind:class="laciTerbuka ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col border-r border-line bg-canvas
                  transition-transform duration-200
                  lg:translate-x-0 lg:transition-[width] lg:rail:w-[76px]"
           aria-label="Navigasi panel admin">

        {{-- ── Kepala: lambang + nama ─────────────────────────────────────── --}}
        <div class="relative flex h-[72px] shrink-0 items-center gap-2.5 border-b border-line px-4
                    lg:rail:justify-center lg:rail:px-0">

            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-corner bg-ink text-white">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                         class="h-6 w-6 object-contain">
                @else
                    <svg class="h-[22px] w-[22px]" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <path d="M16 3.5c6 0 10.5 5.6 10.5 12.5S22 28.5 16 28.5 5.5 22.9 5.5 16 10 3.5 16 3.5Z"
                              stroke="currentColor" stroke-width="2"/>
                        <path d="M16 5.2c-3 3-3 6.9 0 10.8s3 7.8 0 10.8"
                              stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                @endif
            </span>

            <span class="min-w-0 lg:rail:hidden">
                <span class="block truncate font-display text-[15px] font-extrabold tracking-[-0.02em] text-ink">
                    {{ $namaPerusahaan }}
                </span>
                <span class="block text-[11px] text-ink-faint">Panel Admin</span>
            </span>

            {{-- Tombol lipat, menumpang di tepi sidebar seperti pada referensi.
                 Disembunyikan di layar sempit: di sana sidebar berupa laci yang
                 ditutup dengan menyentuh latarnya, bukan dipersempit. --}}
            <button type="button" x-on:click="lipat()"
                    x-bind:aria-label="sempit ? 'Lebarkan sidebar' : 'Persempit sidebar'"
                    aria-label="Persempit sidebar"
                    class="absolute -right-3.5 top-1/2 hidden h-7 w-7 -translate-y-1/2 items-center justify-center
                           rounded-full border border-line bg-canvas text-ink-faint shadow-[0_2px_8px_-2px_rgba(26,29,27,0.18)]
                           transition-colors hover:border-line-strong hover:text-ink lg:flex">
                <x-icon.admin name="panel" size="h-[15px] w-[15px]" />
            </button>
        </div>

        {{-- ── Daftar menu ────────────────────────────────────────────────── --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 pb-4">
            @foreach($kelompokNav as $namaKelompok => $menu)
                {{-- Saat menyempit, judul kelompok hilang dan digantikan garis
                     tipis — pengelompokannya tetap terbaca sebagai jeda, tanpa
                     huruf yang menggantung tanpa isi. --}}
                <p class="admin-group">{{ $namaKelompok }}</p>
                <div class="mx-2 hidden border-t border-line lg:rail:my-3 lg:rail:block"></div>

                <ul class="space-y-1">
                    @foreach($menu as $m)
                        @php $aktif = $ruteAktif === $m['rute']; @endphp

                        <li class="relative group">
                            <a href="{{ route($m['rute']) }}"
                               @if($aktif) aria-current="page" @endif
                               @class([
                                   'admin-link lg:rail:justify-center lg:rail:px-0',
                                   'admin-link-on' => $aktif,
                               ])>
                                <x-icon.admin :name="$m['ikon']" class="shrink-0" />

                                <span class="min-w-0 flex-1 truncate lg:rail:hidden">{{ $m['label'] }}</span>

                                @if($m['rute'] === 'admin.inquiries.index' && $jumlahInquiryBaru > 0)
                                    <span class="ml-auto inline-flex min-w-[22px] shrink-0 items-center justify-center
                                                 rounded-full bg-brand px-1.5 py-0.5 text-[11px] font-bold
                                                 tabular-nums text-white lg:rail:hidden">
                                        {{ $jumlahInquiryBaru > 99 ? '99+' : $jumlahInquiryBaru }}
                                    </span>

                                    {{-- Versi sempit: titik saja. Angka di ikon
                                         selebar 18px hanya jadi noda. --}}
                                    <span class="absolute right-3 top-2 hidden h-2 w-2 rounded-full bg-brand
                                                 ring-2 ring-canvas lg:rail:block"
                                          role="img" aria-label="{{ $jumlahInquiryBaru }} inquiry baru"></span>
                                @endif
                            </a>

                            {{-- Keterangan saat sempit --}}
                            <span class="admin-tip lg:rail:group-hover:block">{{ $m['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>

        {{-- ── Kaki tetap ──────────────────────────────────────────────────
             Tidak ikut menggulung. Isinya yang jarang dibuka tapi harus selalu
             ada di tempat yang sama: pengaturan sistem, jalan keluar ke situs
             publik, dan tombol keluar. --}}
        <div class="shrink-0 space-y-1 border-t border-line p-3">

            @foreach($menuSistem as $m)
                @php $aktif = $ruteAktif === $m['rute']; @endphp

                <div class="relative group">
                    <a href="{{ route($m['rute']) }}"
                       @if($aktif) aria-current="page" @endif
                       @class([
                           'admin-link lg:rail:justify-center lg:rail:px-0',
                           'admin-link-on' => $aktif,
                       ])>
                        <x-icon.admin :name="$m['ikon']" class="shrink-0" />
                        <span class="min-w-0 flex-1 truncate lg:rail:hidden">{{ $m['label'] }}</span>
                    </a>
                    <span class="admin-tip lg:rail:group-hover:block">{{ $m['label'] }}</span>
                </div>
            @endforeach

            <div class="relative group">
                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="admin-link lg:rail:justify-center lg:rail:px-0">
                    <x-icon.admin name="external" class="shrink-0" />
                    <span class="truncate lg:rail:hidden">Lihat situs</span>
                </a>
                <span class="admin-tip lg:rail:group-hover:block">Lihat situs</span>
            </div>

            {{-- Keluar tetap sebuah formulir POST, bukan tautan: permintaan GET
                 bisa dipicu dari luar (gambar, prefetch peramban) dan
                 mengeluarkan orang tanpa ia melakukan apa pun.

                 Warnanya baru memerah saat disentuh — merah sejak awal membuat
                 ia berteriak lebih keras daripada seluruh menu di atasnya. --}}
            <form method="POST" action="{{ route('logout') }}" class="relative group">
                @csrf
                <button type="submit"
                        class="admin-link w-full hover:bg-danger/5 hover:text-danger
                               lg:rail:justify-center lg:rail:px-0">
                    <x-icon.admin name="logout" class="shrink-0" />
                    <span class="truncate lg:rail:hidden">Keluar</span>
                </button>
                <span class="admin-tip lg:rail:group-hover:block">Keluar</span>
            </form>
        </div>
    </aside>

    {{-- ══════════════════════════════════════════════════════════════════════
         KOLOM ISI
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex min-h-screen flex-col transition-[padding] duration-200 lg:pl-[264px] lg:rail:pl-[76px]">

        {{-- ── TOPBAR ─────────────────────────────────────────────────────── --}}
        <header class="sticky top-0 z-20 flex h-[72px] shrink-0 items-center gap-3 border-b border-line
                       bg-canvas/85 px-4 backdrop-blur-md sm:px-6">

            <button type="button" x-on:click="laciTerbuka = true" aria-label="Buka menu"
                    class="-ml-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                           text-ink-muted transition-colors hover:bg-mist hover:text-ink lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Remah jejak: kelompok › halaman. Kelompoknya bukan tautan
                 karena ia label, bukan halaman — hanya penunjuk letak. --}}
            <div class="min-w-0 flex-1">
                <p class="flex items-center gap-1.5 text-[13px] text-ink-faint">
                    @if($kelompokAktif)
                        <span class="hidden sm:inline">{{ $kelompokAktif }}</span>
                        <span class="hidden sm:inline" aria-hidden="true">›</span>
                    @endif
                    <span class="truncate font-semibold text-ink">{{ $judulHalaman }}</span>
                </p>
            </div>

            {{-- ── Lonceng ────────────────────────────────────────────────── --}}
            @can('view inquiries')
                <div x-data="{ buka: false }" x-on:keydown.escape.window="buka = false" class="relative shrink-0">
                    <button type="button" x-on:click="buka = ! buka"
                            x-bind:aria-expanded="buka ? 'true' : 'false'"
                            aria-label="Inquiry baru"
                            class="relative inline-flex h-9 w-9 items-center justify-center rounded-full
                                   text-ink-muted transition-colors hover:bg-mist hover:text-ink">
                        <x-icon.admin name="bell" />

                        @if($jumlahInquiryBaru > 0)
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-brand ring-2 ring-canvas"></span>
                        @endif
                    </button>

                    <div x-show="buka" x-cloak x-on:click.outside="buka = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 top-full z-50 mt-2 w-[300px] overflow-hidden rounded-corner
                                border border-line bg-canvas shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

                        <div class="border-b border-line px-4 py-3">
                            <p class="text-[13px] font-semibold text-ink">Inquiry baru</p>
                            <p class="mt-0.5 text-[12px] text-ink-faint">
                                {{ $jumlahInquiryBaru }} permintaan menunggu ditangani
                            </p>
                        </div>

                        @forelse($inquiryBaru as $inq)
                            <a href="{{ route('admin.inquiries.index') }}" class="admin-menu-row block">
                                <span class="block min-w-0">
                                    <span class="block truncate font-semibold text-ink">{{ $inq->name }}</span>
                                    <span class="mt-0.5 block truncate text-[12px] text-ink-faint">
                                        {{ $inq->company ?: $inq->email }}
                                        · {{ $inq->created_at->locale('id')->diffForHumans([
                                                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'short' => true,
                                           ]) }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <p class="px-4 py-6 text-center text-[13px] text-ink-faint">
                                Tidak ada inquiry baru.
                            </p>
                        @endforelse

                        @if($jumlahInquiryBaru > $inquiryBaru->count())
                            <a href="{{ route('admin.inquiries.index') }}"
                               class="block border-t border-line px-4 py-3 text-center text-[13px]
                                      font-semibold text-brand transition-colors hover:bg-mist">
                                Lihat semuanya
                            </a>
                        @endif
                    </div>
                </div>
            @endcan

            {{-- ── Profil ─────────────────────────────────────────────────── --}}
            <div x-data="{ buka: false }" x-on:keydown.escape.window="buka = false" class="relative shrink-0">
                <button type="button" x-on:click="buka = ! buka"
                        x-bind:aria-expanded="buka ? 'true' : 'false'"
                        aria-haspopup="menu" aria-label="Menu akun"
                        class="flex items-center gap-2 rounded-full p-0.5 transition-colors hover:bg-mist">

                    {{-- aria-hidden: namanya sudah disebut aria-label tombolnya,
                         jadi pembaca layar tidak perlu mengeja inisialnya lagi. --}}
                    <span aria-hidden="true"
                          class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                 bg-brand text-[13px] font-bold text-white">{{ $inisial }}</span>

                    <svg class="mr-1 h-3 w-3 shrink-0 text-ink-faint transition-transform duration-150"
                         x-bind:class="buka && 'rotate-180'" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div x-show="buka" x-cloak x-on:click.outside="buka = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     role="menu"
                     class="absolute right-0 top-full z-50 mt-2 w-[248px] overflow-hidden rounded-corner
                            border border-line bg-canvas py-1.5 shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

                    <div class="border-b border-line px-4 pb-3 pt-2">
                        <p class="truncate text-[13px] font-semibold text-ink">{{ $pengguna?->name }}</p>
                        <p class="mt-0.5 truncate text-[12px] text-ink-faint">{{ $pengguna?->email }}</p>

                        @if($pengguna?->roles->isNotEmpty())
                            <span class="mt-2 inline-flex items-center rounded-full bg-mist px-2 py-0.5
                                         text-[11px] font-semibold text-ink-muted">
                                {{ $pengguna->roles->pluck('name')->implode(', ') }}
                            </span>
                        @endif
                    </div>

                    <div class="py-1">
                        @can('manage global settings')
                            <a href="{{ route('admin.settings.index') }}" class="admin-menu-row" role="menuitem">
                                <x-icon.admin name="settings" size="h-4 w-4" class="shrink-0" />
                                Pengaturan
                            </a>
                        @endcan

                        <a href="{{ route('home') }}" target="_blank" rel="noopener"
                           class="admin-menu-row" role="menuitem">
                            <x-icon.admin name="external" size="h-4 w-4" class="shrink-0" />
                            Lihat situs
                        </a>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-1">
                        @csrf
                        <button type="submit" class="admin-menu-row hover:text-danger" role="menuitem">
                            <x-icon.admin name="logout" size="h-4 w-4" class="shrink-0" />
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main id="isi-utama" class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot ?? '' }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
