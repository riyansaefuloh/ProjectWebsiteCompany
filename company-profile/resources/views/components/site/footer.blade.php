@props([
    'companyName' => '',
    'settings' => [],
    'staticPages' => null,
])

@php
    /* Ajakan besar di kepala footer bisa ditulis sendiri dari menu Halaman. */
    $isiFooter = \App\Support\IsiHalaman::untuk('footer');

    $address  = $settings['company_address'] ?? '';
    $whatsapp = $settings['whatsapp_number'] ?? '';
    $email    = $settings['contact_email'] ?? $settings['company_email'] ?? '';

    /* Lambang sosial mengikuti yang BENAR-BENAR diisi di Pengaturan; yang kosong
       tidak digambar sama sekali. */
    $socials = array_values(array_filter([
        !empty($settings['linkedin_url'])  ? ['label' => 'LinkedIn',  'icon' => 'linkedin',  'url' => $settings['linkedin_url']]  : null,
        !empty($settings['instagram_url']) ? ['label' => 'Instagram', 'icon' => 'instagram', 'url' => $settings['instagram_url']] : null,
        !empty($settings['facebook_url'])  ? ['label' => 'Facebook',  'icon' => 'facebook',  'url' => $settings['facebook_url']]  : null,
    ]));

    /* Satu rentang untuk Senin–Sabtu. Kunci lama tetap dibaca sebagai cadangan
       supaya jam yang belum sempat disimpan ulang dari panel tidak hilang. */
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

    /* Keping ikon: bulat emas berlambang forest, rupa yang sama dengan blok
       kontak di halaman Contact Us. Lambangnya gelap, bukan putih — putih di
       atas emas cuma 2,10:1, terbalik ia 6,99:1. */
    $keping = 'inline-flex h-10 w-10 shrink-0 items-center justify-center '
            . 'rounded-full bg-site-gilt text-site-forest';

    $tautanKaki = 'text-site-small transition-colors hover:text-white';
@endphp

{{-- ══════════════════════════════════════════════════════════════════════
     FOOTER

     Susunannya SATU pembacaan dari kiri ke kanan, bukan dua kisi bertumpuk.

     Yang sebelumnya di sini: ajakan + lambang sosial di kiri, kontak 2×2 di
     kanan, lalu kisi kedua berisi tautan + keterangan di bawahnya. Tiga
     akibatnya — lambang sosial terpisah jauh dari kontak yang ia lanjutkan,
     keterangan terpisah jauh dari ajakan yang ia lanjutkan, dan alamat empat
     baris di petak 2×2 meninggalkan lubang 150px di bawah WhatsApp.

     Sekarang dua kalimat panjang berbagi satu pita di puncak, dan di bawah
     satu garis semua yang berupa daftar berdiri sebaris: kontak, navigasi,
     sumber daya, sosial.
     ══════════════════════════════════════════════════════════════════════ --}}
<footer class="mt-auto bg-site-forest text-white/70">
    <div class="shell py-14 lg:py-16">

        {{-- ── Pita ajakan ────────────────────────────────────────────────
             Keterangannya rata bawah terhadap ajakan, jadi keduanya berbagi
             satu garis dasar alih-alih mengambang di ketinggian berbeda. --}}
        <div class="grid gap-x-8 gap-y-5 lg:grid-cols-12">

            {{-- Tanpa lambang dan nama perusahaan: keduanya sudah berdiri di
                 bilah kepala sepanjang gulir. --}}
            <p class="display display-invert max-w-[20ch] text-site-h2 lg:col-span-7">
                {!! \App\Support\Judul::sorot($isiFooter('headline', 'site.footer_headline')) !!}
            </p>

            <p class="max-w-[42ch] leading-relaxed text-white/85 text-site-body
                      lg:col-span-4 lg:col-start-9 lg:self-end lg:pb-1.5">
                {{ $isiFooter('body', 'site.footer_body') }}
            </p>
        </div>

        {{-- ── Empat lajur daftar ─────────────────────────────────────────
             Pembagiannya 6·2·2·2, habis sampai tepi. Kontak dapat separuh
             karena isinya kalimat, bukan sepatah tautan: dengan enam lajur
             alamat berhenti di dua baris alih-alih empat, dan itulah yang dulu
             merusak barisnya. Tiga lajur tautan cukup dua lajur masing-masing —
             yang terpanjang, "Export Markets", masih muat sebaris. --}}
        <div class="mt-11 grid gap-x-8 gap-y-12 border-t border-white/12 pt-12
                    sm:grid-cols-2 lg:mt-12 lg:grid-cols-12">

            {{-- Kontak. Tanpa judul lajur sendiri: label tiap barisnya sudah
                 sejajar dengan judul lajur di sebelahnya, dan judul kedua di
                 atas empat label bergaya sama cuma menumpuk tingkat. --}}
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

                {{-- WhatsApp saja, tanpa nomor telepon: keduanya mengantar ke
                     kantor yang sama, dan yang bisa langsung dibuka dari ponsel
                     maupun komputer cuma yang ini. --}}
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

    {{-- ── Bilah legal ────────────────────────────────────────────────────
         Hak cipta di kiri, tautan di kanan. Yang lama menaruhnya di tengah
         lewat petak kosong berpenyeimbang di sisi kiri — satu unsur yang tidak
         berisi apa-apa dan cuma ada untuk mendorong. --}}
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
