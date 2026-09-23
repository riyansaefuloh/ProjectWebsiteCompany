@props([
    'companyName' => '',
    'logo' => '',
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

    @if($overHero)
        <div x-show="!solid" x-cloak aria-hidden="true"
             class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[150px]
                    bg-[linear-gradient(to_bottom,rgba(0,0,0,0.58)_0%,rgba(0,0,0,0.58)_55%,transparent_100%)]"></div>
    @endif

    <div class="shell grid h-[76px] grid-cols-[1fr_auto] items-center gap-4 lg:grid-cols-[1fr_auto_1fr] lg:gap-3 xl:gap-6">

        <a href="{{ route('home') }}"
           class="group flex min-w-0 shrink items-center gap-3"
           aria-label="{{ $companyName }}">

            @if($logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                     class="h-9 w-auto max-w-[132px] shrink-0 object-contain brightness-0 invert">
            @else
                
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

        <nav class="hidden items-center gap-1 lg:flex"
             aria-label="{{ __('site.nav_primary') }}">

            @foreach($navItems as $item)
                @if(!empty($item['children']))
                    
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
