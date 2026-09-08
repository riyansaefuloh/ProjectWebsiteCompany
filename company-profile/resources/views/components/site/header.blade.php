@props([
    'companyName' => '',
    'logo' => '',

    /* Dua keadaan, satu bilah — dan hurufnya PUTIH di keduanya.

       overHero=false — bilah menempel (sticky) dan pejal sejak awal: latar
       forest PENUH, garis bawah forest-line. Ini yang tampil di seluruh halaman
       selain beranda.

       Pejal betulan, bukan 95% berkabut seperti dulu: nilainya harus sama
       persis dengan badan pil "Explore Products" di seksi produk, dan latar
       tembus yang bercampur dengan apa pun di belakangnya tidak pernah
       menghasilkan nilai yang sama dua kali.

       overHero=true  — bilah mengambang (fixed) di atas foto hero: latar dan
       garisnya hilang sama sekali, dan yang menjamin keterbacaan hurufnya
       adalah peredam gradasi di bawah ini. Begitu halaman digulung lewat 40px
       ia berubah jadi keadaan pejal yang sama. Dipakai di beranda saja, dan
       hanya kalau hero benar-benar bagian teratasnya — lihat $heroDiPuncak di
       layouts/public.blade.php.

       Karena hurufnya putih di kedua keadaan, memanggilnya di halaman yang
       bagian teratasnya TERANG berarti putih di atas krem: yang berubah saat
       digulung cuma latar bilahnya, bukan warna hurufnya. */
    'overHero' => false,
])

@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $aboutChildren = [
        ['label' => __('site.nav_profile'),        'url' => route('about'),                'active' => request()->routeIs('about')],
        ['label' => __('site.nav_certifications'), 'url' => route('certifications.index'), 'active' => request()->routeIs('certifications.*')],
    ];

    $navItems = [
        ['label' => __('site.nav_home'),           'url' => route('home'),                 'active' => request()->routeIs('home')],
        ['label' => __('site.nav_about'),          'url' => $aboutChildren[0]['url'],      'active' => collect($aboutChildren)->contains(fn ($c) => $c['active']), 'children' => $aboutChildren],
        ['label' => __('site.nav_products'),       'url' => route('products.index'),       'active' => request()->routeIs('products.*')],
        ['label' => __('site.nav_export_markets'), 'url' => route('export-markets.index'), 'active' => request()->routeIs('export-markets.*')],
        ['label' => __('site.nav_news'),           'url' => route('news.index'),           'active' => request()->routeIs('news.*')],
    ];

    $localeLabels = ['en' => 'EN', 'id' => 'ID'];
@endphp

<header
    x-data="{
        mobile: false,
        scrolled: {{ $overHero ? 'false' : 'true' }},
        /* Laci mobile berlatar gading, jadi bilah di atasnya harus ikut pejal —
           kalau tidak, tepi keduanya tidak menyambung. */
        get solid() { return this.scrolled || this.mobile }
    }"
    @if($overHero)
        x-init="scrolled = window.scrollY > 40"
        x-on:scroll.window="scrolled = window.scrollY > 40"
    @endif
    x-bind:class="solid
        ? 'border-site-forest-line bg-site-forest'
        : 'border-transparent bg-transparent'"
    class="{{ $overHero ? 'fixed' : 'sticky' }} top-0 z-50 w-full border-b transition-colors duration-300">

    {{-- Peredam gelap, HANYA saat mengambang.

         MENAHAN setinggi bilah, baru memudar sesudahnya — bukan memudar dari
         tepi atas. Gradasi yang memudar di DALAM bilah tidak bisa menjamin apa
         pun di ujung transparannya: pada foto putih polos, dasar bilah cuma
         mencapai 2,4:1.

         Ditahan pada 0,58: sejak kapsul menu dilepas, huruf menu berdiri
         telanjang di atas foto dan tidak lagi punya bidangnya sendiri. 0,58
         adalah alfa terkecil yang masih menjamin 4,5:1 sekalipun fotonya putih
         polos — 4,79:1 — jadi menaikkannya lagi hanya menggelapkan foto tanpa
         menukarnya dengan keterbacaan apa pun.

         Ini satu-satunya peredam di tepi atas. Hero sengaja tidak membuat
         miliknya sendiri, supaya tidak ada dua yang bertumpuk dan saling tidak
         tahu. --}}
    @if($overHero)
        <div x-show="!solid" x-cloak aria-hidden="true"
             class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[150px]
                    bg-[linear-gradient(to_bottom,rgba(0,0,0,0.58)_0%,rgba(0,0,0,0.58)_55%,transparent_100%)]"></div>
    @endif

    <div class="shell grid h-[76px] grid-cols-[1fr_auto] items-center gap-4 lg:grid-cols-[1fr_auto_1fr] lg:gap-3 xl:gap-6">

        {{-- ── KIRI: lambang + nama perusahaan ──────────────────────────────
             Keduanya diatur dari panel: Pengaturan → Identitas. Nama tetap
             digambar meski lambangnya ada — lambang tanpa nama cuma bisa
             dikenali orang yang sudah tahu perusahaannya. --}}
        <a href="{{ route('home') }}"
           class="group flex min-w-0 shrink items-center gap-3"
           aria-label="{{ $companyName }}">

            {{-- Lambang berdiri telanjang, tanpa bingkai.

                 Saat mengambang ia dibalik jadi PUTIH PENUH (brightness-0
                 invert), bukan dibiarkan berwarna aslinya. Itu yang
                 menggantikan tugas bingkainya: lambang berwarna terang di atas
                 langit terang akan lenyap, sementara siluet putih pekat tetap
                 terbaca di atas foto apa pun — dan peredam bilah kepala sudah
                 menjamin latarnya cukup gelap. --}}
            @if($logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                     class="h-9 w-auto max-w-[132px] shrink-0 object-contain brightness-0 invert">
            @else
                {{-- Belum ada logo: MONOGRAM, bukan lambang bergambar.

                     Yang dulu berdiri di sini sebuah SVG biji kopi, dan itu
                     keliru ke arah yang paling membingungkan — ia menempati
                     persis tempat logo, jadi terbaca SEBAGAI logo. Menghapus
                     logo dari panel lalu tetap melihat lambang di sana membaca
                     seperti penghapusan yang gagal.

                     Inisial tidak punya masalah itu: ia jelas diturunkan dari
                     nama yang tertulis tepat di sebelahnya. Bingkainya pun
                     dibuat bergaris tipis dan tembus, bukan pejal — supaya ia
                     terbaca sebagai tempat yang MENUNGGU diisi, bukan sebagai
                     lambang yang sudah jadi.

                     Bukan pula kotak "gambar kosong" seperti pada kartu produk:
                     di slot isi, kotak abu berikon gambar berarti "fotonya
                     belum ada"; di chrome merek, ia terbaca sebagai gambar yang
                     GAGAL DIMUAT. --}}
                @php $inisial = \App\Support\Monogram::inisial($companyName); @endphp

                @if($inisial !== '')
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px]
                                 border border-white/30 bg-white/10 font-site-display
                                 text-site-small font-bold leading-none text-white"
                          aria-hidden="true">
                        {{ $inisial }}
                    </span>
                @endif
            @endif

            <span class="truncate text-white font-site-display text-site-lede font-bold tracking-[-0.015em] transition-colors duration-300 xl:text-site-title">
                {{ $companyName }}
            </span>
        </a>

        {{-- ── TENGAH: menu ─────────────────────────────────────────────────
             Berderet telanjang, TANPA kapsul yang mengurungnya. Kapsulnya
             sempat ada dan dihapus: di atas kanvas krem ia cuma berselisih
             1,08:1 dengan latarnya, jadi secara optis ia bukan bidang yang
             dipilih melainkan noda. Yang memisahkan menu dari lambang di kiri
             dan ajakan di kanan sekarang tinggal jarak — dan itu cukup.

             Hanya menu AKTIF yang berpil. Memberi pil pada semuanya sama saja
             dengan mengembalikan kapsul yang baru saja dilepas. --}}
        <nav class="hidden items-center gap-1 lg:flex"
             aria-label="{{ __('site.nav_primary') }}">

            @foreach($navItems as $item)
                @if(!empty($item['children']))
                    {{-- Dibuka oleh SOROT sekaligus KLIK. Sorot saja meninggalkan
                         papan ketik dan layar sentuh tanpa jalan masuk. --}}
                    <div x-data="{ open: false }"
                         x-on:mouseenter="open = true"
                         x-on:mouseleave="open = false"
                         x-on:keydown.escape.window="open = false"
                         class="relative">

                        <button type="button"
                                x-on:click="open = !open"
                                x-bind:aria-expanded="open ? 'true' : 'false'"
                                class="nav-pill flex items-center gap-1.5
                                       {{ $item['active']
                                            ? 'bg-white/12 text-white ring-1 ring-white/20'
                                            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            {{ $item['label'] }}
                            <svg class="h-2.5 w-2.5 shrink-0 transition-transform duration-200"
                                 x-bind:class="open && 'rotate-180'"
                                 viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        {{-- pt-3 pada pembungkusnya, bukan margin pada panelnya:
                             jarak itu harus tetap bisa disorot, kalau tidak menu
                             menutup sendiri saat kursor menyeberang ke panelnya. --}}
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-1/2 top-full w-56 -translate-x-1/2 pt-3">
                            <div class="overflow-hidden rounded-corner border border-site-forest-line
                                        bg-site-forest p-1.5 shadow-[0_20px_48px_-18px_rgba(11,13,12,0.65)]">
                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}"
                                       class="block rounded-[10px] px-3.5 py-2.5 text-site-small transition-colors
                                              {{ $child['active']
                                                   ? 'bg-white/10 font-semibold text-site-gilt'
                                                   : 'font-medium text-white/75 hover:bg-white/10 hover:text-white' }}">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}"
                       @if($item['active']) aria-current="page" @endif
                       class="nav-pill
                              {{ $item['active']
                                   ? 'bg-white/12 text-white ring-1 ring-white/20'
                                   : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- ── KANAN ───────────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-1.5 xl:gap-2">

        {{-- Penukar bahasa: pil TENANG, berdiri sendiri.
             Ia setelan, bukan tindakan yang ditawarkan halaman ini — jadi ia
             bergaris di atas krem dan berkaca gelap di atas foto, sementara
             ajakan di sebelahnya pejal. Dua pil sederajat berat akan membuat
             keduanya berebut, dan yang kalah justru ajakannya.

             Kaca GELAP, bukan terang: putih/12 di atas foto cerah cuma
             menyisakan 4,4:1 untuk hurufnya. Yang gelap memberi 10,9:1 di foto
             mana pun, dan senada dengan pil menu aktif di sebelah kirinya. --}}
        <div x-data="{ open: false }"
             x-on:mouseleave="open = false"
             x-on:keydown.escape.window="open = false"
             class="relative hidden sm:block">

            <button type="button"
                    x-on:click="open = !open"
                    x-bind:aria-expanded="open ? 'true' : 'false'"
                    aria-label="{{ __('site.nav_primary') }}"
                    class="flex h-10 items-center gap-1.5 rounded-full pl-4 pr-3.5
                           text-white ring-1 ring-white/25 hover:bg-white/10
                           font-site-body text-site-small font-medium
                           transition-colors duration-300">
                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M1.8 8h12.4M8 1.8c1.7 1.8 2.6 4 2.6 6.2S9.7 12.4 8 14.2C6.3 12.4 5.4 10.2 5.4 8S6.3 3.6 8 1.8Z"
                          stroke="currentColor" stroke-width="1.3"/>
                </svg>
                {{ $localeLabels[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}
                <svg class="h-2.5 w-2.5 shrink-0 transition-transform duration-200"
                     x-bind:class="open && 'rotate-180'"
                     viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute right-0 top-full w-44 pt-3">
                <div class="overflow-hidden rounded-corner border border-site-forest-line
                            bg-site-forest p-1.5 shadow-[0_20px_48px_-18px_rgba(11,13,12,0.65)]">
                    @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                        <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                           rel="alternate" hreflang="{{ $code }}"
                           class="flex items-center justify-between gap-3 rounded-[10px] px-3.5 py-2.5
                                  text-site-small transition-colors
                                  {{ app()->getLocale() === $code
                                       ? 'bg-white/10 font-semibold text-site-gilt'
                                       : 'font-medium text-white/75 hover:bg-white/10 hover:text-white' }}">
                            {{ $props['native'] }}
                            <span class="font-site-accent text-site-micro tracking-[0.08em] text-white/45">
                                {{ $localeLabels[$code] ?? strtoupper($code) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Ajakan utama: pil PEJAL BERAKSEN, warna yang sama dengan label hero
             dan kata aksen judulnya. Satu untuk kedua keadaan — tidak ikut berganti
             saat halaman digulung, karena ajakan yang warnanya berpindah-pindah
             sulit dikenali sebagai satu benda yang sama.

             Isinya BERBALIK gelap, dan itu wajib: putih di atas aksen cuma
             2,10:1. Huruf dan bulatan panahnya memakai forest, yang memberi
             6,99:1 — dan bulatan gelap di dalam pil terang adalah kebalikan
             persis dari susunan di gambar acuan, tempat pil gelap membawa
             bulatan terang.

             Bercincin gilt-deep. Nada terang aksen duduk di L* 73, nyaris
             seterang kanvas krem: sebagai bidang ia cuma berselisih 2,01:1 di
             sana, jadi saat halaman digulung tepinya berhenti terbaca sebagai
             tombol. Cincin nada dalam menggambar tepi itu tanpa menambah warna
             baru ke palet. --}}
        <a href="{{ route('inquiry.index') }}"
           class="hidden h-10 items-center rounded-full bg-site-gilt px-6
                  text-site-forest ring-1 ring-site-gilt-deep/70
                  shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                  hover:bg-site-gilt-soft
                  font-site-body text-site-small font-semibold whitespace-nowrap
                  transition-colors duration-300 sm:inline-flex">
            {{ __('site.cta_request_quote') }}
        </a>

            {{-- Tombol laci mobile --}}
            <button type="button"
                    x-on:click="mobile = !mobile"
                    x-bind:aria-expanded="mobile ? 'true' : 'false'"
                    aria-label="{{ __('site.nav_primary') }}"
                    class="-mr-1.5 inline-flex h-10 w-10 text-white hover:bg-white/10 items-center justify-center rounded-full transition-colors lg:hidden">
                <svg x-show="!mobile" class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                <svg x-show="mobile" x-cloak class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── LACI MOBILE ──────────────────────────────────────────────────────
         Anak menu "Tentang Kami" digambar sebagai daftar bertakuk, bukan di
         balik menu turun kedua: di layar sempit, menu turun di dalam laci
         berarti dua lapisan yang harus dibuka berurutan untuk sampai ke satu
         halaman. --}}
    <div x-show="mobile" x-cloak x-collapse class="border-t border-site-forest-line bg-site-forest lg:hidden">
        <nav class="shell flex flex-col py-4" aria-label="{{ __('site.nav_primary') }}">
            @foreach($navItems as $item)
                @if(!empty($item['children']))
                    <p class="eyebrow mb-1 mt-4 px-1">{{ $item['label'] }}</p>
                    @foreach($item['children'] as $child)
                        <a href="{{ $child['url'] }}"
                           class="rounded-[10px] px-1 py-2.5 text-site-body font-semibold transition-colors
                                  {{ $child['active'] ? 'text-site-gilt' : 'text-white/75' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                    <span class="mb-2 mt-3 h-px bg-site-forest-line"></span>
                @else
                    <a href="{{ $item['url'] }}"
                       @if($item['active']) aria-current="page" @endif
                       class="border-b border-site-forest-line px-1 py-3 text-site-body font-semibold transition-colors
                              {{ $item['active'] ? 'text-site-gilt' : 'text-white/75' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            <a href="{{ route('inquiry.index') }}"
               class="mt-6 inline-flex items-center justify-center rounded-full bg-site-gilt px-6 py-3
                      font-site-body text-site-small font-semibold text-site-forest
                      ring-1 ring-site-gilt-deep/70 transition-colors hover:bg-site-gilt-soft">
                {{ __('site.cta_request_quote') }}
            </a>

            <div class="mt-3 flex items-center gap-1.5">
                @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                    <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                       rel="alternate" hreflang="{{ $code }}"
                       class="flex-1 rounded-full px-4 py-2.5 text-center font-site-body text-site-small
                              font-medium transition-colors
                              {{ app()->getLocale() === $code
                                   ? 'bg-white/12 text-site-gilt'
                                   : 'text-white/70 ring-1 ring-white/20 hover:text-white' }}">
                        {{ $props['native'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</header>
