<div>
    @if(empty($homeSections))
        <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; margin: 30px;">
            <p>{{ __('site.no_home_sections') }}</p>
        </div>
    @endif

    @foreach($homeSections as $section)
        @switch($section['id'])
            @case('hero')
                @php
                    /* Seluruh teks hero datang dari menu Halaman → Susunan
                       beranda → Ubah isi. Yang belum diisi jatuh ke teks bawaan
                       di berkas bahasa, jadi bagian ini tidak pernah kosong. */
                    $heroBody = $isi('hero', 'body', 'site.hero_body');

                    /* Kunci pengaturan lama 'hero_image' tetap dibaca sebagai
                       cadangan supaya foto yang sudah terlanjur diunggah tidak
                       hilang. */
                    $heroAlamat = $gambarBagian['hero'] ?? ($settings['hero_image'] ?? null);

                    $heroImage = !empty($heroAlamat)
                        ? \Illuminate\Support\Facades\Storage::url($heroAlamat)
                        : null;
                @endphp

                {{-- ── HERO ──────────────────────────────────────────────────
                     Foto melintang penuh dengan teks DI ATASNYA, bukan teks di
                     atas krem lalu foto di bawahnya. Bilah kepala ikut
                     mengambang di sini — lihat $heroDiPuncak di layouts/public.

                     pt-[76px] menggantikan tinggi bilah yang kini fixed dan
                     tidak lagi memakan ruang di aliran halaman. --}}
                <section class="relative isolate flex min-h-[620px] items-center overflow-hidden
                                bg-site-shade pt-[76px] sm:min-h-[700px] lg:min-h-[820px]">

                    @if($heroImage)
                        <img src="{{ $heroImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="absolute inset-0 -z-20 h-full w-full object-cover object-[32%_center] lg:object-center">
                    @else
                        {{-- Keadaan "foto belum diunggah". Bukan bidang kosong:
                             hero tanpa apa pun terbaca seperti halaman gagal
                             memuat, bukan seperti halaman yang belum diisi. --}}
                        <div class="absolute inset-0 -z-20 bg-[radial-gradient(1200px_500px_at_15%_-10%,#20402f_0%,transparent_60%),radial-gradient(900px_420px_at_85%_10%,#1a3326_0%,transparent_62%)]"></div>
                    @endif

                    {{-- DUA peredam, dan arahnya berbeda karena tugasnya berbeda.

                         Yang MENDATAR menggelapkan sisi kiri, tempat seluruh
                         teks berdiri, dan melepaskan sisi kanan supaya fotonya
                         tetap terlihat. Ia yang menjamin keterbacaan — dan
                         arahnya mendatar, bukan menegak, karena teksnya kini
                         berdiri di TENGAH secara tegak: gradasi dari bawah hanya
                         kuat di bagian yang justru tidak ditempati teks.

                         Yang MENEGAK di kaki hanya menyambungkan hero dengan
                         bilah sertifikasi di bawahnya, tanpa garis pemisah.

                         Angkanya dipilih untuk kasus TERBURUK, bukan untuk foto
                         yang kebetulan terpasang: pada alpha 0,72 di tepi kanan
                         blok teks, putih tetap lolos 7,9:1 sekalipun fotonya
                         putih polos. Di atas foto gelap selisihnya nyaris tak
                         terlihat, jadi jaminan itu hampir gratis.

                         Warnanya NETRAL (--color-site-shade), bukan hijau merek.
                         Peredam berwarna tidak menggelapkan foto, ia mengecatnya:
                         diukur pada delapan warna foto, peredam hijau menggeser
                         ronanya 44° rata-rata — senja jadi hijau, langit jadi
                         hijau. Yang netral menggesernya 6°.

                         Tepi atas TIDAK digelapkan di sini — itu tugas peredam
                         milik bilah kepala, supaya tidak ada dua peredam yang
                         bertumpuk dan saling tidak tahu. --}}
                    <div aria-hidden="true"
                         class="absolute inset-0 -z-10 bg-gradient-to-r
                                from-site-shade/90 via-site-shade/72 via-55% to-site-shade/15"></div>
                    <div aria-hidden="true"
                         class="absolute inset-x-0 bottom-0 -z-10 h-[45%] bg-gradient-to-t
                                from-site-shade/70 to-transparent"></div>

                    <div class="shell w-full py-14 sm:py-16 lg:py-20">
                        <div class="max-w-[44rem]">

                            {{-- Label di atas judul. Isinya dari kolom
                                 "descriptor" di panel — kolom yang sudah ada,
                                 dulu tergambar di sudut kanan bawah hero. Di
                                 atas judul ia bekerja jauh lebih keras: ia yang
                                 menjawab "ini perusahaan apa" sebelum judulnya
                                 sempat dibaca. --}}
                            <p class="eyebrow eyebrow-invert">
                                {{ $isi('hero', 'descriptor', 'site.hero_descriptor') }}
                            </p>

                            <h1 class="display display-invert mt-6 max-w-[19ch] text-site-hero">
                                {!! \App\Support\Judul::sorot($isi('hero', 'title', 'site.hero_title')) !!}
                            </h1>

                            {{-- Ditulis lewat penyunting teks kaya, jadi bisa
                                 mengandung tag. Yang mengandung tag digambar
                                 sebagai .rich; yang polos tetap satu paragraf
                                 .lede — <p> di dalam <p> bukan HTML yang sah. --}}
                            @if($heroBody !== strip_tags($heroBody))
                                <div class="rich rich-invert mt-6 max-w-[52ch]">{!! $heroBody !!}</div>
                            @else
                                <p class="lede mt-6 max-w-[52ch] text-white/70">{{ $heroBody }}</p>
                            @endif

                            {{-- Dua ajakan, dirupakan sama persis dengan sepasang
                                 pil di bilah kepala — dan itu bukan kebetulan.
                                 Keduanya berdiri di bidang gelap yang sama, dan
                                 pengunjung melihat keempatnya sekaligus dalam
                                 satu layar. Rupa yang berbeda di antara mereka
                                 akan terbaca sebagai dua sistem yang kebetulan
                                 bertemu.

                                 Tidak memakai .btn / .btn-pill: keduanya memakai
                                 rupa aksen KAPITAL berjarak, sementara bilah
                                 kepala memakai huruf biasa. Yang dikejar di sini
                                 keseragaman dengan bilahnya, bukan dengan tombol
                                 di dalam halaman.

                                 Bobotnya tetap tidak sama — hijau pejal untuk yang
                                 diminta, kaca gelap bergaris untuk yang cuma
                                 wajar. Persis pasangan CTA dan penukar bahasa di
                                 atasnya.

                                 Ukurannya pun SAMA PERSIS dengan yang di bilah
                                 kepala, tidak dibesarkan. Keempat pil itu terlihat
                                 sekaligus dalam satu layar, dan dua ukuran untuk
                                 satu rupa membuat yang lebih kecil terbaca seperti
                                 versi yang belum jadi.

                                 PANAH HANYA DI YANG PERTAMA. Dua tombol
                                 berdampingan yang membawa ikon sama membuat
                                 ikonnya berhenti membedakan apa pun — ia jadi
                                 hiasan yang dipakai dua kali. Bilah kepala sudah
                                 memutuskan hal yang sama lebih dulu: di sana
                                 panah cuma ada di ajakannya, tidak di penukar
                                 bahasa di sebelahnya.

                                 Yang kedua sengaja dibiarkan telanjang. Ia bukan
                                 kekurangan yang perlu ditambal ikon lain —
                                 justru ketiadaan itu yang menaruhnya satu tingkat
                                 di bawah, dan yang mengembalikan arti panah pada
                                 satu-satunya tindakan yang benar-benar diminta. --}}
                            <div class="mt-9 flex flex-wrap items-center gap-3 lg:mt-11">
                                <a href="{{ route('inquiry.index') }}"
                                   class="inline-flex items-center gap-2 rounded-full bg-brand py-2.5 pl-5 pr-4
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          text-canvas ring-1 ring-site-shade/80
                                          shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                                          transition-colors duration-300 hover:bg-brand-deep">
                                    {{ $isi('hero', 'cta_primary', 'site.cta_request_quote') }}
                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>

                                <a href="{{ route('products.index') }}"
                                   class="inline-flex items-center rounded-full px-5 py-2.5
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          bg-site-shade/45 text-white ring-1 ring-white/25
                                          transition-colors duration-300 hover:bg-site-shade/65">
                                    {{ $isi('hero', 'cta_secondary', 'site.cta_explore_products') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 CERTIFICATIONS BAR
                 ══════════════════════════════════════════════════════════ --}}
            @case('certifications')
                @if($certifications->isNotEmpty())
                    {{-- Bidang GELAP, bukan krem: bilah ini berdiri tepat di
                         bawah hero dan keduanya terbaca sebagai satu blok. Kalau
                         urutannya dipindah dari panel, ia tetap sah — situs ini
                         sudah memakai bidang gelap di beberapa tempat lain. --}}
                    <section class="border-t border-site-forest-line bg-site-forest py-7 md:py-9">

                        <div class="group relative overflow-hidden
                                    [-webkit-mask-image:linear-gradient(to_right,transparent,#000_9%,#000_91%,transparent)]
                                    [mask-image:linear-gradient(to_right,transparent,#000_9%,#000_91%,transparent)]">

                            <ul class="flex w-max animate-marquee items-center group-hover:[animation-play-state:paused]"
                                aria-hidden="true">
                                @for($copy = 0; $copy < 4; $copy++)
                                    @foreach($certifications as $cert)
                                        @php
                                            $certLogo = $cert->getFirstMediaUrl('logos', 'thumb')
                                                     ?: $cert->getFirstMediaUrl('logos');
                                        @endphp

                                        <li class="flex h-12 shrink-0 items-center justify-center px-8 sm:px-12">
                                            @if($certLogo)
                                                <img src="{{ $certLogo }}" alt="" loading="lazy"
                                                     class="h-10 w-auto max-w-[150px] object-contain opacity-55 brightness-0 invert
                                                            transition duration-200 group-hover:opacity-95">
                                            @else
                                                <span class="flex flex-col justify-center text-center">
                                                    <span class="whitespace-nowrap font-display font-semibold tracking-[-0.01em] text-white/85 text-site-body">
                                                        {{ $cert->translated_name }}
                                                    </span>
                                                    @if($cert->issuer)
                                                        <span class="mt-1 whitespace-nowrap font-site-accent font-medium uppercase tracking-[0.14em] text-site-gilt text-site-micro">
                                                            {{ $cert->issuer }}
                                                        </span>
                                                    @endif
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                @endfor
                            </ul>
                        </div>

                        <ul class="sr-only">
                            @foreach($certifications as $cert)
                                <li>{{ $cert->translated_name }}@if($cert->issuer) — {{ $cert->issuer }}@endif</li>
                            @endforeach
                        </ul>
                    </section>
                @endif
                @break

            @case('products')
                @php
                    /* Teksnya datang dari Halaman → Susunan beranda → Ubah isi.
                       Yang belum diisi jatuh ke bawaan di berkas bahasa. */
                    $produkBody = $isi('products', 'body', 'site.products_body');
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-7">
                                <p class="eyebrow">{{ $isi('products', 'eyebrow', 'site.home_section_products') }}</p>
                                <h2 class="display mt-5 max-w-[18ch] text-site-h2">
                                    {!! \App\Support\Judul::sorot($isi('products', 'title', 'site.products_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:self-end">
                                {{-- Ditulis lewat penyunting teks kaya. Yang
                                     mengandung tag digambar sebagai .rich;
                                     yang polos tetap .lede — <p> di dalam <p>
                                     bukan HTML yang sah. --}}
                                @if($produkBody !== strip_tags($produkBody))
                                    <div class="rich max-w-[46ch]">{!! $produkBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $produkBody }}</p>
                                @endif

                                <a href="{{ route('products.index') }}" class="btn btn-outline btn-arrow mt-7">
                                    {{ $isi('products', 'cta', 'site.cta_explore_products') }}
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        @if($featuredProducts->isNotEmpty())
                            <ul class="mt-12 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3">
                                @foreach($featuredProducts as $product)
                                    <li class="flex">
                                        <x-site.product-card :product="$product" class="w-full" />
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ $isi('products', 'empty', 'site.no_featured_products') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            @case('export_markets')
                @php
                    $pasarBody = $isi('export-markets', 'body', 'site.markets_body');

                    /* Judulnya boleh memuat :count. Penggantiannya dilakukan di sini,
                       bukan lewat __(), karena teks yang diketik pemakai tidak
                       melewati berkas bahasa sama sekali — tanpa baris ini, judul
                       buatan sendiri akan tergambar apa adanya beserta ":count". */
                    $pasarJudul = str_replace(
                        ':count',
                        (string) $exportMarkets->count(),
                        $isi('export-markets', 'title', 'site.markets_title')
                    );
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="mx-auto max-w-[46rem] text-center">
                            <p class="eyebrow">{{ $isi('export-markets', 'eyebrow', 'site.home_section_export_markets') }}</p>
                            <h2 class="display mx-auto mt-5 max-w-[20ch] text-site-h2">
                                {!! \App\Support\Judul::sorot($pasarJudul) !!}
                            </h2>

                            @if($pasarBody !== strip_tags($pasarBody))
                                <div class="rich mx-auto mt-5 max-w-[52ch]">{!! $pasarBody !!}</div>
                            @else
                                <p class="lede mx-auto mt-5 max-w-[52ch]">{{ $pasarBody }}</p>
                            @endif
                        </div>

                        @if($exportMarkets->isNotEmpty())
                            <div class="mt-12 lg:mt-14">
                                <x-site.export-map :markets="$exportMarkets" :show-list="false" />
                            </div>

                            <div class="mt-10 flex justify-center">
                                <a href="{{ route('export-markets.index') }}" class="btn btn-outline btn-arrow">
                                    {{ $isi('export-markets', 'cta', 'site.cta_explore_markets') }}
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
                                {{ $isi('export-markets', 'empty', 'site.no_export_markets') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 WHY CHOOSE US
                 ══════════════════════════════════════════════════════════ --}}
            @case('about')
                @php
                    /* Ikonnya tetap: ia bagian dari rancangan, bukan isi yang
                       diketik. Judul dan keterangannya boleh diganti dari panel. */
                    $pillars = collect(['quality', 'capacity', 'compliance', 'logistics'])
                        ->map(fn ($ikon, $i) => [
                            'icon'  => $ikon,
                            'title' => $isi('about', 'pillar_' . ($i + 1) . '_title', 'site.pillar_' . ($i + 1) . '_title'),
                            'body'  => $isi('about', 'pillar_' . ($i + 1) . '_body',  'site.pillar_' . ($i + 1) . '_body'),
                        ])
                        ->all();

                    $tentangBody = $isi('about', 'body', 'site.pillars_body');
                @endphp

                <section class="section border-t border-line">
                    <div class="shell">

                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-6">
                                <p class="eyebrow">{{ $isi('about', 'eyebrow', 'site.pillars_eyebrow') }}</p>
                                <h2 class="display mt-5 max-w-[16ch] text-site-h2">
                                    {!! \App\Support\Judul::sorot($isi('about', 'title', 'site.pillars_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                                @if($tentangBody !== strip_tags($tentangBody))
                                    <div class="rich max-w-[46ch]">{!! $tentangBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $tentangBody }}</p>
                                @endif
                            </div>
                        </div>

                        <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                                   [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8 lg:mt-14">
                            @foreach($pillars as $pillar)
                                <li class="w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)] lg:w-[calc(25%-0.9375rem)]">
                                    <div class="card group flex h-full min-h-[260px] flex-col justify-between p-6
                                                transition-colors duration-300 hover:border-forest hover:bg-forest sm:min-h-[290px]">

                                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full
                                                     bg-brand text-white transition-colors duration-300
                                                     group-hover:bg-canvas group-hover:text-forest">
                                            <x-icon.pillar :name="$pillar['icon']" />
                                        </span>

                                        <div class="mt-10">
                                            <h3 class="font-display font-semibold leading-snug tracking-[-0.01em] text-ink transition-colors duration-300 group-hover:text-white text-site-lede">
                                                {{ $pillar['title'] }}
                                            </h3>
                                            <p class="mt-2.5 leading-relaxed text-ink-muted transition-colors duration-300 group-hover:text-white/70 text-site-small">
                                                {{ $pillar['body'] }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
                @break

            @case('news')
                <section class="section bg-forest">
                    <div class="shell">
                        <p class="eyebrow eyebrow-invert">{{ $isi('news', 'eyebrow', 'site.news_eyebrow') }}</p>
                        <h2 class="display mt-5 max-w-[20ch] text-white text-site-h2">
                            {!! \App\Support\Judul::sorot($isi('news', 'title', 'site.news_title')) !!}
                        </h2>

                        @if($latestNews->isNotEmpty())
                            <div class="mt-12 grid items-stretch gap-6 lg:mt-14 lg:grid-cols-12">

                                <div class="flex flex-col gap-6 lg:col-span-7">
                                    @foreach($latestNews->take(2) as $article)
                                        @php
                                            $cover = $article->getFirstMediaUrl('covers', 'thumb')
                                                  ?: $article->getFirstMediaUrl('covers', 'webp');
                                        @endphp

                                        <article class="flex flex-1 flex-col overflow-hidden rounded-corner sm:flex-row">
                                            <a href="{{ route('news.show', $article->slug) }}"
                                               class="relative block h-[190px] shrink-0 overflow-hidden bg-forest-line sm:h-auto sm:w-[38%]"
                                               tabindex="-1" aria-hidden="true">
                                                @if($cover)
                                                    <img src="{{ $cover }}" alt="" loading="lazy"
                                                         class="absolute inset-0 h-full w-full object-cover">
                                                @endif
                                            </a>

                                            <div class="flex min-w-0 flex-1 flex-col bg-forest-line/40 px-6 py-6">
                                                @if($article->published_at)
                                                    <p class="flex items-center gap-2 text-white/50 text-site-micro">
                                                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-soft" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                            <rect x="2.2" y="3.4" width="11.6" height="10.4" rx="1.6" stroke="currentColor" stroke-width="1.3"/>
                                                            <path d="M2.2 6.6h11.6M5.6 2.2v2.4M10.4 2.2v2.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                                        </svg>
                                                        <time datetime="{{ $article->published_at->toDateString() }}">
                                                            {{ $article->published_at->translatedFormat('d F Y') }}
                                                        </time>
                                                    </p>
                                                @endif

                                                <h3 class="mt-3 font-display font-semibold leading-snug tracking-[-0.01em] text-white text-site-lede">
                                                    <a href="{{ route('news.show', $article->slug) }}" class="transition-colors hover:text-brand-soft">
                                                        {{ $article->translated_title }}
                                                    </a>
                                                </h3>

                                                @if($article->translated_excerpt)
                                                    <p class="mt-2.5 line-clamp-2 leading-relaxed text-white/60 text-site-small">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 120) }}
                                                    </p>
                                                @endif

                                                <a href="{{ route('news.show', $article->slug) }}" class="link-arrow link-arrow-invert mt-auto pt-6">
                                                    {{ $isi('news', 'read_label', 'site.read_article') }}
                                                    <span>
                                                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>

                                @php
                                    $promo = $latestNews->get(2) ?? $latestNews->first();
                                    $promoCover = $promo?->getFirstMediaUrl('covers', 'webp')
                                               ?: $promo?->getFirstMediaUrl('covers', 'thumb');
                                @endphp

                                <a href="{{ route('news.index') }}"
                                   class="group relative isolate flex min-h-[320px] flex-col justify-end overflow-hidden rounded-corner bg-forest-line p-7 lg:col-span-5">
                                    @if($promoCover)
                                        <img src="{{ $promoCover }}" alt="" aria-hidden="true" loading="lazy"
                                             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">
                                    @endif

                                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest via-forest/70 to-forest/20" aria-hidden="true"></div>

                                    <p class="display max-w-[16ch] text-white text-site-h3">
                                        {!! \App\Support\Judul::sorot($isi('news', 'promo_title', 'site.news_promo_title')) !!}
                                    </p>

                                    <span class="btn-pill btn-pill-invert mt-7 self-start">
                                        {{ $isi('news', 'cta', 'site.cta_see_more_news') }}
                                        <span>
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        @else
                            <p class="mt-12 rounded-corner border border-dashed border-forest-line px-6 py-14 text-center text-white/60 text-site-body">
                                {{ $isi('news', 'empty', 'site.no_news_found') }}
                            </p>
                        @endif
                    </div>
                </section>
                @break

            {{-- ══════════════════════════════════════════════════════════
                 CLOSING CTA BANNER
                 ══════════════════════════════════════════════════════════ --}}
            @case('contact')
                @php
                    $whatsapp = $settings['whatsapp_number'] ?? '';
                    $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;

                /* Fotonya menempel pada bagiannya sendiri. Kunci pengaturan lama
                   'cta_image' tetap dibaca sebagai cadangan supaya foto yang
                   sudah terlanjur diunggah tidak hilang. */
                    $ctaAlamat = $gambarBagian['contact'] ?? ($settings['cta_image'] ?? null);

                    $ctaImage = !empty($ctaAlamat)
                        ? \Illuminate\Support\Facades\Storage::url($ctaAlamat)
                        : null;

                    $kontakBody = $isi('contact', 'body', 'site.cta_body');
                @endphp

                <section class="pb-16 pt-16 md:pb-20 md:pt-20 lg:pb-24 lg:pt-24">
                    <div class="shell">
                        <div class="relative isolate overflow-hidden rounded-panel bg-forest px-6 py-16 text-center sm:px-10 md:py-20 lg:py-24">

                            @if($ctaImage)
                                <img src="{{ $ctaImage }}" alt="" aria-hidden="true" loading="lazy"
                                     class="absolute inset-0 -z-10 h-full w-full object-cover">
                                <div class="absolute inset-0 -z-10 bg-forest/85" aria-hidden="true"></div>
                            @endif

                            <h2 class="display mx-auto max-w-[18ch] text-white text-site-h2">
                                {!! \App\Support\Judul::sorot($isi('contact', 'title', 'site.cta_title')) !!}
                            </h2>

                            @if($kontakBody !== strip_tags($kontakBody))
                                <div class="rich rich-invert mx-auto mt-6 max-w-[56ch] text-white/70">{!! $kontakBody !!}</div>
                            @else
                                <p class="mx-auto mt-6 max-w-[56ch] leading-relaxed text-white/70 text-site-lede">
                                    {{ $kontakBody }}
                                </p>
                            @endif

                            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('inquiry.index') }}" class="btn-pill">
                                    {{ $isi('contact', 'cta_primary', 'site.cta_request_quote') }}
                                    <span>
                                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>

                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-outline-invert">
                                        <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                        {{ $isi('contact', 'cta_whatsapp', 'site.cta_whatsapp') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
                @break

            @default
                <!-- OTHER SECTIONS -->
                <div style="margin-bottom: 50px; padding: 20px; border: 1px dashed #94a3b8; background: #f1f5f9;">
                    <h2>[{{ __('site.home_section_' . str_replace('-', '_', $section['id'])) }}]</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Buat UI untuk seksi {{ __('site.home_section_' . str_replace('-', '_', $section['id'])) }} di sini.]
                    </div>
                </div>
                @break
        @endswitch
    @endforeach
</div>
