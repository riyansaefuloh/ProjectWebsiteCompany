@php
    $address  = $settings['company_address'] ?? '';
    /* Urutan kuncinya disamakan dengan kaki situs. Sebelumnya halaman ini
       mendahulukan company_email sedangkan kaki situs contact_email, jadi satu
       layar bisa memuat dua alamat yang berbeda. */
    $email    = $settings['contact_email'] ?? $settings['company_email'] ?? '';
    $whatsapp = $settings['whatsapp_number'] ?? '';
    $phone    = $settings['company_phone'] ?? '';
    $mapUrl   = $settings['google_map_url'] ?? '';

    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;

    $phoneIsDistinct = $phone && preg_replace('/\D+/', '', $phone) !== preg_replace('/\D+/', '', $whatsapp);

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

    $recaptchaSiteKey = env('RECAPTCHA_SITE_KEY');
@endphp

<div>
    <section class="pb-20 pt-12 md:pt-16 lg:pb-24 lg:pt-20">
        <div class="shell">

            {{-- ══════════════════════════════════════════════════════════ --}}
            <div class="grid items-start gap-6 lg:grid-cols-12 lg:gap-8">

                {{-- ── KIRI: kartu putih ─────────────────────────────────── --}}
                <div class="lg:sticky lg:top-[92px] lg:col-span-5">
                    <div class="card p-8 sm:p-10">

                        <h1 class="max-w-[16ch] display text-[28px] sm:text-[32px]">
                            {{ __('site.inquiry_headline') }}
                        </h1>

                        <p class="lede mt-5 max-w-[42ch] text-[14px]">
                            {{ __('site.inquiry_intro') }}
                        </p>

                        <dl class="mt-10 grid gap-x-6 gap-y-8 sm:grid-cols-2">

                            @if($address)
                                <div>
                                    <dt class="flex items-center gap-2">
                                        <x-icon.contact name="location" class="shrink-0 text-brand" />
                                        <span class="eyebrow">{{ __('site.label_location') }}</span>
                                    </dt>
                                    <dd class="mt-3.5 max-w-[26ch] text-[13px] leading-relaxed text-ink-muted">{{ $address }}</dd>
                                </div>
                            @endif

                            {{-- Susunannya menirukan kaki situs: alamat + email di baris
                                 atas, WhatsApp + telepon di baris bawah.

                                 Keduanya berpasangan menurut caranya dihubungi — yang
                                 ditulis di atas, yang ditelepon di bawah. Kedua nomor
                                 pun jadi sebaris, jadi panjangnya yang beda-beda tidak
                                 lagi membuat kolomnya tampak miring. --}}
                            @if($email)
                                <div>
                                    <dt class="flex items-center gap-2">
                                        <x-icon.contact name="email" class="shrink-0 text-brand" />
                                        <span class="eyebrow">{{ __('site.field_email') }}</span>
                                    </dt>
                                    <dd class="mt-3.5 text-[13px] leading-relaxed">
                                        <a href="mailto:{{ $email }}" class="break-all text-ink-muted transition-colors hover:text-brand">{{ $email }}</a>
                                    </dd>
                                </div>
                            @endif

                            @if($waLink)
                                <div>
                                    <dt class="flex items-center gap-2">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0 text-brand" />
                                        <span class="eyebrow">{{ __('site.label_whatsapp') }}</span>
                                    </dt>
                                    <dd class="mt-3.5 text-[13px] leading-relaxed text-ink-muted">
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                           class="transition-colors hover:text-brand">{{ $whatsapp }}</a>
                                    </dd>
                                </div>
                            @endif

                            {{-- Telepon: hanya kalau angkanya beda dari WhatsApp. Nomor
                                 yang sama tergambar dua kali cuma membuat pembaca
                                 mengira salah satunya salah ketik. --}}
                            @if($phoneIsDistinct)
                                <div>
                                    <dt class="flex items-center gap-2">
                                        <x-icon.contact name="phone" class="shrink-0 text-brand" />
                                        <span class="eyebrow">{{ __('site.label_phone') }}</span>
                                    </dt>
                                    <dd class="mt-3.5 text-[13px] leading-relaxed text-ink-muted">
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                                           class="transition-colors hover:text-brand">{{ $phone }}</a>
                                    </dd>
                                </div>
                            @endif

                            @if(!empty($hours))
                                <div class="sm:col-span-2">
                                    <dt class="flex items-center gap-2">
                                        <x-icon.contact name="clock" class="shrink-0 text-brand" />
                                        <span class="eyebrow">{{ __('site.label_open_hours') }}</span>
                                    </dt>
                                    <dd class="mt-3.5 space-y-1.5 text-[13px] leading-relaxed text-ink-muted">
                                        @foreach($hours as $dayLabel => $range)
                                            <span class="block">{{ rtrim($dayLabel, ':') }} · {{ $range }}</span>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif

                            {{-- Media sosial turun ke bawah supaya empat blok kontak di
                                 atasnya bisa berpasangan dua-dua seperti di kaki situs. --}}
                            @if(!empty($socials))
                                <div class="sm:col-span-2">
                                    <dt class="eyebrow">{{ __('site.label_social_media') }}</dt>
                                    <dd class="mt-3.5">
                                        <ul class="flex flex-wrap gap-2.5">
                                            @foreach($socials as $social)
                                                <li>
                                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                                       aria-label="{{ $social['label'] }}"
                                                       class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line
                                                              text-ink-muted transition-colors hover:border-brand hover:text-brand">
                                                        <x-icon.social :name="$social['icon']" />
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- ── KANAN: formulir ────────────────────────────────────── --}}
                <div class="lg:col-span-7">
                    <div class="rounded-panel bg-forest p-7 sm:p-9">

                        @if($isSubmitted)
                            {{-- ── Keadaan terkirim ────────────────────────── --}}
                            <div class="py-10 text-center">
                                <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-white/10 text-brand-soft">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>

                                <h2 class="mt-7 font-display text-[22px] font-extrabold tracking-[-0.02em] text-white sm:text-[26px]">
                                    {{ __('site.inquiry_success') }}
                                </h2>
                                <p class="mx-auto mt-4 max-w-[42ch] text-[15px] leading-relaxed text-white/70">
                                    {{ __('site.inquiry_thank_you') }}
                                </p>

                                @if($whatsappUrl)
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="btn mt-8 bg-[#25D366] text-white transition-transform hover:scale-[1.03]">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                        {{ __('site.cta_whatsapp') }}
                                    </a>
                                @endif

                                <p class="mt-8">
                                    <button type="button" wire:click="$set('isSubmitted', false)"
                                            class="text-[13px] font-bold text-brand-soft underline underline-offset-4 transition-colors hover:text-white">
                                        {{ __('site.send_another') }}
                                    </button>
                                </p>
                            </div>
                        @else
                            <h2 class="font-display text-[20px] font-extrabold tracking-[-0.02em] text-white sm:text-[23px]">{{ __('site.inquiry_form_title') }}</h2>
                            <p class="mt-2.5 text-[13px] text-white/60">{{ __('site.inquiry_form_intro') }}</p>

                            <form wire:submit.prevent="executeRecaptcha" class="mt-8">

                                <div class="hidden" aria-hidden="true">
                                    <label for="website_hp">Website</label>
                                    <input id="website_hp" type="text" wire:model="website_hp" tabindex="-1" autocomplete="off">
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">

                                    <div class="sm:col-span-2">
                                        <label for="f-name" class="field-label text-white/60">{{ __('site.field_name') }} *</label>
                                        <input id="f-name" type="text" wire:model="name" required
                                               placeholder="{{ __('site.field_name_ph') }}"
                                               class="field @error('name') border-danger @enderror">
                                        @error('name') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-company" class="field-label text-white/60">{{ __('site.field_company') }} *</label>
                                        <input id="f-company" type="text" wire:model="company" required
                                               placeholder="{{ __('site.field_company_ph') }}"
                                               class="field @error('company') border-danger @enderror">
                                        @error('company') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-country" class="field-label text-white/60">{{ __('site.field_country') }} *</label>
                                        <select id="f-country" wire:model="country_code" required
                                                class="field field-select @error('country_code') border-danger @enderror">
                                            <option value="">{{ __('site.field_country_ph') }}</option>
                                            @foreach(config('countries', []) as $code => $countryName)
                                                <option value="{{ $code }}" @selected($country_code === $code)>
                                                    ({{ $code }}) {{ $countryName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_code') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-email" class="field-label text-white/60">{{ __('site.field_email') }} *</label>
                                        <input id="f-email" type="email" wire:model="email" required
                                               placeholder="{{ __('site.field_email_ph') }}"
                                               class="field @error('email') border-danger @enderror">
                                        @error('email') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-phone" class="field-label text-white/60">{{ __('site.field_phone') }}</label>
                                        <input id="f-phone" type="tel" wire:model="phone"
                                               placeholder="{{ __('site.field_phone_ph') }}" class="field">
                                        @error('phone') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="f-product" class="field-label text-white/60">{{ __('site.field_product') }}</label>
                                        <select id="f-product" wire:model="product_id" class="field field-select">
                                            <option value="">{{ __('site.field_product_ph') }}</option>
                                            @foreach($products ?? [] as $product)
                                                <option value="{{ $product->id }}" @selected($product_id === $product->id)>
                                                    {{ $product->translated_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_id') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-volume" class="field-label text-white/60">{{ __('site.field_volume') }}</label>
                                        <input id="f-volume" type="text" wire:model="volume"
                                               placeholder="{{ __('site.field_volume_ph') }}" class="field">
                                        @error('volume') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-incoterms" class="field-label text-white/60">{{ __('site.field_incoterms') }}</label>
                                        <select id="f-incoterms" wire:model="incoterms" class="field field-select">
                                            <option value="">{{ __('site.field_incoterms_ph') }}</option>
                                            <option value="FOB" @selected($incoterms === 'FOB')>FOB (Free On Board)</option>
                                            <option value="CIF" @selected($incoterms === 'CIF')>CIF (Cost, Insurance &amp; Freight)</option>
                                            <option value="EXW" @selected($incoterms === 'EXW')>EXW (Ex Works)</option>
                                        </select>
                                        @error('incoterms') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="f-message" class="field-label text-white/60">{{ __('site.field_message') }} *</label>
                                        <textarea id="f-message" wire:model="message" rows="6" required
                                                  placeholder="{{ __('site.field_message_ph') }}"
                                                  class="field resize-y @error('message') border-danger @enderror"></textarea>
                                        @error('message') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-invert mt-8 w-full"
                                        wire:loading.attr="disabled" wire:target="executeRecaptcha, submit">
                                    <span wire:loading.remove wire:target="executeRecaptcha, submit">{{ __('site.btn_submit') }}</span>
                                    <span wire:loading wire:target="executeRecaptcha, submit">{{ __('site.btn_processing') }}</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Peta lokasi ────────────────────────────────────────────── --}}
            @if($mapUrl)
                <div class="mt-6">
                    <p class="eyebrow">{{ __('site.find_us') }}</p>
                    <div class="mt-4 overflow-hidden rounded-panel border border-line">
                        <iframe src="{{ $mapUrl }}" title="{{ __('site.find_us') }}"
                                class="block h-[320px] w-full border-0 sm:h-[420px]"
                                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($recaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
    @endif

    <div x-data="{ siteKey: @js($recaptchaSiteKey) }"
         x-on:request-recaptcha.window="
            if (siteKey && typeof grecaptcha !== 'undefined') {
                grecaptcha.ready(() => {
                    grecaptcha.execute(siteKey, { action: 'inquiry' })
                        .then((token) => $wire.submit(token));
                });
            } else {
                /* Tanpa kunci di .env — keadaan pengembangan lokal saat ini —
                   langkah tokennya dilewati dan formulir tetap bisa diuji.
                   Server juga melewati verifikasinya bila RECAPTCHA_SECRET_KEY
                   kosong, jadi keduanya sepakat. */
                $wire.submit('dummy-token-for-local-testing');
            }
         "></div>
</div>
