@props([
    'companyName' => '',
    'settings' => [],
    'staticPages' => null,
])

@php
    $isiFooter = \App\Support\IsiHalaman::untuk('footer');

    $address  = $settings['company_address'] ?? '';
    $whatsapp = $settings['whatsapp_number'] ?? '';
    $email    = $settings['contact_email'] ?? $settings['company_email'] ?? '';

    $socials = array_values(array_filter([
        !empty($settings['linkedin_url'])  ? ['label' => 'LinkedIn',  'icon' => 'linkedin',  'url' => $settings['linkedin_url']]  : null,
        !empty($settings['instagram_url']) ? ['label' => 'Instagram', 'icon' => 'instagram', 'url' => $settings['instagram_url']] : null,
        !empty($settings['facebook_url'])  ? ['label' => 'Facebook',  'icon' => 'facebook',  'url' => $settings['facebook_url']]  : null,
    ]));

    $jamKerja = ($settings['hours_weekly'] ?? '') ?: ($settings['hours_weekday'] ?? '');

    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;

    $navigation = [
        ['label' => __('site.nav_home'),           'url' => route('home')],
        ['label' => __('site.nav_about'),          'url' => route('about')],
        ['label' => __('site.nav_products'),       'url' => route('products.index')],
        ['label' => __('site.nav_export_markets'), 'url' => route('export-markets.index')],
        ['label' => __('site.nav_news'),           'url' => route('news.index')],
    ];

    $resources = [
        ['label' => __('site.nav_certifications'), 'url' => route('certifications.index')],
        ['label' => __('site.nav_gallery'),        'url' => route('gallery.index')],
        ['label' => __('site.nav_downloads'),      'url' => route('downloads.index')],
        ['label' => __('site.nav_contact'),        'url' => route('inquiry.index')],
    ];

    $keping = 'inline-flex h-10 w-10 shrink-0 items-center justify-center '
            . 'rounded-full bg-site-gilt text-site-forest';

    $tautanKaki = 'text-site-small transition-colors hover:text-white';
@endphp

<footer class="mt-auto bg-site-forest text-white/70">
    <div class="shell py-14 lg:py-16">

        <div class="grid gap-x-8 gap-y-5 lg:grid-cols-12">

            <p class="display display-invert max-w-[20ch] text-site-h2 lg:col-span-7">
                {!! \App\Support\Judul::sorot($isiFooter('headline', 'site.footer_headline')) !!}
            </p>

            <p class="max-w-[42ch] leading-relaxed text-white/85 text-site-body
                      lg:col-span-4 lg:col-start-9 lg:self-end lg:pb-1.5">
                {{ $isiFooter('body', 'site.footer_body') }}
            </p>
        </div>

        <div class="mt-11 grid gap-x-8 gap-y-12 border-t border-white/12 pt-12
                    sm:grid-cols-2 lg:mt-12 lg:grid-cols-12">

            <dl class="space-y-5 sm:col-span-2 lg:col-span-6">

                @if($address)
                    <div class="flex items-start gap-4">
                        <span class="{{ $keping }}" aria-hidden="true">
                            <x-icon.contact name="location" />
                        </span>

                        <div class="min-w-0">
                            <dt class="eyebrow eyebrow-invert">{{ __('site.label_location') }}</dt>
                            <dd class="mt-2 max-w-[44ch] leading-relaxed text-white/85 text-site-small">{{ $address }}</dd>
                        </div>
                    </div>
                @endif

                @if($email)
                    <div class="flex items-start gap-4">
                        <span class="{{ $keping }}" aria-hidden="true">
                            <x-icon.contact name="email" />
                        </span>

                        <div class="min-w-0">
                            <dt class="eyebrow eyebrow-invert">{{ __('site.field_email') }}</dt>
                            <dd class="mt-2 text-white/85 text-site-small">
                                <a href="mailto:{{ $email }}" class="break-words transition-colors hover:text-white">{{ $email }}</a>
                            </dd>
                        </div>
                    </div>
                @endif

                @if($waLink)
                    <div class="flex items-start gap-4">
                        <span class="{{ $keping }}" aria-hidden="true">
                            <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                        </span>

                        <div class="min-w-0">
                            <dt class="eyebrow eyebrow-invert">{{ __('site.label_whatsapp') }}</dt>
                            <dd class="mt-2 text-white/85 text-site-small">
                                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                   class="transition-colors hover:text-white">{{ $whatsapp }}</a>
                            </dd>
                        </div>
                    </div>
                @endif

                @if($jamKerja)
                    <div class="flex items-start gap-4">
                        <span class="{{ $keping }}" aria-hidden="true">
                            <x-icon.contact name="clock" />
                        </span>

                        <div class="min-w-0">
                            <dt class="eyebrow eyebrow-invert">{{ __('site.label_open_hours') }}</dt>
                            <dd class="mt-2 text-white/85 text-site-small">
                                {{ __('site.hours_week_label') }} · {{ $jamKerja }}
                            </dd>
                        </div>
                    </div>
                @endif
            </dl>

            <nav aria-label="{{ __('site.footer_navigation') }}" class="lg:col-span-2">
                <p class="eyebrow eyebrow-invert">{{ __('site.footer_navigation') }}</p>
                <ul class="mt-5 space-y-3">
                    @foreach($navigation as $item)
                        <li><a href="{{ $item['url'] }}" class="{{ $tautanKaki }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <nav aria-label="{{ __('site.footer_resources') }}" class="lg:col-span-2">
                <p class="eyebrow eyebrow-invert">{{ __('site.footer_resources') }}</p>
                <ul class="mt-5 space-y-3">
                    @foreach($resources as $item)
                        <li><a href="{{ $item['url'] }}" class="{{ $tautanKaki }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            @if(!empty($socials))
                <div class="sm:col-span-2 lg:col-span-2">
                    <p class="eyebrow eyebrow-invert">{{ __('site.label_social_media') }}</p>

                    <ul class="mt-4 flex flex-wrap items-center gap-2.5">
                        @foreach($socials as $social)
                            <li>
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                   aria-label="{{ $social['label'] }}"
                                   class="{{ $keping }} transition-colors duration-300 hover:bg-site-gilt-soft">
                                    <x-icon.social :name="$social['icon']" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="border-t border-white/12">
        <div class="shell flex flex-col items-center gap-3 py-6 text-center
                    md:flex-row md:justify-between md:text-left">

            <p class="text-site-small text-white/70">
                &copy; {{ date('Y') }} {{ $companyName }}. {{ __('site.footer_rights') }}
            </p>

            @if($staticPages)
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                    @foreach($staticPages as $page)
                        <a href="{{ route('page.show', $page->slug) }}"
                           class="text-site-small text-white/70 transition-colors hover:text-white">
                            {{ $page->translated_title }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</footer>
