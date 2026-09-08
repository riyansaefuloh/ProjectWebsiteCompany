@php
    /*
     * Bahasa diambil dari ruas pertama alamat, bukan dari app()->getLocale().
     *
     * Middleware bahasa hanya berjalan kalau ada rute yang COCOK. Alamat yang
     * tidak cocok dengan rute mana pun — persis kasus yang membawa orang ke
     * sini — tidak pernah melewatinya, jadi pengunjung /id/salah-ketik akan
     * disambut halaman berbahasa Inggris. Yang jatuh dari firstOrFail() di
     * dalam rute memang sudah benar bahasanya; baris ini tidak mengubahnya.
     */
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

{{-- ══════════════════════════════════════════════════════════════════════
     404

     Memakai tata letak publik penuh — berbilah kepala dan berkaki halaman.
     Itu SATU-SATUNYA alasan halaman ini ada: yang lama memakai halaman bawaan
     Laravel, tanpa menu, tanpa kaki, tanpa satu pun tautan. Pengunjung yang
     datang dari hasil pencarian ke produk yang sudah dihapus mentok di situ.

     Aman memanggil basis data di sini: yang gagal cuma pencarian satu baris,
     bukan aplikasinya. Halaman 500 tidak boleh — lihat catatan di sana.
     ══════════════════════════════════════════════════════════════════════ --}}
<x-layouts.public>
    <section class="pb-20 pt-14 md:pt-16 lg:pb-28 lg:pt-20">
        <div class="shell">
            <div class="mx-auto max-w-[44rem] text-center">

                {{-- Angka raksasa berlatar tipis, rupa yang sama dengan nomor
                     kartu di beranda dan halaman Tentang Kami.

                     data-hias: ia hiasan, dan tintanya 7% memang tidak akan
                     lolos ambang kontras — penanda ini yang membuat pemeriksa
                     kontras melewatinya alih-alih melaporkannya sebagai cacat.
                     aria-hidden supaya pembaca layar tidak melafalkan "404" dua
                     kali, karena label di bawahnya sudah menyebutnya. --}}
                <p aria-hidden="true" data-hias
                   class="select-none font-site-display font-bold leading-none tracking-[-0.04em]
                          text-ink/[0.07] text-[clamp(88px,17vw,152px)]">
                    404
                </p>

                <p class="eyebrow mt-4">{{ __('site.error_404_eyebrow') }}</p>

                {{-- 22ch, bukan 18: judul versi Indonesia 29 huruf dan pada 18ch
                     ia pecah jadi tiga baris dengan satu kata sendirian di baris
                     terakhir. --}}
                <h1 class="display mx-auto mt-5 max-w-[22ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.error_404_title')) !!}
                </h1>

                <p class="lede mx-auto mt-5 max-w-[54ch] text-site-body">
                    {{ __('site.error_404_body') }}
                </p>

                {{-- Tombol utama pil forest berbulatan emas, bentuk yang sama
                     dengan "Explore Products" dan "See More News". --}}
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

                {{-- Jalan pintas ke seluruh halaman utama. Tombol di atas cuma
                     menawarkan dua tujuan; yang tersesat di sini bisa saja
                     sedang mencari salah satu dari enam ini. --}}
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
