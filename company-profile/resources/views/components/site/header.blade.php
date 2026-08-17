@props([
    'companyName' => '',
    'logo' => '',

    // ── DUA MODE HEADER ──────────────────────────────────────────────────
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

{{-- ══════════════════════════════════════════════════════════════════════
     HEADER
     ══════════════════════════════════════════════════════════════════════ --}}
<header
    x-data="{
        mobile: false,
        scrolled: {{ $overHero ? 'false' : 'true' }},
        /* Bilah dipaksa solid juga saat laci mobile terbuka: laci berlatar
           putih, dan tanpa ini bilah di atasnya tetap transparan sehingga
           tepi keduanya tidak menyambung. */
        get solid() { return this.scrolled || this.mobile }
    }"
    @if($overHero)
        x-init="scrolled = window.scrollY > 40"
        x-on:scroll.window="scrolled = window.scrollY > 40"
    @endif
    x-bind:class="solid
        ? 'border-line bg-canvas/90 backdrop-blur'
        : 'border-transparent bg-transparent'"
    class="{{ $overHero ? 'fixed' : 'sticky' }} top-0 z-50 w-full border-b transition-colors duration-300">

    <div class="shell grid h-[76px] grid-cols-[1fr_auto] items-center gap-6 lg:grid-cols-[1fr_auto_1fr]">

        {{-- ── KIRI: logo + nama perusahaan ──────────────────────────────── --}}
        <a href="{{ route('home') }}" class="flex min-w-0 shrink items-center gap-2.5" aria-label="{{ $companyName }}">
            @if($logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                     x-bind:class="solid ? '' : 'brightness-0 invert'"
                     class="h-9 w-auto shrink-0 object-contain transition-[filter]">
            @else
                <svg x-bind:class="solid ? 'text-brand' : 'text-white'"
                     class="h-8 w-8 shrink-0 transition-colors" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <path d="M16 3.5c6 0 10.5 5.6 10.5 12.5S22 28.5 16 28.5 5.5 22.9 5.5 16 10 3.5 16 3.5Z"
                          stroke="currentColor" stroke-width="2"/>
                    <path d="M16 5.2c-3 3-3 6.9 0 10.8s3 7.8 0 10.8"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            @endif

            <span x-bind:class="solid ? 'text-ink' : 'text-white'"
                  class="truncate font-display text-[18px] font-extrabold tracking-[-0.02em] transition-colors">
                {{ $companyName }}
            </span>
        </a>

        {{-- ── TENGAH: menu utama ────────────────────────────────────────── --}}
        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('site.nav_primary') }}">
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
                                x-bind:class="solid
                                    ? '{{ $item['active'] ? 'bg-brand/10 text-brand' : 'text-ink-muted hover:text-ink' }}'
                                    : '{{ $item['active'] ? 'bg-white/20 text-white' : 'text-white/75 hover:text-white' }}'"
                                class="nav-pill flex items-center gap-1.5">
                            {{ $item['label'] }}
                            <svg class="h-3 w-3 transition-transform" x-bind:class="open && 'rotate-180'"
                                 viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                             class="absolute left-1/2 top-full w-56 -translate-x-1/2 pt-3">
                            <div class="rounded-control border border-line bg-canvas py-1.5 shadow-[0_16px_40px_-16px_rgba(26,29,27,0.28)]">
                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}"
                                       class="block px-4 py-2.5 text-[14px] font-semibold transition-colors hover:bg-mist
                                              {{ $child['active'] ? 'text-brand' : 'text-ink-muted hover:text-ink' }}">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}"
                       x-bind:class="solid
                           ? '{{ $item['active'] ? 'bg-brand/10 text-brand' : 'text-ink-muted hover:text-ink' }}'
                           : '{{ $item['active'] ? 'bg-white/20 text-white' : 'text-white/75 hover:text-white' }}'"
                       class="nav-pill">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- ── KANAN: penukar bahasa + ajakan utama ──────────────────────── --}}
        <div class="flex items-center justify-end gap-2.5">

            <div x-data="{ open: false }"
                 x-on:mouseleave="open = false"
                 x-on:keydown.escape.window="open = false"
                 class="relative hidden sm:block">

                <button type="button"
                        x-on:click="open = !open"
                        x-bind:aria-expanded="open ? 'true' : 'false'"
                        x-bind:class="solid
                            ? 'border-line text-ink-muted hover:border-line-strong hover:text-ink'
                            : 'border-white/30 text-white hover:border-white/60 hover:bg-white/10'"
                        class="flex items-center gap-1.5 rounded-full border px-3 py-2 text-[13px] font-bold transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M1.8 8h12.4M8 1.8c1.7 1.8 2.6 4 2.6 6.2S9.7 12.4 8 14.2C6.3 12.4 5.4 10.2 5.4 8S6.3 3.6 8 1.8Z"
                              stroke="currentColor" stroke-width="1.3"/>
                    </svg>
                    {{ $localeLabels[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}
                    <svg class="h-3 w-3 transition-transform" x-bind:class="open && 'rotate-180'"
                         viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                     class="absolute right-0 top-full w-44 pt-2.5">
                    <div class="rounded-control border border-line bg-canvas py-1.5 shadow-[0_16px_40px_-16px_rgba(26,29,27,0.28)]">
                        @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                            <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                               rel="alternate" hreflang="{{ $code }}"
                               class="flex items-center justify-between gap-3 px-4 py-2.5 text-[14px] font-semibold transition-colors hover:bg-mist
                                      {{ app()->getLocale() === $code ? 'text-brand' : 'text-ink-muted hover:text-ink' }}">
                                {{ $props['native'] }}
                                <span class="text-[11px] tracking-[0.08em] text-ink-faint">
                                    {{ $localeLabels[$code] ?? strtoupper($code) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="{{ route('inquiry.index') }}"
               class="btn-pill btn-pill-brand hidden py-1 pl-5 pr-1 text-[13px]
                      [&>span]:h-8 [&>span]:w-8 sm:inline-flex">
                {{ __('site.cta_request_quote') }}
                <span>
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>

            {{-- Tombol laci mobile --}}
            <button type="button"
                    x-on:click="mobile = !mobile"
                    x-bind:aria-expanded="mobile ? 'true' : 'false'"
                    aria-label="{{ __('site.nav_primary') }}"
                    x-bind:class="solid ? 'text-ink' : 'text-white'"
                    class="-mr-1 inline-flex h-10 w-10 items-center justify-center rounded-full transition-colors lg:hidden">
                <svg x-show="!mobile" class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                <svg x-show="mobile" x-cloak class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── LACI MOBILE ──────────────────────────────────────────────────── --}}
    <div x-show="mobile" x-cloak x-collapse class="border-t border-line bg-canvas lg:hidden">
        <nav class="shell flex flex-col py-5" aria-label="{{ __('site.nav_primary') }}">
            @foreach($navItems as $item)
                @if(!empty($item['children']))
                    <p class="eyebrow mt-5 mb-1.5 first:mt-0">{{ $item['label'] }}</p>
                    @foreach($item['children'] as $child)
                        <a href="{{ $child['url'] }}"
                           class="py-2.5 text-[15px] font-semibold {{ $child['active'] ? 'text-brand' : 'text-ink-muted' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ $item['url'] }}"
                       class="border-b border-line py-3 text-[15px] font-semibold {{ $item['active'] ? 'text-brand' : 'text-ink-muted' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            <div class="mt-6 flex flex-wrap items-center gap-2">
                <a href="{{ route('inquiry.index') }}" class="btn btn-brand btn-sm">
                    {{ __('site.cta_request_quote') }}
                </a>
                @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                    @if(app()->getLocale() !== $code)
                        <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                           rel="alternate" hreflang="{{ $code }}"
                           class="btn btn-outline btn-sm">
                            {{ $props['native'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>
    </div>
</header>
