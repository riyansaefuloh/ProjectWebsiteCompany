@php
    $hasFilters = filled($search) || filled($category);
@endphp

<div>
    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            @php $produkBody = $isi('body', 'site.page_products_sub'); @endphp

            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-6">
                    <p class="eyebrow">{{ $isi('eyebrow', 'site.home_section_products') }}</p>

                    {{-- 42px cokelat, ukuran dan warna yang sama dengan judul seksi
                         produk di beranda. Tetap <h1> — ia judul halaman ini — tapi
                         tidak lagi 48px: ukuran itu membuatnya lebih besar daripada
                         judul mana pun di situs, padahal perannya sama. --}}
                    <h1 class="display mt-5 max-w-[16ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($isi('title', 'site.page_products')) !!}
                    </h1>
                </div>

                <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                    {{-- Isian ini ditulis lewat penyunting teks kaya, jadi isinya
                         mengandung tag. Yang lama menggambarnya dengan {{ }} —
                         tag-nya ikut TERCETAK di layar sebagai "<p>…</p>". Yang
                         mengandung tag digambar sebagai .rich; yang polos tetap
                         .lede, karena <p> di dalam <p> bukan HTML yang sah. --}}
                    @if($produkBody !== strip_tags($produkBody))
                        <div class="rich max-w-[46ch]">{!! $produkBody !!}</div>
                    @else
                        <p class="lede max-w-[46ch] text-site-body">{{ $produkBody }}</p>
                    @endif

                    {{-- Katalog PDF jadi TAUTAN, bukan kartu berbingkai.

                         Kartu di sudut kanan atas berebut perhatian dengan judul
                         halamannya sendiri — dua bidang berjudul tebal bersebelahan,
                         dan yang di kanan menang karena berbingkai. Padahal orang
                         datang ke halaman ini untuk melihat produknya; katalog itu
                         jalan pintas bagi yang sudah tahu maunya. --}}
                    <a href="{{ route('download.catalog.form') }}"
                       class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                              bg-site-forest text-white
                              font-site-body text-site-small font-semibold whitespace-nowrap
                              transition-colors duration-300 hover:bg-site-brand-deep">
                        {{ $isi('catalog_cta', 'site.download_pdf') }}
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                     bg-site-gilt text-site-forest transition-transform duration-200
                                     group-hover:translate-x-0.5">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M8 3v8m0 0L4.8 7.8M8 11l3.2-3.2M3 13h10" stroke="currentColor"
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         BILAH SARING
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-8">
        <div class="shell">

            {{-- TANPA garis pemisah — yang memisahkan cuma ruang.

                 Kepala halaman berakhir dengan keterangan dan tombolnya; baris ini
                 mulai dengan pil kategori. Keduanya sudah berbeda bentuk, berbeda
                 ukuran huruf, dan berbeda warna, jadi mata sudah tahu di mana yang
                 satu berhenti. Garis di antaranya cuma menyatakan ulang hal yang
                 sudah jelas — dan garis yang tidak dibutuhkan selalu terbaca
                 sebagai batas yang berarti sesuatu.

                 Kartu di sini berlaku seolah menyaring itu tugas tersendiri yang
                 punya bidangnya sendiri — padahal ia cuma cara memilih apa yang
                 tampil di bawahnya. Bingkai, latar, dan bilah kaki membuatnya
                 sepadat kartu produk, dan halaman ini jadi punya dua benda yang
                 sama beratnya sebelum satu produk pun terlihat.

                 Yang tersisa sekarang satu garis rambut dengan kategori duduk di
                 atasnya, sama seperti bilah tab. Kategori memang "pilih satu", dan
                 tab menyatakan itu tanpa perlu bingkai. --}}
            <div class="pt-2 lg:pt-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between lg:gap-6">

                    {{-- Kategori sebagai PIL bergaris rambut.

                         Yang diam cuma garis tepinya — tanpa latar sendiri, jadi
                         empat pil berdampingan tidak menumpuk empat bidang krem di
                         atas bidang krem halamannya. Yang aktif dipejalkan forest,
                         dan karena tetangganya cuma bergaris, satu bidang gelap itu
                         sudah cukup menandainya tanpa perlu ikut membesar. --}}
                    <fieldset class="-mx-6 min-w-0 overflow-x-auto px-6 sm:-mx-8 sm:px-8
                                     lg:mx-0 lg:overflow-visible lg:px-0
                                     [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        <legend class="sr-only">{{ __('site.category') }}</legend>

                        <div class="flex w-max items-center gap-2 lg:w-auto lg:flex-wrap">
                            @php
                                $pilDiam  = 'border-line text-ink-muted hover:border-line-strong hover:text-ink';
                                $pilAktif = 'border-site-forest bg-site-forest text-white';
                            @endphp

                            <button type="button" wire:click="selectCategory('')"
                                    aria-pressed="{{ $category === '' ? 'true' : 'false' }}"
                                    @class([
                                        'inline-flex h-9 shrink-0 items-center whitespace-nowrap rounded-full border px-4',
                                        'font-site-body text-site-small font-semibold transition-colors',
                                        $pilAktif => $category === '',
                                        $pilDiam  => $category !== '',
                                    ])>
                                {{ __('site.all_categories') }}
                            </button>

                            @foreach($categories as $cat)
                                @php $isActive = $category === $cat->slug; @endphp

                                <button type="button" wire:click="selectCategory('{{ $cat->slug }}')"
                                        aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                        @class([
                                            'inline-flex h-9 shrink-0 items-center whitespace-nowrap rounded-full border px-4',
                                            'font-site-body text-site-small font-semibold transition-colors',
                                            $pilAktif => $isActive,
                                            $pilDiam  => ! $isActive,
                                        ])>
                                    {{ $cat->translated_name }}

                                    {{-- Jumlahnya menyusut jadi angka kecil pucat.
                                         Ia keterangan tentang kategorinya, bukan
                                         bagian dari namanya — dan petak berlatar yang
                                         dulu membungkusnya menaikkannya jadi sederajat
                                         dengan nama yang diterangkannya. --}}
                                    <span @class([
                                        'ml-1.5 text-site-micro font-semibold tabular-nums',
                                        'text-white/60' => $isActive,
                                        'text-ink-faint' => ! $isActive,
                                    ])>{{ $cat->products_count }}</span>
                                </button>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="flex shrink-0 items-center gap-2">
                        {{-- Kotak cari 36px bergaris rambut, tanpa latar sendiri. Ia
                             berdiri di bidang halaman, bukan di dalam kartu, jadi latar
                             tambahan cuma menghasilkan dua nada krem yang bertumpuk dan
                             keduanya jadi keruh.

                             Menu urutan yang dulu berdiri di sebelahnya sudah dilepas:
                             empat pilihan yang menawarkan cara memandang katalog yang
                             sama untuk sembilan produk. Yang tersisa satu urutan —
                             unggulan lebih dulu, lalu nomor urut dari panel — dan itu
                             memang urutan yang dimaksudkan pemiliknya. --}}
                        <div class="relative flex-1 lg:flex-none">
                            <label for="product-search" class="sr-only">{{ __('site.search_products') }}</label>

                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="7.2" cy="7.2" r="4.8" stroke="currentColor" stroke-width="1.5"/>
                                <path d="m10.8 10.8 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>

                            <input id="product-search" type="search"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="{{ __('site.search_placeholder') }}"
                                   class="h-9 w-full rounded-full border border-line bg-transparent pl-10 pr-4
                                          font-site-body text-site-small text-ink placeholder:text-ink-faint
                                          transition-colors focus:border-site-forest focus:outline-none
                                          lg:w-[15rem]">
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Baris keterangan ────────────────────────────────────────────
                 Jumlah hasil di kiri, saringan yang sedang aktif di kanan. Keduanya
                 menjawab pertanyaan yang sama — "kenapa yang tampil cuma ini" — jadi
                 keduanya berdiri di satu baris, bukan di dua tempat berjauhan.

                 Tiap keping membawa tombol lepasnya sendiri: melepas kategori tidak
                 boleh ikut menghapus kata yang sedang dicari. --}}
            <div class="mt-5 flex flex-wrap items-center justify-between gap-x-4 gap-y-3">
                {{-- Jumlah hasil disusun sebagai LABEL, bukan kalimat.

                     Sebagai kalimat 13px biasa ia terbaca seperti teks yang
                     tertinggal di layar — tidak cukup besar untuk jadi keterangan,
                     tidak cukup kecil untuk jadi catatan. Huruf aksen kapital
                     berjarak menjadikannya penanda, rupa yang sama dengan label
                     kecil di atas tiap judul seksi.

                     ink-muted, bukan ink-faint: pada 11px yang terukur dari piksel
                     cuma 4,33:1, di bawah ambang. --}}
                <p class="font-site-accent text-site-micro font-medium uppercase tracking-[0.14em] text-ink-muted"
                   aria-live="polite">
                    {{ trans_choice('site.products_count', $products->total(), ['count' => $products->total()]) }}
                </p>

                @if($hasFilters)
                    <div class="flex flex-wrap items-center gap-2">
                        @if(filled($search))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-site-paper py-1 pl-3 pr-1.5
                                         text-site-small text-ink-muted">
                                {{ __('site.filter_search') }}:
                                <span class="font-semibold text-site-forest">{{ $search }}</span>

                                <button type="button" wire:click="$set('search', '')"
                                        aria-label="{{ __('site.filter_search') }}: {{ $search }} — {{ __('site.reset_filters') }}"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                               text-ink-faint transition-colors hover:bg-site-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if(filled($category))
                            @php $namaKategori = $categories->firstWhere('slug', $category)?->translated_name ?? $category; @endphp

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-site-paper py-1 pl-3 pr-1.5
                                         text-site-small text-ink-muted">
                                {{ __('site.filter_category') }}:
                                <span class="font-semibold text-site-forest">{{ $namaKategori }}</span>

                                <button type="button" wire:click="selectCategory('')"
                                        aria-label="{{ __('site.filter_category') }}: {{ $namaKategori }} — {{ __('site.reset_filters') }}"
                                        class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                               text-ink-faint transition-colors hover:bg-site-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        <button type="button" wire:click="resetFilters"
                                class="font-site-body text-site-small font-semibold text-site-forest
                                       underline decoration-line-strong underline-offset-4
                                       transition-colors hover:decoration-site-forest">
                            {{ __('site.clear_all_filters') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         HASIL
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">

            <div wire:loading.class="opacity-40" class="transition-opacity duration-200">
                @if($products->isNotEmpty())
                    <ul class="grid auto-rows-fr gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($products as $product)
                            <li class="flex" wire:key="product-{{ $product->id }}">
                                <x-site.product-card :product="$product"
                                                     :label="$isi('view_label', 'site.view_details')"
                                                     class="w-full" />
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-12">
                        {{ $products->links('vendor.pagination.site') }}
                    </div>
                @else
                    <div class="rounded-corner border border-dashed border-line px-6 py-20 text-center">
                        {{-- font-site-display: font-display di lingkup .situs dipetakan
                             ke huruf BADAN, jadi kelas itu menghasilkan Inter. --}}
                        <p class="font-site-display font-bold text-site-forest text-site-title">
                            {{ $isi('empty', 'site.no_products_found') }}
                        </p>

                        {{-- Pil forest, rupa yang sama dengan ajakan di seluruh
                             situs. Yang lama .btn .btn-outline — bergaris, sudut 4px,
                             tinggi 44px, huruf aksen kapital. --}}
                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters"
                                    class="mt-6 inline-flex h-10 items-center rounded-full bg-site-forest px-6
                                           font-site-body text-site-small font-semibold text-white
                                           transition-colors duration-300 hover:bg-site-brand-deep">
                                {{ __('site.reset_filters') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
