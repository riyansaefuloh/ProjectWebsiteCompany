<x-layouts.public>
    <section class="pb-20 pt-6 md:pt-8 lg:pb-24 lg:pt-10">
        <div class="shell">

            {{-- Tautan kembali berupa pil pucat bercincin, rupa yang sama dengan
                 jalan kembali di halaman detail produk. Yang lama .link-arrow —
                 tautan telanjang berpanah kecil dari keluarga tombol lama yang
                 sudah tidak dipakai di halaman publik mana pun lagi. --}}
            <a href="{{ route('products.index') }}"
               class="inline-flex h-10 w-max items-center gap-2.5 rounded-full pl-3 pr-5
                      bg-site-paper text-site-forest ring-1 ring-line-strong
                      font-site-body text-site-small font-semibold whitespace-nowrap
                      transition-colors duration-300 hover:bg-site-line">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                             bg-site-canvas text-site-forest">
                    <svg class="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('site.page_products') }}
            </a>

            <div class="mt-6 grid items-start gap-10 lg:mt-8 lg:grid-cols-12 lg:gap-14">

                {{-- ── KIRI: apa yang ditawarkan ───────────────────────────────
                     Label, judul, dan keterangan berukuran sama dengan kepala
                     halaman sertifikat dan produk — 42px cokelat, bukan 48px hitam.
                     Halaman ini bukan beranda; judul sebesar hero membuatnya
                     terbaca lebih penting daripada halaman mana pun di situs. --}}
                <div class="lg:col-span-6">
                    <p class="eyebrow">{{ __('site.offline_catalog') }}</p>

                    <h1 class="display mt-4 max-w-[16ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot(__('site.catalog_headline')) !!}
                    </h1>

                    <p class="lede mt-5 max-w-[52ch]">
                        {{ __('site.catalog_body') }}
                    </p>

                    {{-- Katalog ini dibangkitkan saat tombolnya ditekan, bukan
                         berkas yang diunggah sekali lalu menua di server. Itu
                         perbedaan yang menentukan apakah isinya layak dipercaya,
                         jadi ia disebut — sekali, kecil, di bawah keterangannya.

                         Nada ink-muted, bukan ink-faint: pada 13px, ink-faint
                         terukur 4,33:1 dan jatuh di bawah ambang AA. --}}
                    <p class="mt-6 flex max-w-[26rem] items-start gap-2.5 text-ink-muted text-site-small">
                        <svg class="mt-[3px] h-4 w-4 shrink-0 text-site-gilt-deep" viewBox="0 0 16 16"
                             fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M8 4.4V8l2.4 1.6" stroke="currentColor" stroke-width="1.4"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('site.catalog_generated') }}
                    </p>
                </div>

                {{-- ── KANAN: gerbangnya ───────────────────────────────────────
                     Satu bidang FOREST, bukan krem di atas krem.

                     Panel ini satu-satunya tempat di halaman ini yang meminta
                     tindakan. Berlatar paper, ia cuma sedikit lebih gelap dari kanvas
                     halamannya — selisih yang terlalu tipis untuk menyatakan apa pun,
                     dan yang membedakannya dari halaman tinggal garis tepinya. Gelap
                     pejal membuatnya berdiri sendiri tanpa perlu dibesarkan, dan
                     mengulang bahasa yang sudah dipakai banner penutup dan kaki
                     halaman untuk hal yang sama.

                     Warnanya juga menyelesaikan satu hal lain: tombol emas butuh alas
                     gelap. Di bidang krem, emas terhadap kremnya cuma terpaut tipis;
                     di forest ia 6,99:1. --}}
                <div class="lg:col-span-5 lg:col-start-8 lg:sticky lg:top-[92px]">
                    <div class="rounded-panel bg-site-forest p-7 sm:p-8">

                        {{-- Kepingnya ikut berbalik: emas berisi lambang gelap. Forest
                             di atas forest tidak akan terlihat sama sekali. --}}
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-corner
                                     bg-site-gilt text-site-forest" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <path d="M13.4 2.8H6.8a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.6l-5.8-5.8Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M13.4 2.8v5.8h5.8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <p class="mt-5 font-site-display text-site-title font-bold text-white">
                            {{ __('site.catalog_form_title') }}
                        </p>

                        <form method="POST" action="{{ route('download.catalog') }}" class="mt-5">
                            @csrf

                            {{-- .field-label bernada ink-muted — nada untuk bidang
                                 terang. Di sini ia ditimpa putih redup; utilitas
                                 mengalahkan kelas komponen di urutan lapisan, jadi satu
                                 kelas cukup tanpa perlu menyentuh app.css. --}}
                            <label for="catalog-email" class="field-label text-white/75">
                                {{ __('site.field_email') }} *
                            </label>

                            <input id="catalog-email" type="email" name="email" required
                                   autocomplete="email"
                                   value="{{ old('email') }}"
                                   placeholder="{{ __('site.field_email_ph') }}"
                                   @error('email') aria-invalid="true" aria-describedby="catalog-email-error" @enderror
                                   class="field @error('email') border-danger @enderror">

                            @error('email')
                                {{-- Merah pekat --color-danger terbaca 1,9:1 di forest.
                                     Nada lembutnya 7,62:1. --}}
                                <span id="catalog-email-error" class="field-error text-danger-soft">
                                    {{ $message }}
                                </span>
                            @enderror

                            {{-- Pil EMAS berbulatan forest — kebalikan dari tombol di
                                 bidang terang, dan rupa yang sama dengan "Request Quote"
                                 di kepala situs dan hero. Di atas forest, emas yang
                                 paling dulu terlihat.

                                 Melebar penuh karena ia satu-satunya tindakan di dalam
                                 panel ini; tombol selebar separuh panel cuma menyisakan
                                 ruang yang tidak menawarkan apa pun.

                                 Bulatan panahnya menunjuk KE BAWAH — yang terjadi
                                 sesudah ditekan adalah berkas turun, bukan halaman
                                 berpindah. --}}
                            <button type="submit"
                                    class="group mt-6 inline-flex h-12 w-full items-center justify-center gap-3
                                           rounded-full bg-site-gilt pl-6 pr-2 text-site-forest
                                           ring-1 ring-site-gilt-deep/70
                                           font-site-body text-site-small font-semibold
                                           transition-colors duration-300 hover:bg-site-gilt-soft">
                                {{ __('site.download_pdf') }}
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                             bg-site-forest text-site-gilt transition-transform duration-200
                                             group-hover:translate-y-0.5">
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M8 3v7m0 0L5 7m3 3 3-3M3.5 13h9" stroke="currentColor"
                                              stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>

                            <p class="mt-4 leading-relaxed text-white/75 text-site-small">
                                {{ __('site.catalog_privacy') }}
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
