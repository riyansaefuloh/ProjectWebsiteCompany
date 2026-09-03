@props([
    'companyName' => '',
    'logo' => '',

    /* Dua keadaan, satu bilah.

       overHero=false — bilah berdiri di atas kanvas krem: latar gading tembus,
       garis bawah tipis, huruf tinta. Ini yang tampil di seluruh halaman
       sekarang.

       overHero=true  — bilah mengambang di atas foto hero: latar hilang, huruf
       jadi putih, dan kapsul menu memakai kaca gelap. Dipakai di beranda saja,
       dan hanya kalau hero benar-benar bagian teratasnya — lihat $heroDiPuncak
       di layouts/public.blade.php. Memanggilnya di halaman tanpa bidang gelap
       berarti huruf putih di atas krem. */
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
        ? 'border-line bg-canvas/85 backdrop-blur-md'
        : 'border-transparent bg-transparent'"
    class="{{ $overHero ? 'fixed' : 'sticky' }} top-0 z-50 w-full border-b transition-colors duration-300">

    {{-- Peredam gelap, HANYA saat mengambang.

         MENAHAN setinggi bilah, baru memudar sesudahnya — bukan memudar dari
         tepi atas. Gradasi yang memudar di DALAM bilah tidak bisa menjamin apa
         pun di ujung transparannya: pada foto putih polos, dasar bilah cuma
         mencapai 2,4:1.

         Ditahan pada 0,60, bukan 0,55: sejak kapsul menu dilepas, huruf menu
         berdiri telanjang di atas foto dan tidak lagi punya bidangnya sendiri.
         Lima perseratus itu yang membawanya dari 4,2:1 ke 5,1:1.

         Ini satu-satunya peredam di tepi atas. Hero sengaja tidak membuat
         miliknya sendiri, supaya tidak ada dua yang bertumpuk dan saling tidak
         tahu. --}}
    @if($overHero)
        <div x-show="!solid" x-cloak aria-hidden="true"
             class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[150px]
                    bg-[linear-gradient(to_bottom,rgba(0,0,0,0.66)_0%,rgba(0,0,0,0.60)_55%,transparent_100%)]"></div>
    @endif

    <div class="shell grid h-[76px] grid-cols-[1fr_auto] items-center gap-4 lg:grid-cols-[1fr_auto_1fr] lg:gap-3 xl:gap-6">

        {{-- ── KIRI: lambang + nama perusahaan ──────────────────────────────
             Keduanya diatur dari panel: Pengaturan → Identitas. Nama tetap
             digambar meski lambangnya ada — lambang tanpa nama cuma bisa
             dikenali orang yang sudah tahu perusahaannya. --}}
        <a href="{{ route('home') }}"
           class="group flex min-w-0 shrink items-center gap-3"
           aria-label="{{ $companyName }}">

            {{-- Lambang selalu berdiri di dalam BINGKAI, sama seperti di kepala
                 bilah sisi panel admin dan di halaman masuk: petak 40px
                 bersudut rounded-control, bergaris tipis, berlatar sendiri.

                 Bukan sekadar demi seragam. Lambang yang ditempel telanjang di
                 atas foto ikut mewarisi apa pun yang kebetulan ada di
                 belakangnya — dan lambang berwarna terang di atas langit terang
                 lenyap. Bingkai memberinya latarnya sendiri, jadi ia terbaca
                 sama di atas foto apa pun. --}}
            <span x-bind:class="solid
                      ? 'border-line bg-mist'
                      : 'border-white/25 bg-black/25 backdrop-blur-md'"
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-control
                         border transition-colors duration-300">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                         x-bind:class="solid ? '' : 'brightness-0 invert'"
                         class="h-6 w-6 object-contain transition-[filter] duration-300">
                @else
                    {{-- Cadangan saat lambang belum diunggah. Bukan kotak kosong:
                         halaman tanpa lambang apa pun terbaca seperti belum jadi. --}}
                    <svg x-bind:class="solid ? 'text-brand' : 'text-white'"
                         class="h-5 w-5 transition-colors duration-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3c4.6 0 8 3.9 8 9s-3.4 9-8 9-8-3.9-8-9 3.4-9 8-9Z"
                              stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 4.4c-2.3 2.6-2.3 5.9 0 8.6s2.3 6 0 8.6"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                @endif
            </span>

            <span x-bind:class="solid ? 'text-ink' : 'text-white'"
                  class="truncate font-site-display text-site-lede font-bold tracking-[-0.015em] transition-colors duration-300 xl:text-site-title">
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
                                x-bind:class="solid
                                    ? '{{ $item['active'] ? 'bg-brand text-canvas' : 'text-ink-muted hover:bg-mist hover:text-ink' }}'
                                    : '{{ $item['active'] ? 'bg-site-shade/55 text-white ring-1 ring-white/25' : 'text-white/90 hover:bg-white/10 hover:text-white' }}'"
                                class="nav-pill flex items-center gap-1.5">
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
                            <div class="overflow-hidden rounded-corner border border-line bg-canvas p-1.5
                                        shadow-[0_20px_48px_-20px_rgba(20,32,26,0.32)]">
                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}"
                                       class="block rounded-[10px] px-3.5 py-2.5 text-site-small font-medium transition-colors
                                              {{ $child['active'] ? 'bg-brand-wash text-brand' : 'text-ink-muted hover:bg-mist hover:text-ink' }}">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}"
                       @if($item['active']) aria-current="page" @endif
                       x-bind:class="solid
                           ? '{{ $item['active'] ? 'bg-brand text-canvas' : 'text-ink-muted hover:bg-mist hover:text-ink' }}'
                           : '{{ $item['active'] ? 'bg-site-shade/55 text-white ring-1 ring-white/25' : 'text-white/90 hover:bg-white/10 hover:text-white' }}'"
                       class="nav-pill">
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
                    x-bind:class="solid
                        ? 'ring-1 ring-line text-ink-muted hover:bg-mist hover:text-ink'
                        : 'bg-site-shade/45 ring-1 ring-white/25 text-white hover:bg-site-shade/60'"
                    class="flex items-center gap-1.5 rounded-full py-2.5 pl-4 pr-3.5
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
                <div class="overflow-hidden rounded-corner border border-line bg-canvas p-1.5
                            shadow-[0_20px_48px_-20px_rgba(20,32,26,0.32)]">
                    @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                        <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                           rel="alternate" hreflang="{{ $code }}"
                           class="flex items-center justify-between gap-3 rounded-[10px] px-3.5 py-2.5
                                  text-site-small font-medium transition-colors
                                  {{ app()->getLocale() === $code ? 'bg-brand-wash text-brand' : 'text-ink-muted hover:bg-mist hover:text-ink' }}">
                            {{ $props['native'] }}
                            <span class="font-site-accent text-site-micro tracking-[0.08em] text-ink-faint">
                                {{ $localeLabels[$code] ?? strtoupper($code) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Ajakan utama: pil PEJAL hijau merek, dan warnanya SATU untuk kedua
             keadaan — tidak ikut berganti saat halaman digulung.

             Hijau di L* 46 cukup terang untuk berdiri sendiri di atas foto gelap
             tanpa dibantu apa pun, dan cukup gelap untuk membawa huruf gading
             pada 5,03:1. Emas sempat dipakai di sini dan dikembalikan ke
             perannya semula: menandai label, bukan menjadi tombol. Ajakan yang
             warnanya berganti-ganti menurut posisi gulungan sulit dikenali
             sebagai satu benda yang sama.

             Bercincin gelap HANYA saat mengambang, dan itu bukan hiasan.

             Hijau L* 46 adalah warna TENGAH, dan warna tengah cuma punya tepi
             yang kuat di atas latar yang sangat gelap atau sangat terang. Di
             atas foto gelap tepinya 3,9:1; di atas foto terang ia jatuh ke
             1,9:1 — tombolnya melebur meski hurufnya tetap terbaca. Cincin
             nyaris hitam lenyap di atas gelap dan justru menggambar tepinya di
             atas terang, jadi ia menyesuaikan diri tanpa perlu tahu fotonya.

             Di atas krem cincin itu DILEPAS: di sana tepinya sudah 5,0:1, dan
             garis gelap yang tidak dibutuhkan cuma jadi outline yang mengganggu. --}}
        <a href="{{ route('inquiry.index') }}"
           x-bind:class="solid ? '' : 'ring-1 ring-site-shade/80'"
           class="hidden items-center gap-2 rounded-full bg-brand py-2.5 pl-5 pr-4 text-canvas
                  shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                  hover:bg-brand-deep
                  font-site-body text-site-small font-semibold whitespace-nowrap
                  transition-colors duration-300 sm:inline-flex">
            {{ __('site.cta_request_quote') }}
            <svg class="h-3 w-3 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

            {{-- Tombol laci mobile --}}
            <button type="button"
                    x-on:click="mobile = !mobile"
                    x-bind:aria-expanded="mobile ? 'true' : 'false'"
                    aria-label="{{ __('site.nav_primary') }}"
                    x-bind:class="solid
                        ? 'text-ink hover:bg-mist'
                        : 'text-white hover:bg-white/10'"
                    class="-mr-1.5 inline-flex h-10 w-10 items-center justify-center rounded-full transition-colors lg:hidden">
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
    <div x-show="mobile" x-cloak x-collapse class="border-t border-line bg-canvas lg:hidden">
        <nav class="shell flex flex-col py-4" aria-label="{{ __('site.nav_primary') }}">
            @foreach($navItems as $item)
                @if(!empty($item['children']))
                    <p class="eyebrow mb-1 mt-4 px-1">{{ $item['label'] }}</p>
                    @foreach($item['children'] as $child)
                        <a href="{{ $child['url'] }}"
                           class="rounded-[10px] px-1 py-2.5 text-site-body font-semibold transition-colors
                                  {{ $child['active'] ? 'text-brand' : 'text-ink-muted' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                    <span class="mb-2 mt-3 h-px bg-line"></span>
                @else
                    <a href="{{ $item['url'] }}"
                       @if($item['active']) aria-current="page" @endif
                       class="border-b border-line px-1 py-3 text-site-body font-semibold transition-colors
                              {{ $item['active'] ? 'text-brand' : 'text-ink-muted' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            <a href="{{ route('inquiry.index') }}"
               class="mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-brand px-5 py-3
                      font-site-body text-site-small font-semibold
                      text-canvas transition-colors hover:bg-brand-deep">
                {{ __('site.cta_request_quote') }}
                <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <div class="mt-3 flex items-center gap-1.5">
                @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                    <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                       rel="alternate" hreflang="{{ $code }}"
                       class="flex-1 rounded-full px-4 py-2.5 text-center font-site-body text-site-small
                              font-medium transition-colors
                              {{ app()->getLocale() === $code
                                   ? 'bg-brand-wash text-brand'
                                   : 'text-ink-muted ring-1 ring-line hover:text-ink' }}">
                        {{ $props['native'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</header>
