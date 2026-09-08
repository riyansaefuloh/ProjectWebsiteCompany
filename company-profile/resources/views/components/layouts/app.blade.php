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
     * SELURUH kelompok menggulung bersama, "Sistem" termasuk.
     *
     * Sebelumnya Pengguna & Peran dan Pengaturan dikeluarkan dari daftar dan
     * dipasang tetap di kaki sidebar, dengan alasan keduanya jarang dibuka
     * sehingga harus selalu ada di tempat yang sama. Alasan itu ditukar dengan
     * yang lebih kuat: di kaki, keduanya berdiri tanpa judul kelompok,
     * bertetangga dengan "Lihat situs" dan "Keluar" — dua hal yang bukan menu
     * sama sekali. Yang terbaca di sana bukan "ini kelompok Sistem" melainkan
     * "ini sisa-sisa yang tidak kebagian tempat".
     *
     * Sekarang keduanya kembali ke daftar bersama judul "Sistem" di atasnya,
     * dan kaki sidebar menyisakan persis yang memang bukan menu: jalan keluar
     * ke situs publik, dan tombol keluar.
     */
    $kelompokNav = $kelompokMenu;
    $seluruhMenu = $kelompokMenu;

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

@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $judulHalaman }} · {{ $namaPerusahaan }}</title>

    {{-- Selalu dinyatakan, termasuk saat kosong — kalau tidak, peramban jatuh
         ke /favicon.ico dan mempertahankan ikon yang terakhir dikenalnya, jadi
         favicon yang sudah dihapus tetap tampak di tab. Keterangan lengkapnya
         di App\Support\Monogram. --}}
    @if($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @else
        <link rel="icon" type="image/svg+xml"
              href="{{ \App\Support\Monogram::favicon($namaPerusahaan) }}">
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
    {{-- Dua huruf, dua tugas: Plex Sans untuk seluruh antarmuka, Plex Mono
         hanya untuk deret yang dibaca karakter per karakter. Bobot 700
         sengaja tidak diminta — bobot yang dipakai tapi tidak dimuat akan
         ditebalkan sendiri oleh peramban. --}}
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="admin-shell min-h-screen bg-mist font-ui text-admin-body text-ink"
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
              focus:rounded-control focus:bg-ink focus:px-4 focus:py-2 focus:text-admin-body
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
    {{-- Keadaan keterangan rail dipegang di <aside>, bukan di tiap menu: satu
         elemen bersama yang berdiri DI LUAR <nav>, karena nav memotong apa
         pun yang melewati tepinya. --}}
    <aside x-bind:class="laciTerbuka ? 'translate-x-0' : '-translate-x-full'"
           x-data="{
               tip: '',
               tipY: 0,

               tampilTip(el, teks) {
                   /* Dibaca dari DOM, bukan dari keadaan Alpine: kelas
                      sidebar-rail sudah dipasang skrip di <head> sebelum Alpine
                      hidup, jadi ia sumber yang paling pasti. Ambang 1024px
                      menyamai ambang lg — di bawah itu sidebar berupa laci lebar
                      yang labelnya sudah terbaca, jadi keterangan tidak perlu. */
                   if (window.innerWidth < 1024) return;
                   if (! document.documentElement.classList.contains('sidebar-rail')) return;

                   const kotak = el.getBoundingClientRect();

                   this.tip  = teks;
                   /* Titik tengah tegak petaknya, diukur terhadap viewport —
                      dan itu sama dengan terhadap bilah sisi, karena bilah sisi
                      dipasang fixed inset-y-0 sehingga tepi atasnya berimpit
                      dengan tepi atas layar. */
                   this.tipY = kotak.top + kotak.height / 2;
               },
           }"
           class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col border-r border-line bg-canvas
                  transition-transform duration-200
                  lg:translate-x-0 lg:transition-[width] lg:rail:w-[76px]"
           aria-label="Navigasi panel admin">

        {{-- ── Kepala: lambang + nama ─────────────────────────────────────── --}}
        <div class="relative flex h-[64px] shrink-0 items-center gap-2.5 border-b border-line px-4
                    lg:rail:justify-center lg:rail:px-0">

            {{-- Bingkai lambang 40px, seukuran petak menu di bawahnya dan
                 bingkai di halaman masuk — satu kolom, satu lebar. --}}
            <span class="flex h-10 w-10 shrink-0 items-center justify-center
                         rounded-control border border-line bg-mist">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                         class="h-6 w-6 object-contain">
                @else
                    {{-- Monogram, bukan lambang bergambar — sama seperti di kepala
                         situs publik. Bingkainya di sini TETAP dipertahankan:
                         saat sidebar dipersempit, kotak inilah satu-satunya yang
                         tersisa di kepalanya. --}}
                    <span class="text-admin-strong font-semibold leading-none text-brand-deep"
                          aria-hidden="true">
                        {{ \App\Support\Monogram::inisial($namaPerusahaan) ?: '·' }}
                    </span>
                @endif
            </span>

            {{-- Nama perusahaan memakai huruf antarmuka panel, bukan huruf
                 situs publik. --}}
            <span class="min-w-0 lg:rail:hidden">
                <span class="block truncate text-admin-title text-ink">
                    {{ $namaPerusahaan }}
                </span>
                <span class="mt-0.5 block text-admin-overline uppercase text-ink-faint">Panel Admin</span>
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
        {{-- Nav ini MEMOTONG apa pun yang melewati tepinya, dan itu tak
             terhindarkan: begitu satu sumbu diberi overflow-y-auto ia jadi
             scroll container, dan sumbu satunya tidak bisa lagi visible.
             Karena itu keterangan menu berdiri di luar <nav>. --}}
        <nav id="nav-admin"
             class="admin-scroll flex-1 overflow-y-auto overflow-x-hidden px-3 pb-4">
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
                            {{-- wire:navigate.hover — berpindah menu tanpa
                                 mengunduh dan mengurai ulang CSS, huruf, dan
                                 skrip. --}}
                            <a href="{{ route($m['rute']) }}" wire:navigate.hover
                               @if($aktif) aria-current="page" @endif
                               x-on:mouseenter="tampilTip($el, {{ \Illuminate\Support\Js::from($m['label']) }})"
                               x-on:mouseleave="tip = ''"
                               @class([
                                   'admin-link',
                                   'admin-link-on' => $aktif,
                               ])>
                                <x-icon.admin :name="$m['ikon']" class="shrink-0" />

                                <span class="min-w-0 flex-1 truncate lg:rail:hidden">{{ $m['label'] }}</span>

                                @if($m['rute'] === 'admin.inquiries.index' && $jumlahInquiryBaru > 0)
                                    <span class="ml-auto inline-flex h-[18px] min-w-[18px] shrink-0 items-center
                                                 justify-center rounded-full bg-brand px-1 text-admin-caption
                                                 font-semibold tabular-nums text-white lg:rail:hidden">
                                        {{ $jumlahInquiryBaru > 99 ? '99+' : $jumlahInquiryBaru }}
                                    </span>

                                    {{-- Angka, bukan titik: satu inquiry dan
                                         tiga puluh menuntut tindakan berbeda.
                                         Dibatasi 9+. --}}
                                    <span class="absolute right-0 top-0 hidden h-4 min-w-4 items-center
                                                 justify-center rounded-full bg-brand px-1 text-admin-caption
                                                 font-semibold leading-none tabular-nums text-white ring-2
                                                 ring-canvas lg:rail:inline-flex"
                                          role="img" aria-label="{{ $jumlahInquiryBaru }} inquiry baru">
                                        {{ $jumlahInquiryBaru > 9 ? '9+' : $jumlahInquiryBaru }}
                                    </span>
                                @endif
                            </a>

                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>

        {{-- ── Kaki tetap — tidak ikut menggulung. Isinya dua hal yang memang
             BUKAN menu: jalan keluar ke situs publik, dan tombol keluar. ── --}}
        <div class="shrink-0 space-y-1 border-t border-line p-3">

            <div class="relative group">
                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   x-on:mouseenter="tampilTip($el, 'Lihat situs')"
                   x-on:mouseleave="tip = ''"
                   class="admin-link">
                    <x-icon.admin name="external" class="shrink-0" />
                    <span class="truncate lg:rail:hidden">Lihat situs</span>
                </a>
            </div>

            {{-- POST, bukan tautan: permintaan GET bisa dipicu dari luar
                 (prefetch peramban, gambar) dan mengeluarkan orang tanpa ia
                 berbuat apa pun. --}}
            <form method="POST" action="{{ route('logout') }}" class="relative group">
                @csrf
                {{-- lg:rail:w-10 ditulis di sini meski .admin-link sudah
                     membawanya. Soal cascade layer, bukan spesifisitas:
                     w-full berdiri di lapisan utilities dan menang atas
                     .admin-link di lapisan components. --}}
                <button type="submit"
                        x-on:mouseenter="tampilTip($el, 'Keluar')"
                        x-on:mouseleave="tip = ''"
                        class="admin-link w-full hover:bg-danger/5 hover:text-danger lg:rail:w-10">
                    <x-icon.admin name="logout" class="shrink-0" />
                    <span class="truncate lg:rail:hidden">Keluar</span>
                </button>
            </form>
        </div>

        {{-- SATU keterangan untuk semua menu, anak langsung <aside> supaya
             lolos dari pemotong <nav>. Posisi tegaknya disetel dari
             JavaScript. --}}
        <div x-show="tip !== ''" x-cloak x-transition.opacity.duration.100ms
             x-bind:style="'top: ' + tipY + 'px'"
             x-text="tip"
             aria-hidden="true"
             class="admin-tip"></div>
    </aside>

    {{-- Posisi gulung daftar menu dipertahankan antar-halaman — tanpa ini ia
         kembali ke atas tiap kali pindah menu. --}}
    <script>
        (function () {
            var kunci = 'admin-nav-scroll';

            function pasang() {
                var nav = document.getElementById('nav-admin');
                if (! nav) return;

                /* Penanda pada elemennya sendiri, bukan variabel di luar: yang
                   perlu dijawab bukan "sudah pernah jalan?" melainkan "nav INI
                   sudah dipasangi?" — dan tiap perpindahan halaman membawa nav
                   yang baru. Tanpa ini, jalur langsung dan jalur navigated
                   memasang dua pendengar pada elemen yang sama. */
                if (nav.dataset.gulunganTerpasang) return;
                nav.dataset.gulunganTerpasang = '1';

                try {
                    var tersimpan = sessionStorage.getItem(kunci);
                    if (tersimpan) nav.scrollTop = parseInt(tersimpan, 10) || 0;
                } catch (e) {}

                nav.addEventListener('scroll', function () {
                    try {
                        sessionStorage.setItem(kunci, nav.scrollTop);
                    } catch (e) {}
                }, { passive: true });
            }

            pasang();
            document.addEventListener('livewire:navigated', pasang);
        })();
    </script>

    {{-- ══════════════════════════════════════════════════════════════════════
         KOLOM ISI
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex min-h-screen flex-col transition-[padding] duration-200 lg:pl-[264px] lg:rail:pl-[76px]">

        {{-- ── TOPBAR ─────────────────────────────────────────────────────── --}}
        {{-- Latar PEKAT, tanpa backdrop-blur: topbar ini sticky, jadi blur
             memaksa peramban memburamkan ulang seluruh bidang di belakangnya
             pada SETIAP bingkai gulungan. --}}
        <header class="sticky top-0 z-20 flex h-[64px] shrink-0 items-center gap-3 border-b border-line
                       bg-canvas px-4 sm:px-6">

            <button type="button" x-on:click="laciTerbuka = true" aria-label="Buka menu"
                    class="-ml-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                           text-ink-muted transition-colors hover:bg-mist hover:text-brand-deep lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Remah jejak: kelompok › halaman. Kelompoknya bukan tautan
                 karena ia label, bukan halaman — hanya penunjuk letak. --}}
            <div class="min-w-0 flex-1">
                <p class="flex items-center gap-1.5 text-admin-body text-ink-faint">
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
                            aria-label="{{ $jumlahInquiryBaru > 0 ? $jumlahInquiryBaru . ' inquiry baru' : 'Tidak ada inquiry baru' }}"
                            {{-- 40px rounded-control — petak yang sama persis
                                 dengan avatar di sebelahnya. --}}
                            class="relative inline-flex h-10 w-10 items-center justify-center rounded-control
                                   text-ink-muted transition-colors hover:bg-mist hover:text-brand-deep">
                        <x-icon.admin name="bell" size="h-6 w-6" />

                        {{-- Angka, bukan titik. aria-hidden karena jumlahnya
                             sudah ikut disebut di aria-label tombolnya. --}}
                        @if($jumlahInquiryBaru > 0)
                            <span aria-hidden="true"
                                  {{-- right-0/top-0 — rata dengan sudut
                                       tombol; lebih ke dalam dan keping ini
                                       menutupi loncengnya. --}}
                                  class="absolute right-0 top-0 inline-flex h-[18px] min-w-[18px] items-center
                                         justify-center rounded-full bg-brand px-1 text-admin-caption font-semibold
                                         tabular-nums text-white ring-2 ring-canvas">
                                {{ $jumlahInquiryBaru > 99 ? '99+' : $jumlahInquiryBaru }}
                            </span>
                        @endif
                    </button>

                    <div x-show="buka" x-cloak x-on:click.outside="buka = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 top-full z-50 mt-2 w-[340px] overflow-hidden rounded-corner
                                border border-line bg-canvas shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

                        <div class="border-b border-line px-4 py-3">
                            <p class="text-admin-strong text-ink">Inquiry baru</p>
                            <p class="mt-0.5 text-admin-caption text-ink-faint">
                                {{ $jumlahInquiryBaru }} permintaan menunggu ditangani
                            </p>
                        </div>

                        {{-- Bahasa visualnya dipinjam dari tabel "Inquiry
                             terbaru" di dasbor — isi yang sama dibaca dengan
                             cara yang sama di dua tempat. --}}
                        <div class="p-3">
                            <div class="overflow-hidden rounded-corner border border-line">

                                {{-- 189px = 3 baris x 63. Ditulis sebagai
                                     batas ATAS, bukan tinggi tetap. --}}
                                <div class="admin-scroll max-h-[189px] overflow-y-auto overscroll-contain">
                                @forelse($inquiryBaru as $inq)
                                    <a href="{{ route('admin.inquiries.index') }}" wire:navigate.hover
                                       class="flex items-center gap-2.5 border-b border-b-line px-3 py-3
                                              transition-colors last:border-b-0 hover:bg-mist">

                                        <x-admin.avatar :name="$inq->name" size="sm" />

                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-admin-strong text-ink"
                                                  title="{{ $inq->name }}">{{ $inq->name }}</span>
                                            <span class="mt-0.5 block truncate text-admin-caption text-ink-faint"
                                                  title="{{ $inq->company ?: $inq->email }}">{{ $inq->company ?: $inq->email }}</span>
                                        </span>

                                        {{-- Tanggal di atas jam, tabular.
                                             Tahun dibuang: kelimanya yang
                                             paling baru masuk, jadi tahun
                                             tidak pernah jadi pembeda. --}}
                                        <time datetime="{{ $inq->created_at->toIso8601String() }}"
                                              class="w-[58px] shrink-0">
                                            <span class="block text-admin-caption tabular-nums text-ink-muted">
                                                {{ $inq->created_at->locale('id')->translatedFormat('d M') }}
                                            </span>
                                            <span class="mt-0.5 block text-admin-caption tabular-nums text-ink-faint">
                                                {{ $inq->created_at->format('H:i') }}
                                            </span>
                                        </time>
                                    </a>
                                @empty
                                    <p class="px-3 py-6 text-center text-admin-body text-ink-faint">
                                        Tidak ada inquiry baru.
                                    </p>
                                @endforelse
                                </div>
                            </div>
                        </div>

                        @if($jumlahInquiryBaru > $inquiryBaru->count())
                            <a href="{{ route('admin.inquiries.index') }}"
                               wire:navigate.hover
                               class="block border-t border-line px-4 py-3 text-center text-admin-strong
                                      font-semibold text-brand transition-colors hover:bg-mist">
                                Lihat semuanya
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Pemisah: lonceng menjawab "apa yang menunggu", akun
                     menjawab "siapa saya". --}}
                <span aria-hidden="true" class="h-7 w-px shrink-0 bg-line"></span>
            @endcan

            {{-- ── Profil ─────────────────────────────────────────────────── --}}
            <div x-data="{ buka: false }" x-on:keydown.escape.window="buka = false" class="relative shrink-0">
                {{-- Pemicunya avatar saja — nama, surel, dan peran pindah ke
                     dalam menunya. aria-label yang membawa namanya. --}}
                <button type="button" x-on:click="buka = ! buka"
                        x-bind:aria-expanded="buka ? 'true' : 'false'"
                        aria-haspopup="menu"
                        aria-label="Menu akun — {{ $pengguna?->name }}"
                        {{-- Ikut bulat: latar sorot yang menyiku di
                             sekeliling lingkaran menyisakan empat sudut abu. --}}
                        class="flex rounded-full p-0.5 transition-colors hover:bg-mist">

                    {{-- BULAT, satu-satunya di panel: seluruh petak 40px lain
                         bersudut rounded-control. Avatar mewakili seorang
                         ORANG, bukan menu atau berkas. --}}
                    <x-admin.avatar :name="$pengguna?->name" size="lg" />

                </button>

                <div x-show="buka" x-cloak x-on:click.outside="buka = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     role="menu"
                     class="absolute right-0 top-full z-50 mt-2 w-[268px] overflow-hidden rounded-corner
                            border border-line bg-canvas shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

                    {{-- Nama → surel → peran, dari yang paling sering dicari
                         ke yang paling jarang. Topbar tidak lagi menampilkan
                         apa pun selain avatarnya. --}}
                    <div class="border-b border-line px-4 pb-3.5 pt-3.5">
                        <p class="truncate text-admin-title text-ink">{{ $pengguna?->name }}</p>

                        <p class="mt-0.5 break-all text-admin-body text-ink-muted">
                            {{ $pengguna?->email }}
                        </p>

                        {{-- Pil, bukan baris teks kapital: bentuk berpil di
                             panel ini sudah berarti "keadaan yang melekat
                             pada sesuatu". --}}
                        @if($pengguna?->roles->isNotEmpty())
                            <span class="mt-2.5 inline-flex items-center rounded-full bg-brand/10 px-2.5 py-1
                                         text-admin-caption font-semibold text-brand">
                                {{ $pengguna->roles->pluck('name')->implode(', ') }}
                            </span>
                        @endif
                    </div>

                    {{-- Dua baris saja. Pengaturan dan Pengguna sudah berdiri
                         di bilah sisi; menyalinnya ke sini membuat satu
                         halaman punya dua pintu yang harus dijaga sama. --}}
                    {{-- Bantalan dipasang di WADAHNYA, bukan sebagai margin
                         tiap baris: barisnya memakai w-full, dan margin
                         berdiri di luar kotak — lebar penuh plus margin
                         menyembul melewati tepi menu. --}}
                    <div class="p-1.5">
                        {{-- Bentuk dan warna sama persis dengan menu di bilah
                             sisi — keduanya baris yang membawa berpindah. --}}
                        <a href="{{ route('home') }}" target="_blank" rel="noopener"
                           class="admin-menu-row rounded-control hover:text-brand-deep" role="menuitem">
                            <x-icon.admin name="external" size="h-4 w-4" class="shrink-0" />
                            Lihat situs
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="admin-menu-row rounded-control hover:bg-danger/5 hover:text-danger"
                                    role="menuitem">
                                <x-icon.admin name="logout" size="h-4 w-4" class="shrink-0" />
                                Keluar
                            </button>
                        </form>
                    </div>
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
