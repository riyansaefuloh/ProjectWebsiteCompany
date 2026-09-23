@php
    $pengaturan     = \App\Models\Setting::pluck('value', 'key')->toArray();
    $namaPerusahaan = $pengaturan['company_name'] ?? config('app.name');
    $logo           = $pengaturan['logo'] ?? '';
    $favicon        = $pengaturan['favicon'] ?? '';

    $pengguna = auth()->user();

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

    $kelompokMenu = collect($kelompokMenu)
        ->map(fn ($menu) => array_values(array_filter(
            $menu,
            fn ($m) => $m['izin'] === null || $pengguna?->can($m['izin'])
        )))
        ->filter(fn ($menu) => count($menu) > 0)
        ->all();

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

    @if($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @else
        <link rel="icon" type="image/svg+xml"
              href="{{ \App\Support\Monogram::favicon($namaPerusahaan) }}">
    @endif

    <script>
        if (localStorage.getItem('sidebar-rail') === '1') {
            document.documentElement.classList.add('sidebar-rail');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="admin-shell min-h-screen bg-shell font-ui text-admin-body text-ink"
      x-data="{
          laciTerbuka: false,
          sempit: document.documentElement.classList.contains('sidebar-rail'),

          lipat() {
              this.sempit = ! this.sempit;
              document.documentElement.classList.toggle('sidebar-rail', this.sempit);
              localStorage.setItem('sidebar-rail', this.sempit ? '1' : '0');
          },
      }">

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

    <aside x-bind:class="laciTerbuka ? 'translate-x-0' : '-translate-x-full'"
           x-data="{
               tip: '',
               tipY: 0,

               tampilTip(el, teks) {
                   
                   if (window.innerWidth < 1024) return;
                   if (! document.documentElement.classList.contains('sidebar-rail')) return;

                   const kotak = el.getBoundingClientRect();

                   this.tip  = teks;
                   
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

            <span class="flex h-10 w-10 shrink-0 items-center justify-center">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                         class="h-8 w-8 object-contain">
                @else
                    
                    <span class="text-admin-title font-bold leading-none text-heading"
                          aria-hidden="true">
                        {{ \App\Support\Monogram::inisial($namaPerusahaan) ?: '·' }}
                    </span>
                @endif
            </span>

            <span class="min-w-0 lg:rail:hidden">
                <span class="block truncate text-admin-title text-heading">
                    {{ $namaPerusahaan }}
                </span>
                <span class="mt-0.5 block text-admin-overline uppercase text-ink-faint">Panel Admin</span>
            </span>

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
        
        <nav id="nav-admin"
             class="admin-scroll flex-1 overflow-y-auto overflow-x-hidden px-3 pb-4">
            @foreach($kelompokNav as $namaKelompok => $menu)
                
                <p class="admin-group">{{ $namaKelompok }}</p>
                <div class="mx-2 hidden border-t border-line lg:rail:my-3 lg:rail:block"></div>

                <ul class="space-y-1">
                    @foreach($menu as $m)
                        @php $aktif = $ruteAktif === $m['rute']; @endphp

                        <li class="relative group">
                            
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
                                                 justify-center rounded-full bg-brand-deep px-1 text-admin-caption
                                                 font-semibold tabular-nums text-white lg:rail:hidden">
                                        {{ $jumlahInquiryBaru > 99 ? '99+' : $jumlahInquiryBaru }}
                                    </span>

                                    <span class="absolute right-0 top-0 hidden h-4 min-w-4 items-center
                                                 justify-center rounded-full bg-brand-deep px-1 text-admin-caption
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

            <form method="POST" action="{{ route('logout') }}" class="relative group">
                @csrf
                
                <button type="submit"
                        x-on:mouseenter="tampilTip($el, 'Keluar')"
                        x-on:mouseleave="tip = ''"
                        class="admin-link w-full hover:bg-danger/5 hover:text-danger lg:rail:w-10">
                    <x-icon.admin name="logout" class="shrink-0" />
                    <span class="truncate lg:rail:hidden">Keluar</span>
                </button>
            </form>
        </div>

        <div x-show="tip !== ''" x-cloak x-transition.opacity.duration.100ms
             x-bind:style="'top: ' + tipY + 'px'"
             x-text="tip"
             aria-hidden="true"
             class="admin-tip"></div>
    </aside>

    <script>
        (function () {
            var kunci = 'admin-nav-scroll';

            function pasang() {
                var nav = document.getElementById('nav-admin');
                if (! nav) return;

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

    <div class="flex min-h-screen flex-col transition-[padding] duration-200 lg:pl-[264px] lg:rail:pl-[76px]">

        {{-- ── TOPBAR ─────────────────────────────────────────────────────── --}}
        
        <header class="sticky top-0 z-20 flex h-[64px] shrink-0 items-center gap-3 border-b border-line
                       bg-canvas px-4 sm:px-6">

            <button type="button" x-on:click="laciTerbuka = true" aria-label="Buka menu"
                    class="-ml-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-control
                           text-ink-muted transition-colors hover:bg-mist hover:text-brand-deep lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>

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
                            
                            class="relative inline-flex h-10 w-10 items-center justify-center rounded-control
                                   text-ink-muted transition-colors hover:bg-mist hover:text-brand-deep">
                        <x-icon.admin name="bell" size="h-6 w-6" />

                        @if($jumlahInquiryBaru > 0)
                            <span aria-hidden="true"
                                  
                                  class="absolute right-0 top-0 inline-flex h-[18px] min-w-[18px] items-center
                                         justify-center rounded-full bg-brand-deep px-1 text-admin-caption font-semibold
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

                        <div class="p-3">
                            <div class="overflow-hidden rounded-corner border border-line">

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

                <span aria-hidden="true" class="h-7 w-px shrink-0 bg-line"></span>
            @endcan

            {{-- ── Profil ─────────────────────────────────────────────────── --}}
            <div x-data="{ buka: false }" x-on:keydown.escape.window="buka = false" class="relative shrink-0">
                
                <button type="button" x-on:click="buka = ! buka"
                        x-bind:aria-expanded="buka ? 'true' : 'false'"
                        aria-haspopup="menu"
                        aria-label="Menu akun — {{ $pengguna?->name }}"
                        
                        class="flex rounded-full p-0.5 transition-colors hover:bg-mist">

                    <x-admin.avatar :name="$pengguna?->name" size="lg" tone="brand" />

                </button>

                <div x-show="buka" x-cloak x-on:click.outside="buka = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     role="menu"
                     class="absolute right-0 top-full z-50 mt-2 w-[268px] overflow-hidden rounded-corner
                            border border-line bg-canvas shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

                    <div class="border-b border-line px-4 pb-3.5 pt-3.5">
                        <p class="truncate text-admin-title text-heading">{{ $pengguna?->name }}</p>

                        <p class="mt-0.5 break-all text-admin-body text-ink-muted">
                            {{ $pengguna?->email }}
                        </p>

                        @if($pengguna?->roles->isNotEmpty())
                            <span class="mt-2.5 inline-flex items-center rounded-full bg-brand/10 px-2.5 py-1
                                         text-admin-caption font-semibold text-brand">
                                {{ $pengguna->roles->pluck('name')->implode(', ') }}
                            </span>
                        @endif
                    </div>

                    <div class="p-1.5">
                        
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
