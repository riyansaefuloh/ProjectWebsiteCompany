@props([
    'companyName' => '',
    'settings' => [],
    'staticPages' => null,
])

@php
    $address  = $settings['company_address'] ?? '';
    $phone    = $settings['company_phone'] ?? '';
    $whatsapp = $settings['whatsapp_number'] ?? '';
    $email    = $settings['contact_email'] ?? $settings['company_email'] ?? '';

    $socials = array_values(array_filter([
        !empty($settings['linkedin_url'])  ? ['label' => 'LinkedIn',  'icon' => 'linkedin',  'url' => $settings['linkedin_url']]  : null,
        !empty($settings['instagram_url']) ? ['label' => 'Instagram', 'icon' => 'instagram', 'url' => $settings['instagram_url']] : null,
        !empty($settings['facebook_url'])  ? ['label' => 'Facebook',  'icon' => 'facebook',  'url' => $settings['facebook_url']]  : null,
    ]));

    $hours = array_filter([
        __('site.hours_weekday_label')  => $settings['hours_weekday'] ?? null,
        __('site.hours_saturday_label') => $settings['hours_saturday'] ?? null,
        __('site.hours_sunday_label')   => $settings['hours_sunday'] ?? null,
    ]);

    $phoneIsDistinct = $phone && preg_replace('/\D+/', '', $phone) !== preg_replace('/\D+/', '', $whatsapp);

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
@endphp

{{-- ══════════════════════════════════════════════════════════════════════
     FOOTER
     ══════════════════════════════════════════════════════════════════════ --}}
<footer class="mt-auto bg-forest text-white/65">
    <div class="shell py-16 lg:py-20">

        {{-- ── Tingkat 1 ────────────────────────────────────────────────── --}}
        <div class="grid gap-x-8 gap-y-14 lg:grid-cols-12">

            <div class="lg:col-span-5">
                <p class="max-w-[13ch] font-display text-[36px] font-normal leading-[1.12] tracking-[-0.03em] text-white sm:text-[46px] lg:text-[54px]">
                    {{ __('site.footer_headline') }}
                </p>

                @if(!empty($socials))
                    <ul class="mt-10 flex flex-wrap items-center gap-3" aria-label="{{ __('site.footer_social') }}">
                        @foreach($socials as $social)
                            <li>
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                   aria-label="{{ $social['label'] }}"
                                   class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-forest-line
                                          text-white/60 transition-colors hover:border-white/45 hover:text-white">
                                    <x-icon.social :name="$social['icon']" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <dl class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:col-span-6 lg:col-start-7">

                @if($address)
                    <div>
                        <dt class="flex items-center gap-2">
                            <x-icon.contact name="location" class="shrink-0 text-brand-soft" />
                            <span class="eyebrow eyebrow-invert">{{ __('site.footer_locations') }}</span>
                        </dt>
                        <dd class="mt-4 max-w-[26ch] text-[14px] leading-relaxed text-white/80">{{ $address }}</dd>
                    </div>
                @endif

                {{-- Susunannya: alamat + email di baris atas, WhatsApp + telepon
                     di baris bawah.

                     Keduanya berpasangan menurut caranya dihubungi — yang ditulis
                     di atas, yang ditelepon di bawah. Kedua nomor pun jadi
                     sebaris, jadi panjangnya yang beda-beda tidak lagi membuat
                     kolomnya tampak miring. --}}
                @if($email)
                    <div>
                        <dt class="flex items-center gap-2">
                            <x-icon.contact name="email" class="shrink-0 text-brand-soft" />
                            <span class="eyebrow eyebrow-invert">{{ __('site.footer_email') }}</span>
                        </dt>
                        <dd class="mt-4 text-[14px] leading-relaxed text-white/80">
                            <a href="mailto:{{ $email }}" class="break-all transition-colors hover:text-white">{{ $email }}</a>
                        </dd>
                    </div>
                @endif

                @if($waLink)
                    <div>
                        <dt class="flex items-center gap-2">
                            <x-icon.whatsapp size="h-4 w-4" class="shrink-0 text-brand-soft" />
                            <span class="eyebrow eyebrow-invert">{{ __('site.footer_whatsapp') }}</span>
                        </dt>
                        <dd class="mt-4 text-[14px] leading-relaxed text-white/80">
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                               class="transition-colors hover:text-white">{{ $whatsapp }}</a>
                        </dd>
                    </div>
                @endif

                {{-- Telepon: hanya kalau angkanya beda dari WhatsApp. Nomor yang
                     sama tergambar dua kali cuma membuat pembaca mengira salah
                     satunya salah ketik. --}}
                @if($phoneIsDistinct)
                    <div>
                        <dt class="flex items-center gap-2">
                            <x-icon.contact name="phone" class="shrink-0 text-brand-soft" />
                            <span class="eyebrow eyebrow-invert">{{ __('site.footer_call_us') }}</span>
                        </dt>
                        <dd class="mt-4 text-[14px] leading-relaxed text-white/80">
                            <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                               class="transition-colors hover:text-white">{{ $phone }}</a>
                        </dd>
                    </div>
                @endif

                @if(!empty($hours))
                    <div>
                        <dt class="flex items-center gap-2">
                            <x-icon.contact name="clock" class="shrink-0 text-brand-soft" />
                            <span class="eyebrow eyebrow-invert">{{ __('site.footer_open_time') }}</span>
                        </dt>
                        <dd class="mt-4 space-y-1.5 text-[14px] leading-relaxed text-white/80">
                            @foreach($hours as $dayLabel => $range)
                                <span class="block">{{ rtrim($dayLabel, ':') }} · {{ $range }}</span>
                            @endforeach
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- ── Tingkat 2: kolom tautan ─────────────────────────────────── --}}
        <div class="mt-16 grid gap-x-8 gap-y-10 border-t border-forest-line pt-14 sm:grid-cols-2 lg:mt-20 lg:grid-cols-4">

            <nav aria-label="{{ __('site.footer_navigation') }}">
                <p class="eyebrow eyebrow-invert">{{ __('site.footer_navigation') }}</p>
                <ul class="mt-5 space-y-3.5">
                    @foreach($navigation as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="text-[14px] transition-colors hover:text-white">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <nav aria-label="{{ __('site.footer_resources') }}">
                <p class="eyebrow eyebrow-invert">{{ __('site.footer_resources') }}</p>
                <ul class="mt-5 space-y-3.5">
                    @foreach($resources as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="text-[14px] transition-colors hover:text-white">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>

    {{-- ── Tingkat 3: baris legal ──────────────────────────────────────── --}}
    <div class="border-t border-forest-line">
        <div class="shell flex flex-col items-center gap-4 py-7 text-center md:flex-row md:justify-between md:text-left">

            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 md:flex-1 md:justify-start">
                @if($staticPages)
                    @foreach($staticPages as $page)
                        <a href="{{ route('page.show', $page->slug) }}"
                           class="text-[13px] text-white/50 transition-colors hover:text-white/85">
                            {{ $page->translated_title }}
                        </a>
                    @endforeach
                @endif
            </div>

            <p class="order-last text-[13px] text-white/40 md:order-none">
                &copy; {{ date('Y') }} {{ $companyName }}. {{ __('site.footer_rights') }}
            </p>

            <div class="hidden md:block md:flex-1" aria-hidden="true"></div>
        </div>
    </div>
</footer>
