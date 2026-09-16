@php
    $address  = $settings['company_address'] ?? '';
    /* Urutan kuncinya disamakan dengan kaki situs. Sebelumnya halaman ini
       mendahulukan company_email sedangkan kaki situs contact_email, jadi satu
       layar bisa memuat dua alamat yang berbeda. */
    $email    = $settings['contact_email'] ?? $settings['company_email'] ?? '';
    $whatsapp = $settings['whatsapp_number'] ?? '';
    /*
     * Tautan peta DIUBAH jadi alamat yang bisa disemat.
     *
     * Yang disalin orang dari Google Maps hampir selalu tautan berbagi
     * ("maps.app.goo.gl/…") atau alamat panjang dari bilah alamat. Keduanya
     * menolak disemat — Google mengirim X-Frame-Options pada halaman petanya —
     * jadi iframe-nya tergambar KOSONG tanpa satu pesan galat pun, dan yang
     * mengisinya di panel mengira tautannya tidak tersimpan.
     *
     * Alamat perusahaan dipakai sebagai cadangan: tautan pendek tidak memuat
     * koordinat apa pun, dan membukanya dari sisi peladen berarti satu
     * permintaan jaringan tiap kali halaman digambar.
     */
    $mapTautan = $settings['google_map_url'] ?? '';
    $mapUrl    = \App\Support\Peta::sematan($mapTautan, $address);

    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;


    $socials = array_values(array_filter([
        !empty($settings['linkedin_url'])  ? ['label' => 'LinkedIn',  'icon' => 'linkedin',  'url' => $settings['linkedin_url']]  : null,
        !empty($settings['instagram_url']) ? ['label' => 'Instagram', 'icon' => 'instagram', 'url' => $settings['instagram_url']] : null,
        !empty($settings['facebook_url'])  ? ['label' => 'Facebook',  'icon' => 'facebook',  'url' => $settings['facebook_url']]  : null,
    ]));

    /* Satu rentang untuk Senin–Sabtu. Kunci lama tetap dibaca sebagai cadangan
       supaya jam yang belum sempat disimpan ulang dari panel tidak hilang. */
    $jamKerja = ($settings['hours_weekly'] ?? '') ?: ($settings['hours_weekday'] ?? '');

    $hours = array_filter([
        __('site.hours_week_label') => $jamKerja ?: null,
    ]);

    $recaptchaSiteKey = env('RECAPTCHA_SITE_KEY');
@endphp

<div>
    <section class="pb-20 pt-12 md:pt-16 lg:pb-24 lg:pt-20">
        <div class="shell">

            {{-- ══════════════════════════════════════════════════════════ --}}
            {{-- items-stretch, bukan items-start: kedua lajur meregang ke
                 tinggi yang sama, dan panel kiri mengisi tinggi itu dengan
                 menyebar isinya — bukan menumpuk di puncak lalu meninggalkan
                 324px kosong di bawahnya.

                 Jaraknya dilebarkan jadi 48px di layar besar: dua panel yang
                 kini sama tinggi butuh sela yang jelas supaya tidak terbaca
                 sebagai satu bidang terbelah. --}}
            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">

                {{-- ── KIRI: keterangan, TANPA kartu ──────────────────────
                     Kartu di sini mengurung kepala halaman — judul, keterangan,
                     dan blok kontaknya — di dalam satu bidang bergaris, dan yang
                     dihasilkan bukan kejelasan melainkan sebaliknya: judul
                     halaman ini jadi terbaca sebagai judul KARTU, sederajat
                     dengan judul formulir di sebelahnya. Padahal yang satu nama
                     halaman dan yang satu nama borang di dalamnya.

                     Tanpa kartu, judulnya berdiri di bidang halaman seperti di
                     enam halaman publik lain, dan formulir gelap di kanan jadi
                     satu-satunya bidang di layar ini — yang memang benar, karena
                     ia satu-satunya yang meminta tindakan. --}}
                {{-- lg:sticky DILEPAS. Melekat di puncak layar cuma berguna
                     kalau isinya lebih pendek daripada yang di sebelahnya dan
                     perlu tetap terlihat saat yang panjang digulir — tapi begitu
                     kedua lajur sama tinggi, tidak ada lagi yang tertinggal untuk
                     dilekatkan. --}}
                <div class="flex flex-col lg:col-span-5">
                    <div class="flex h-full flex-col">
                        @php $kontakIntro = $isi('intro', 'site.inquiry_intro'); @endphp

                        {{-- Label kecil di atas judul, seperti enam halaman publik
                             lain. Halaman ini satu-satunya yang tidak punya, jadi
                             kepalanya langsung dimulai dari judul tanpa penanda
                             golongan apa pun. --}}
                        <p class="eyebrow">{{ $isi('eyebrow', 'site.nav_contact') }}</p>

                        {{-- 42px cokelat, ukuran dan warna yang sama dengan kepala
                             halaman lain. Yang lama 48px hitam — ukuran hero. --}}
                        <h1 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                            {!! \App\Support\Judul::sorot($isi('headline', 'site.inquiry_headline')) !!}
                        </h1>

                        {{-- Isian ini ditulis lewat penyunting teks kaya, jadi
                             isinya bisa mengandung tag. Yang lama menggambarnya
                             dengan {{ }} — tag-nya akan ikut TERCETAK di layar
                             sebagai "<p>…</p>" begitu ada yang menyunting kolomnya
                             lewat penyuntingnya. Yang mengandung tag digambar
                             sebagai .rich; yang polos tetap .lede. --}}
                        @if($kontakIntro !== strip_tags($kontakIntro))
                            <div class="rich mt-5 max-w-[46ch]">{!! $kontakIntro !!}</div>
                        @else
                            <p class="lede mt-5 max-w-[46ch] text-site-body">{{ $kontakIntro }}</p>
                        @endif

                        {{-- Urutan blok kontak menentukan pasangannya, karena
                             kisinya dua lajur dan mengisi baris demi baris.

                             Lokasi berpasangan dengan MEDIA SOSIAL, email dengan
                             WHATSAPP — bukan lokasi dengan email seperti
                             sebelumnya. Yang berpasangan sekarang hal yang setara:
                             dua alamat tempat di baris pertama (satu di dunia
                             nyata, satu di dunia maya), dua saluran pesan di baris
                             kedua. Jam kerja melebar penuh di bawahnya karena ia
                             menerangkan keduanya sekaligus. --}}
                        {{-- Tiap blok dibuka garis rambut di atasnya, bukan
                             mengambang bebas dengan jarak saja.

                             Empat blok yang cuma dipisah ruang terbaca sebagai
                             empat benda yang kebetilan berdekatan; garis di atas
                             tiap blok menjadikannya satu daftar berbaris — dan
                             daftar itu yang sebenarnya: satu himpunan cara
                             menghubungi perusahaan yang sama. --}}
                        {{-- Mengalir wajar sesudah keterangan, TIDAK diregangkan
                             mengisi lajurnya.

                             Dicoba dua cara meregangkannya: didorong ke dasar
                             (mt-auto) menyisakan satu lubang 256px di antara
                             keterangan dan daftarnya; disebar (content-between)
                             merenggangkan jarak antar barisnya jadi 245px. Dua-
                             duanya menukar satu masalah dengan masalah yang lebih
                             terlihat.

                             Sisa ruangnya sekarang jatuh di DASAR lajur — di bawah
                             blok terakhir, di bidang tanpa latar, tempat ia tidak
                             terbaca sebagai lubang melainkan sebagai napas sebelum
                             peta di bawahnya. --}}
                        {{-- Keping BULAT di kiri, label dan nilainya bertingkat di
                             kanannya — susunan acuan.

                             Yang berbalik dari versi sebelumnya bukan cuma letaknya:
                             LABEL yang jadi tulisan tebal gelap, dan NILAI yang
                             turun jadi nada redup di bawahnya. Sebelumnya kebalikan
                             — label kapital emas kecil, nilai 16px pekat.

                             Keduanya sah, dan yang menentukan pilihan adalah apa
                             yang dicari orang di blok ini. Yang dicari BUKAN alamat
                             surelnya, melainkan "di mana alamat surelnya" — mata
                             menyusuri label dulu sampai menemukan yang dituju, baru
                             membaca nilainya. Label yang menonjol memendekkan
                             pencarian itu; nilai yang menonjol memaksa membaca
                             keempatnya. --}}
                        <dl class="mt-10 space-y-5">

                            @php
                                /* Keping BULAT beraksen emas berlambang gelap.

                                   Bulat, bukan persegi berbulatan: keping persegi
                                   milik kartu "why choose us" berdiri sendiri di
                                   puncak kartunya; di sini ia berjajar empat ke
                                   bawah di sebelah tulisan, dan lingkaran yang
                                   berulang terbaca sebagai deretan penanda alih-alih
                                   deretan kotak.

                                   Emas berlambang forest, bukan sebaliknya: lambang
                                   putih di atas emas cuma 2,10:1. Terbalik, ia
                                   6,99:1. */
                                $keping = 'inline-flex h-10 w-10 shrink-0 items-center justify-center '
                                        . 'rounded-full bg-site-gilt text-site-forest';

                                $labelKontak = 'font-site-body text-site-body font-semibold text-site-forest';
                                $nilaiKontak = 'mt-0.5 leading-relaxed text-ink-muted text-site-small';
                            @endphp

                            @if($address)
                                <div class="flex items-start gap-4">
                                    <span class="{{ $keping }}" aria-hidden="true">
                                        <x-icon.contact name="location" />
                                    </span>

                                    <div class="min-w-0">
                                        <dt class="{{ $labelKontak }}">{{ __('site.label_location') }}</dt>
                                        <dd class="{{ $nilaiKontak }}">{{ $address }}</dd>
                                    </div>
                                </div>
                            @endif

                            @if($email)
                                <div class="flex items-start gap-4">
                                    <span class="{{ $keping }}" aria-hidden="true">
                                        <x-icon.contact name="email" />
                                    </span>

                                    <div class="min-w-0">
                                        <dt class="{{ $labelKontak }}">{{ __('site.field_email') }}</dt>
                                        <dd class="{{ $nilaiKontak }}">
                                            <a href="mailto:{{ $email }}"
                                               class="break-all transition-colors hover:text-site-gilt-deep">{{ $email }}</a>
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
                                        <dt class="{{ $labelKontak }}">{{ __('site.label_whatsapp') }}</dt>
                                        <dd class="{{ $nilaiKontak }}">
                                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                               class="transition-colors hover:text-site-gilt-deep">{{ $whatsapp }}</a>
                                        </dd>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($hours))
                                <div class="flex items-start gap-4">
                                    <span class="{{ $keping }}" aria-hidden="true">
                                        <x-icon.contact name="clock" />
                                    </span>

                                    <div class="min-w-0">
                                        <dt class="{{ $labelKontak }}">{{ __('site.label_open_hours') }}</dt>
                                        <dd class="{{ $nilaiKontak }} space-y-1">
                                            @foreach($hours as $dayLabel => $range)
                                                <span class="block">{{ rtrim($dayLabel, ':') }} · {{ $range }}</span>
                                            @endforeach
                                        </dd>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($socials))
                                {{-- Media sosial dipisah garis rambut, seperti di
                                     acuan. Ia bukan cara menghubungi satu-satu
                                     seperti empat di atasnya — ia tempat mengikuti,
                                     dan bentuk isinya pun berbeda: deretan keping,
                                     bukan sebaris tulisan.

                                     Labelnya memakai rupa yang sama dengan keempat
                                     label di atas, jadi garis itu memisahkan tanpa
                                     memutus. --}}
                                <div class="border-t border-line pt-6">
                                    <dt class="{{ $labelKontak }}">{{ __('site.label_social_media') }}</dt>

                                    <dd class="mt-3.5">
                                        <ul class="flex flex-wrap gap-2.5">
                                            @foreach($socials as $social)
                                                <li>
                                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                                       aria-label="{{ $social['label'] }}"
                                                       class="{{ $keping }} transition-colors duration-300
                                                              hover:bg-site-gilt-soft">
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

                {{-- ── KANAN: formulir ────────────────────────────────────
                     Satu-satunya bidang pejal di halaman ini, dan itu memang
                     perannya: dari seluruh isi halaman, cuma di sini pengunjung
                     diminta melakukan sesuatu.

                     Kartunya COKELAT TUA, isiannya cokelat yang lebih tua lagi —
                     dua nada gelap yang cuma terpaut 1,16:1. Selisih setipis itu
                     tidak menandai batas dengan sendirinya, dan memang tidak
                     perlu: yang menggambar tepi tiap pil adalah bulatannya, bukan
                     kontrasnya. Yang dikerjakan nada gelap kedua cuma satu —
                     menyatakan bahwa yang di dalamnya bisa diisi.

                     Warna yang mengerjakan sisanya emas: label tiap isian, dan
                     tombol di dasar borang. Di bidang segelap ini, emas
                     satu-satunya yang bisa memimpin tanpa berteriak. --}}
                <div class="lg:col-span-7">
                    <div class="rounded-panel bg-site-forest p-7 sm:p-9">

                        @if($isSubmitted)
                            {{-- ── Keadaan terkirim ────────────────────────── --}}
                            <div class="py-10 text-center">
                                <span class="inline-flex h-16 w-16 items-center justify-center rounded-full
                                             bg-site-gilt text-site-forest">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>

                                <h2 class="mt-7 font-site-display text-site-h3 font-bold tracking-[-0.02em] text-white">
                                    {{ $isi('success_title', 'site.inquiry_success') }}
                                </h2>
                                <p class="mx-auto mt-4 max-w-[42ch] leading-relaxed text-white/75 text-site-body">
                                    {{ $isi('success_body', 'site.inquiry_thank_you') }}
                                </p>

                                @if($whatsappUrl)
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="mt-8 inline-flex h-10 items-center gap-2.5 rounded-full px-5
                                              bg-site-canvas text-site-forest ring-1 ring-line-strong
                                              font-site-body text-site-small font-semibold whitespace-nowrap
                                              transition-colors duration-300 hover:bg-site-line">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                        {{ __('site.cta_whatsapp') }}
                                    </a>
                                @endif

                                <p class="mt-8">
                                    <button type="button" wire:click="$set('isSubmitted', false)"
                                            class="font-site-body text-site-small font-semibold text-site-gilt
                                                   underline decoration-site-gilt/50 underline-offset-4
                                                   transition-colors hover:text-white hover:decoration-white">
                                        {{ $isi('send_another', 'site.send_another') }}
                                    </button>
                                </p>
                            </div>
                        @else
                            @php
                                /* ── RUPA ISIAN: PIL ─────────────────────────────
                                 *
                                 * Setinggi 48px dan berbulatan penuh — bentuk yang
                                 * sudah dipakai di seluruh situs ini untuk hal yang
                                 * bisa ditekan atau diisi: pil kategori di halaman
                                 * Produk, pil negara di Pasar Ekspor, kotak cari di
                                 * bilah saring.
                                 *
                                 * Bidangnya brand-deep, satu tingkat lebih gelap
                                 * daripada kartunya. Garis tepinya putih 10% —
                                 * cukup untuk menggambar tepi pil tanpa jadi
                                 * bingkai; garis pekat di sembilan pil sekaligus
                                 * akan mengubah borang jadi kisi.
                                 *
                                 * Saat diketik, garis itu berbalik EMAS. Di bidang
                                 * gelap, menebalkan garis cokelat tidak terlihat
                                 * sama sekali — yang terlihat cuma pergantian
                                 * rona.
                                 *
                                 * Ditulis sekali lalu dipakai sembilan kali;
                                 * disalin sembilan kali, kesembilannya pasti
                                 * lama-lama berbeda. */
                                $pil = 'h-12 w-full rounded-full border border-white/10 bg-site-brand-deep px-5 '
                                     . 'font-site-body text-site-body text-white placeholder:text-white/55 '
                                     . 'transition-colors focus:border-site-gilt focus:outline-none';

                                /* Kotak pesan TIDAK bisa jadi pil: pil punya satu
                                 * garis dasar, kotak pesan punya tiga. Yang dipakai
                                 * bulatan 22px — bulatan terbesar di situs ini,
                                 * dipakai panel — jadi ia terbaca sebagai anggota
                                 * keluarga yang sama, cuma yang tidak muat
                                 * dibulatkan penuh. */
                                $kotakPesan = 'w-full resize-none rounded-[22px] border border-white/10 bg-site-brand-deep px-5 py-4 '
                                            . 'font-site-body text-site-body text-white placeholder:text-white/55 '
                                            . 'transition-colors focus:border-site-gilt focus:outline-none';

                                /* Label EMAS, bukan putih redup: di bidang segelap
                                 * ini putih redup jadi abu tak berona, dan sembilan
                                 * label abu di atas sembilan pil gelap tidak
                                 * membedakan apa pun. Emas 6,99:1 terhadap kartunya. */
                                $labelIsian = 'block font-site-accent text-site-micro font-medium uppercase '
                                            . 'tracking-[0.14em] text-site-gilt';
                            @endphp

                            <h2 class="font-site-display text-site-h3 font-bold tracking-[-0.02em] text-white">
                                {{ $isi('form_title', 'site.inquiry_form_title') }}
                            </h2>

                            {{-- Keterangan di bawah judul dihapus. Ia menyatakan
                                 bahwa isian bertanda bintang wajib diisi — hal yang
                                 sudah dinyatakan bintangnya sendiri, di tempat
                                 bintang itu berada. --}}

                            <form wire:submit.prevent="executeRecaptcha" class="mt-8">

                                <div class="hidden" aria-hidden="true">
                                    <label for="website_hp">Website</label>
                                    <input id="website_hp" type="text" wire:model="website_hp" tabindex="-1" autocomplete="off">
                                </div>

                                {{-- SATU kisi dua lajur untuk seluruh isian, kecuali
                                     kotak pesan.

                                     Judul kelompok "About you" dan "Your inquiry"
                                     dilepas: pembaginya sekarang bentuk, bukan
                                     tulisan — delapan pil berpasangan dua-dua, lalu
                                     satu kotak melebar penuh yang menutup borangnya.
                                     Judul yang menamai kelompok yang sudah terlihat
                                     cuma menambah dua baris. --}}
                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div>
                                        <label for="f-name" class="{{ $labelIsian }}">{{ __('site.field_name') }} *</label>
                                        <input id="f-name" type="text" wire:model="name" required
                                               autocomplete="name"
                                               placeholder="{{ __('site.field_name_ph') }}"
                                               class="{{ $pil }} mt-2 @error('name') border-danger @enderror">
                                        @error('name') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-company" class="{{ $labelIsian }}">{{ __('site.field_company') }} *</label>
                                        <input id="f-company" type="text" wire:model="company" required
                                               autocomplete="organization"
                                               placeholder="{{ __('site.field_company_ph') }}"
                                               class="{{ $pil }} mt-2 @error('company') border-danger @enderror">
                                        @error('company') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="pilih-country_code" class="{{ $labelIsian }}">{{ __('site.field_country') }} *</label>

                                        <x-site.pilih name="country_code" :value="$country_code" required
                                            :invalid="$errors->has('country_code')"
                                            :placeholder="__('site.field_country_ph')"
                                            :options="collect(config('countries', []))
                                                ->map(fn ($nama, $kode) => ['nilai' => $kode, 'label' => '(' . $kode . ') ' . $nama])
                                                ->values()->all()" />

                                        @error('country_code') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-email" class="{{ $labelIsian }}">{{ __('site.field_email') }} *</label>
                                        <input id="f-email" type="email" wire:model="email" required
                                               autocomplete="email"
                                               placeholder="{{ __('site.field_email_ph') }}"
                                               class="{{ $pil }} mt-2 @error('email') border-danger @enderror">
                                        @error('email') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-phone" class="{{ $labelIsian }}">{{ __('site.field_phone') }}</label>
                                        <input id="f-phone" type="tel" wire:model="phone" autocomplete="tel"
                                               placeholder="{{ __('site.field_phone_ph') }}" class="{{ $pil }} mt-2">
                                        @error('phone') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="pilih-product_id" class="{{ $labelIsian }}">{{ __('site.field_product') }}</label>

                                        <x-site.pilih name="product_id" :value="$product_id"
                                            :invalid="$errors->has('product_id')"
                                            :placeholder="__('site.field_product_ph')"
                                            :options="collect($products ?? [])
                                                ->map(fn ($p) => ['nilai' => $p->id, 'label' => $p->translated_name])
                                                ->all()" />

                                        @error('product_id') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="f-volume" class="{{ $labelIsian }}">{{ __('site.field_volume') }}</label>
                                        <input id="f-volume" type="text" wire:model="volume"
                                               placeholder="{{ __('site.field_volume_ph') }}" class="{{ $pil }} mt-2">
                                        @error('volume') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="pilih-incoterms" class="{{ $labelIsian }}">{{ __('site.field_incoterms') }}</label>

                                        <x-site.pilih name="incoterms" :value="$incoterms"
                                            :invalid="$errors->has('incoterms')"
                                            :placeholder="__('site.field_incoterms_ph')"
                                            :options="[
                                                ['nilai' => 'FOB', 'label' => 'FOB (Free On Board)'],
                                                ['nilai' => 'CIF', 'label' => 'CIF (Cost, Insurance & Freight)'],
                                                ['nilai' => 'EXW', 'label' => 'EXW (Ex Works)'],
                                            ]" />

                                        @error('incoterms') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="f-message" class="{{ $labelIsian }}">{{ __('site.field_message') }} *</label>
                                        <textarea id="f-message" wire:model="message" rows="3" required
                                                  placeholder="{{ __('site.field_message_ph') }}"
                                                  class="{{ $kotakPesan }} mt-2 @error('message') border-danger @enderror"></textarea>
                                        @error('message') <span class="field-error text-danger-soft">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Tombol MELEBAR PENUH, setinggi dan sebulat isian
                                     di atasnya.

                                     Waktu isiannya bergaris tipis, tombol selebar
                                     panel jadi benda paling berat di borang dan
                                     beratnya tidak sepadan. Sekarang isiannya pil
                                     selebar penuh juga — tombol yang lebih sempit
                                     justru memutus irama yang sudah terbentuk enam
                                     baris di atasnya.

                                     Warnanya EMAS, bukan forest: di kartu yang sudah
                                     cokelat tua, tombol forest lenyap ke dalam
                                     bidangnya sendiri. Hurufnya wajib gelap — putih
                                     di atas emas cuma 2,10:1, forest memberi 6,99:1.

                                     Tanpa bulatan panah. Panah menyatakan
                                     PERPINDAHAN — ke halaman lain, ke berkas yang
                                     turun — sementara tombol ini mengirim sesuatu
                                     dan tetap di tempat. Tulisannya sendiri sudah
                                     menyebutkan apa yang terjadi. --}}
                                <div class="mt-8">
                                    <button type="submit"
                                            wire:loading.attr="disabled" wire:target="executeRecaptcha, submit"
                                            class="group inline-flex h-12 w-full items-center justify-center gap-3
                                                   rounded-full bg-site-gilt px-6 text-site-forest
                                                   ring-1 ring-site-gilt-deep/70
                                                   font-site-body text-site-small font-semibold whitespace-nowrap
                                                   transition-colors duration-300 hover:bg-site-gilt-soft
                                                   disabled:opacity-70">
                                        <span wire:loading.remove wire:target="executeRecaptcha, submit">{{ __('site.btn_submit') }}</span>
                                        <span wire:loading wire:target="executeRecaptcha, submit">{{ __('site.btn_processing') }}</span>

                                    </button>
                                </div>
                                <div class="mt-4 text-center font-site-body text-[10px] leading-relaxed text-site-gilt">
                                    This site is protected by reCAPTCHA and the Google
                                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="underline hover:text-white">Privacy Policy</a> and
                                    <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" class="underline hover:text-white">Terms of Service</a> apply.
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Peta lokasi ────────────────────────────────────────────── --}}
            @if($mapUrl)
                {{-- Jaraknya 56px dari panel di atasnya, bukan 24px: peta ini
                     bagian yang berdiri sendiri, bukan lampiran formulir. --}}
                {{-- Tanpa judul dan tanpa tautan di atasnya.

                     Judul "Find us" menamai sesuatu yang sudah menamai dirinya
                     sendiri — sebuah peta berpenanda di halaman kontak tidak bisa
                     disalahartikan. Tautan "Open in Google Maps" pun tidak perlu:
                     peta sematan Google sudah membawa tautannya sendiri di dalam
                     bingkainya. --}}
                <div class="mt-14">
                    <div class="overflow-hidden rounded-panel border border-line">
                        <iframe src="{{ $mapUrl }}" title="{{ $isi('map_title', 'site.find_us') }}"
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
        <style>
            .grecaptcha-badge { visibility: hidden !important; }
        </style>
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
