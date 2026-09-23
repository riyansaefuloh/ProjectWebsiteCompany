@php
    
    $ruas = request()->segment(1);

    if (array_key_exists((string) $ruas, config('laravellocalization.supportedLocales', []))) {
        app()->setLocale($ruas);
    }

    $beranda = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'));

    $jalanPintas = [
        ['label' => __('site.nav_products'),       'url' => route('products.index')],
        ['label' => __('site.nav_export_markets'), 'url' => route('export-markets.index')],
        ['label' => __('site.nav_news'),           'url' => route('news.index')],
        ['label' => __('site.nav_certifications'), 'url' => route('certifications.index')],
        ['label' => __('site.nav_downloads'),      'url' => route('downloads.index')],
        ['label' => __('site.nav_contact'),        'url' => route('inquiry.index')],
    ];

    \Artesaos\SEOTools\Facades\SEOMeta::setTitle(__('site.error_404_eyebrow') . ' - ' . config('app.name'));
@endphp

<x-layouts.public>
    <section class="pb-20 pt-14 md:pt-16 lg:pb-28 lg:pt-20">
        <div class="shell">
            <div class="mx-auto max-w-[44rem] text-center">

                <p aria-hidden="true" data-hias
                   class="select-none font-site-display font-bold leading-none tracking-[-0.04em]
                          text-ink/[0.07] text-[clamp(88px,17vw,152px)]">
                    404
                </p>

                <p class="eyebrow mt-4">{{ __('site.error_404_eyebrow') }}</p>

                <h1 class="display mx-auto mt-5 max-w-[22ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.error_404_title')) !!}
                </h1>

                <p class="lede mx-auto mt-5 max-w-[54ch] text-site-body">
                    {{ __('site.error_404_body') }}
                </p>

                <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ $beranda }}"
                       class="group inline-flex h-10 items-center gap-3 rounded-full bg-site-forest pl-5 pr-1.5
                              font-site-body text-site-small font-semibold whitespace-nowrap text-white
                              transition-colors duration-300 hover:bg-site-brand-deep">
                        {{ __('site.error_back_home') }}
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                     bg-site-gilt text-site-forest transition-transform duration-200
                                     group-hover:translate-x-0.5">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>

                    <a href="{{ route('inquiry.index') }}"
                       class="inline-flex h-10 items-center rounded-full bg-site-paper px-6
                              font-site-body text-site-small font-semibold whitespace-nowrap text-site-forest
                              ring-1 ring-line-strong transition-colors duration-300 hover:bg-site-line">
                        {{ __('site.nav_contact') }}
                    </a>
                </div>

                <div class="mt-14 border-t border-line pt-9">
                    <p class="eyebrow">{{ __('site.error_404_links') }}</p>

                    <ul class="mt-5 flex flex-wrap items-center justify-center gap-2.5">
                        @foreach($jalanPintas as $butir)
                            <li>
                                <a href="{{ $butir['url'] }}"
                                   class="inline-flex h-9 items-center rounded-full border border-line px-4
                                          font-site-body text-site-small text-ink-muted
                                          transition-colors duration-300
                                          hover:border-line-strong hover:text-site-forest">
                                    {{ $butir['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
