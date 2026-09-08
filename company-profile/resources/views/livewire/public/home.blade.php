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
                     tidak lagi memakan ruang di aliran halaman.

                     Tingginya SATU LAYAR, bukan angka piksel tetap. Sebelumnya
                     820px di desktop — lebih tinggi daripada bidang tampak
                     kebanyakan laptop, jadi foto hero tidak pernah terlihat utuh
                     sekaligus dan bilah sertifikasi di bawahnya tidak pernah
                     memberi petunjuk bahwa halaman berlanjut.

                     svh, bukan vh: di peramban ponsel, vh dihitung terhadap
                     tinggi TANPA bilah alamat, jadi hero jadi lebih tinggi
                     daripada layar yang benar-benar terlihat dan kakinya
                     tersembunyi sampai pengunjung menggulung. min-h menjaga
                     jendela yang sangat pendek tetap kebagian ruang. --}}
                <section class="relative isolate flex h-[100svh] min-h-[540px] items-center
                                overflow-hidden bg-site-shade pt-[76px]">

                    @if($heroImage)
                        <img src="{{ $heroImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="absolute inset-0 -z-20 h-full w-full object-cover object-[32%_center] lg:object-center">
                    @else
                        {{-- Keadaan "foto belum diunggah". Bukan bidang kosong:
                             hero tanpa apa pun terbaca seperti halaman gagal
                             memuat, bukan seperti halaman yang belum diisi.

                             Warnanya ikut palet — dua radial cokelat hangat,
                             bukan hijau. Yang lama tertinggal dari palet
                             sebelumnya dan tidak pernah terlihat karena fotonya
                             selalu ada; ia baru muncul justru ketika fotonya
                             dihapus. --}}
                        <div class="absolute inset-0 -z-20 bg-[radial-gradient(1200px_500px_at_15%_-10%,#3d2f1f_0%,transparent_60%),radial-gradient(900px_420px_at_85%_10%,#2b2015_0%,transparent_62%)]"></div>
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
                         yang kebetulan terpasang — tapi kasus terburuk itu
                         ternyata jauh lebih murah daripada yang dikira.

                         Diukur pada foto yang sekarang terpasang, dengan
                         seluruh peredam dimatikan: petak tempat teks berdiri
                         bermedian luminans 0,085 — fotonya memang sudah gelap
                         sendiri, 7,75:1 terhadap putih. Yang benar-benar
                         dibutuhkan cuma alfa 0,53.

                         Dan untuk MENJAMIN 4,5:1 sekalipun foto ini diganti
                         putih polos, alfa 0,58 sudah cukup: 4,79:1. Angka lama
                         0,90 memberi 15,4:1 — sepuluh kali lipat dari yang
                         diperlukan, dan seluruh kelebihan itu dibayar dengan
                         fotonya yang tidak lagi terlihat.

                         Jadi yang berubah bukan jaminannya melainkan
                         pemborosannya. Peredam DITAHAN pada 0,58 sepanjang
                         petak teks — yang berakhir di 58% lebar — lalu jatuh
                         cepat ke 0,08 di sisa kanannya, tempat tidak ada satu
                         huruf pun yang perlu dilindungi.

                         Warnanya NETRAL (--color-site-shade), dan itu satu-satunya
                         token yang TIDAK ikut menghangat bersama palet ini.
                         Peredam berwarna tidak menggelapkan foto, ia mengecatnya:
                         diukur pada delapan warna foto, peredam berona menggeser
                         ronanya 44° rata-rata — senja jadi berona itu, langit
                         jadi berona itu. Yang netral menggesernya 6°.

                         Tepi atas TIDAK digelapkan di sini — itu tugas peredam
                         milik bilah kepala, supaya tidak ada dua peredam yang
                         bertumpuk dan saling tidak tahu. --}}
                    {{-- Di layar SEMPIT peredamnya rata, bukan mendatar.
                         Teks di sana memenuhi lebar layar, sementara gradasi
                         mendatar justru paling tipis di kanan — dan tepi kanan
                         teks akan jatuh jauh di bawah ambang. Peredam rata pada
                         0,58 memberi 4,79:1 di lebar berapa pun, bahkan atas
                         foto putih polos. --}}
                    <div aria-hidden="true"
                         class="absolute inset-0 -z-10 bg-site-shade/58 lg:hidden"></div>
                    <div aria-hidden="true"
                         class="absolute inset-0 -z-10 hidden bg-gradient-to-r
                                from-site-shade/62 via-site-shade/58 via-58% to-site-shade/8 lg:block"></div>

                    {{-- Peredam kaki: tugasnya menyambungkan hero dengan seksi di
                         bawahnya, BUKAN keterbacaan — tidak ada teks di sana.
                         Karena itu ia yang paling banyak dipangkas: 0,70 jadi
                         0,45 dan tingginya 45% jadi 38%. --}}
                    <div aria-hidden="true"
                         class="absolute inset-x-0 bottom-0 -z-10 h-[38%] bg-gradient-to-t
                                from-site-shade/45 to-transparent"></div>

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

                                 Bobotnya tetap tidak sama — aksen pejal untuk
                                 yang diminta, kaca gelap bergaris untuk yang cuma
                                 wajar. Persis pasangan CTA dan penukar bahasa di
                                 atasnya.

                                 Warnanya SAMA dengan label di atas judul dan
                                 kata aksen di dalamnya: satu aksen untuk seluruh
                                 hero, bukan tiga warna yang masing-masing
                                 menuntut perhatian sendiri.

                                 Ukurannya pun SAMA PERSIS dengan yang di bilah
                                 kepala, tidak dibesarkan. Keempat pil itu terlihat
                                 sekaligus dalam satu layar, dan dua ukuran untuk
                                 satu rupa membuat yang lebih kecil terbaca seperti
                                 versi yang belum jadi.

                                 PANAH HANYA DI YANG KEDUA, dan berbulatan.
                                 Dua tombol berdampingan yang membawa ikon sama
                                 membuat ikonnya berhenti membedakan apa pun.

                                 Yang kedua yang mendapatkannya karena panah
                                 berarti BERPINDAH TEMPAT: "Explore Products"
                                 membawa ke halaman lain, sementara "Request
                                 Quote" adalah permintaan — tujuan, bukan arah.

                                 Bulatan beraksen di dalam pil kaca gelap, bukan
                                 sebaliknya: pil aksen pejal sudah jadi benda
                                 paling terang di bidang ini, dan menaruh bulatan
                                 di dalamnya cuma menambah tekanan pada yang sudah
                                 paling ditekankan. Di dalam pil yang tenang, satu
                                 titik aksen justru mengangkatnya tanpa membuatnya
                                 menyaingi yang pertama.

                                 Bulatannya mengambil bantalan kanan pilnya, jadi
                                 tingginya tidak bertambah: 28px di dalam pil 40px
                                 menyisakan 6px di atas dan bawah. Tinggi kedua
                                 tombol dikunci 40px — tanpa itu yang berbulatan
                                 tumbuh sendiri dan keduanya berhenti sebaris.

                                 Yang kedua sengaja dibiarkan telanjang. Ia bukan
                                 kekurangan yang perlu ditambal ikon lain —
                                 justru ketiadaan itu yang menaruhnya satu tingkat
                                 di bawah, dan yang mengembalikan arti panah pada
                                 satu-satunya tindakan yang benar-benar diminta. --}}
                            <div class="mt-9 flex flex-wrap items-center gap-3 lg:mt-11">
                                <a href="{{ route('inquiry.index') }}"
                                   class="inline-flex h-10 items-center rounded-full bg-site-gilt px-6
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          text-site-forest ring-1 ring-site-gilt-deep/70
                                          shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                                          transition-colors duration-300 hover:bg-site-gilt-soft">
                                    {{ $isi('hero', 'cta_primary', 'site.cta_request_quote') }}
                                </a>

                                <a href="{{ route('products.index') }}"
                                   class="group inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          bg-site-shade/45 text-white ring-1 ring-white/25
                                          transition-colors duration-300 hover:bg-site-shade/65">
                                    {{ $isi('hero', 'cta_secondary', 'site.cta_explore_products') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
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
                    {{-- ── BAR KEPERCAYAAN ───────────────────────────────────
                         Deretan LAMBANG, satu baris, di bawah satu kalimat
                         pembuka yang berdiri di tengah.

                         Tinggi lambang dikunci, lebarnya dibiarkan mengalir.
                         Lambang lembaga sertifikasi berbeda-beda bentuknya —
                         Fairtrade nyaris bujur sangkar, Rainforest Alliance
                         melebar. Mengunci lebarnya akan membuat yang melebar
                         menyusut sampai tidak terbaca, sementara mengunci
                         tingginya membuat semuanya terbaca sama besar.

                         Karena lebarnya tidak seragam, deretannya memakai flex
                         yang dipusatkan, BUKAN petak enam kolom. Petak memberi
                         jatah lebar yang sama kepada lambang yang lebarnya tidak
                         sama, dan yang sempit akan mengambang di tengah jatah
                         yang kebesaran.

                         Tanpa garis pemisah tegak. Garis di antara lambang yang
                         tingginya berbeda-beda jatuh di tempat yang tidak sama
                         terhadap masing-masing, dan yang terbaca bukan pemisah
                         melainkan ketidakrapian.

                         CADANGAN PER BUTIR, bukan satu keputusan untuk semua.
                         Sertifikasi yang punya lambang digambar sebagai lambang;
                         yang belum, sebagai NAMA yang disusun seperti wordmark.
                         Alasannya keadaan datanya memang berubah-ubah: pernah
                         keenam berkasnya identik dan berisi lambang agensi
                         pembuat situs, sekarang keenamnya tidak punya berkas
                         sama sekali. Cadangan yang berlaku untuk satu butir saja
                         membuat bilah ini benar apa pun isinya — dan berubah
                         jadi persis deretan lambang begitu lambangnya diunggah,
                         satu per satu, tanpa menyentuh berkas ini lagi.

                         Terang, bukan gelap seperti di gambar acuan. Hero di
                         atasnya setinggi satu layar penuh dan gelap; bilah gelap
                         lagi tepat di bawahnya membuat seluruh kepala halaman
                         jadi satu blok berat tanpa jeda. Di sinilah halaman
                         berganti napas.

                         Tanpa gulungan otomatis. Marquee membaca sebagai "isinya
                         terlalu banyak untuk muat", padahal enam butir muat
                         seluruhnya — dan yang bergerak tidak bisa dipindai. --}}
                    {{-- Tanpa label dan tanpa tautan "View Certifications".

                         Keduanya dilepas karena tidak satu pun bisa disunting
                         dari panel: bagian 'certifications' tidak ada di
                         BIDANG_BAGIAN, dan tautannya teks bawaan yang terpaku di
                         berkas bahasa. Teks yang tergambar di halaman publik tapi
                         tidak bisa diubah dari panel adalah janji yang tidak
                         ditepati — pemiliknya melihatnya, mencarinya di panel,
                         dan tidak menemukannya.

                         Yang tersisa justru cukup: deretan lambang tidak
                         memerlukan pengumuman bahwa ia deretan lambang. --}}
                    @php
                        /* Trek marquee dirakit dari DUA paruh yang identik, lalu
                           digeser -50% — itu yang membuat sambungannya tidak
                           terlihat: begitu paruh pertama habis, paruh kedua sudah
                           berada persis di tempat paruh pertama bermula.

                           Tiap paruh diulang sampai isinya cukup untuk melebihi
                           layar terlebar. Empat lambang saja tidak memenuhi
                           1440px, dan trek yang lebih sempit dari layarnya akan
                           menyisakan lubang kosong yang berjalan. */
                        $ulangan = (int) ceil(8 / max($certifications->count(), 1));
                        $deret = collect(range(1, $ulangan))
                            ->flatMap(fn () => $certifications)
                            ->all();
                    @endphp

                    {{-- Latarnya kanvas, BUKAN gelap seperti gambar acuan — dan
                         itu dipaksa oleh lambangnya sendiri, bukan selera.

                         Diukur pada keempat berkas yang terunggah: Fairtrade
                         bertinta L* 14 dan ISO 22000 L* 1, keduanya nyaris hitam.
                         Di atas bilah gelap keduanya lenyap. Deretan lambang di
                         gambar acuan bisa gelap karena berkasnya memang disiapkan
                         putih; berkas di sini tidak.

                         Yang berubah nadanya: dari kertas krem ke kanvas, jadi
                         bilah ini berhenti jadi pita berwarna dan lambangnya
                         berdiri langsung di atas halaman.

                         Garis pemisahnya ADA, tapi digambar oleh seksi di
                         bawahnya — yang memang sudah membawa border-t sendiri.
                         Bilah ini tidak menambah border-b lagi: dua garis rambut
                         yang berdempetan terbaca sebagai satu garis yang
                         tebalnya salah, bukan sebagai dua pemisah. Satu garis,
                         satu sumber. --}}
                    <section class="bg-site-canvas">
                        {{-- Deretannya berjalan DI DALAM lebar isi halaman, bukan
                             dari tepi ke tepi layar. Marquee yang melintasi
                             seluruh lebar layar tidak punya hubungan dengan
                             kolom apa pun di atas maupun di bawahnya, dan
                             terbaca seperti pita yang menempel dari luar.
                             Dibatasi shell, ia jadi bagian dari halaman.

                             Tepinya memudar, bukan terpotong — lihat
                             .marquee-fade di app.css. --}}
                        <div class="shell py-7 lg:py-9">
                            <div class="marquee-fade overflow-hidden">

                            {{-- Disorot: berhenti DAN warnanya kembali asli.

                                 Diam-diam kelabu, itu yang membuat deretan ini
                                 terbaca sebagai satu benda. Delapan lambang
                                 berwarna-warni dari delapan lembaga yang berbeda
                                 saling berebut, dan yang tersisa di mata bukan
                                 "bersertifikat" melainkan "banyak warna".

                                 Warna aslinya dikembalikan saat disorot, bersama
                                 berhentinya gerakan — dua-duanya menjawab satu
                                 hal yang sama: ini saatnya membaca, bukan lagi
                                 memindai.

                                 Gerakannya dimatikan sepenuhnya untuk yang
                                 memasang prefers-reduced-motion; aturannya sudah
                                 ada di app.css. Saat mati, trek berhenti di
                                 translateX(0) — yaitu paruh pertama, yang memang
                                 memuat seluruh lambang. --}}
                            <div class="group flex w-max animate-marquee items-center
                                        hover:[animation-play-state:paused]">
                                @foreach([false, true] as $salinan)
                                    {{-- Paruh kedua disembunyikan dari pembaca layar:
                                         ia salinan, dan membacakan nama yang sama dua
                                         kali membuat daftar ini terdengar dua kali
                                         lebih panjang daripada isinya. --}}
                                    <ul class="flex shrink-0 items-center gap-x-12 pr-12 lg:gap-x-16 lg:pr-16"
                                        @if($salinan) aria-hidden="true" @endif>
                                @foreach($deret as $cert)
                                    @php
                                        $logoCert = $cert->getFirstMediaUrl('logos', 'thumb')
                                                 ?: $cert->getFirstMediaUrl('logos');
                                    @endphp

                                    {{-- Petak berukuran SAMA untuk tiap butir, dan itu
                                         yang membuat lambangnya terbaca sama besar.

                                         Sebelumnya cuma tingginya yang dikunci dan
                                         lebarnya dibiarkan mengalir. Secara angka itu
                                         benar — tingginya memang sama persis — tapi
                                         secara MATA tidak: lambang yang melebar jadi
                                         menguasai barisnya, sementara yang nyaris
                                         bujur sangkar tampak kecil di sebelahnya.
                                         Yang dibandingkan mata bukan tingginya
                                         melainkan luas yang ditempatinya.

                                         Petak yang sama menyamakan luas itu.
                                         object-contain di dalamnya membuat lambang
                                         melebar menyentuh sisi kiri-kanannya dan
                                         lambang meninggi menyentuh atas-bawahnya —
                                         keduanya mengisi petak yang sama tanpa
                                         satu pun terpotong atau meregang. --}}
                                    <li class="flex h-9 w-[108px] shrink-0 items-center justify-center
                                               lg:h-10 lg:w-[128px]">
                                        @if($logoCert)
                                            {{-- alt berisi namanya, bukan kosong: deretan ini
                                                 ISI, bukan hiasan — nama lembaganya persis yang
                                                 dicari pembeli, dan pembaca layar tidak bisa
                                                 menebaknya dari lambang. --}}
                                            <img src="{{ $logoCert }}" alt="{{ $cert->translated_name }}"
                                                 loading="lazy"
                                                 class="max-h-full max-w-full object-contain grayscale
                                                        transition-[filter] duration-300
                                                        group-hover:grayscale-0">
                                        @else
                                            {{-- Belum ada lambang: namanya yang jadi lambang.
                                                 Disusun dengan huruf judul, bukan huruf badan —
                                                 di deretan seperti ini nama dibaca sebagai
                                                 tanda, bukan sebagai kalimat.

                                                 Dipusatkan dan dijepit dalam petak yang sama
                                                 dengan lambang, supaya barisnya tetap rata
                                                 meski isinya campuran. --}}
                                            <span class="text-center font-site-display text-site-small
                                                         font-bold leading-tight text-ink">
                                                {{ $cert->translated_name }}
                                            </span>
                                        @endif
                                    </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                            </div>
                        </div>
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

                                {{-- Judulnya COKELAT — nilai yang sama dengan badan
                                     pil "Explore Products" tepat di sebelahnya,
                                     bukan tinta hitam seperti judul seksi lain.

                                     Kata beraksennya tidak ikut: .display em
                                     punya warnanya sendiri, jadi ia tetap aksen
                                     nada dalam dan tekanannya justru menguat —
                                     jaraknya ke badan judul melebar begitu
                                     badannya berpindah dari hitam ke cokelat.

                                     14,08:1 di atas kanvas; judul besar butuh
                                     3,0. --}}
                                <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
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

                                {{-- Bentuknya SAMA PERSIS dengan "Explore
                                     Products" di hero: pil setinggi 40px, huruf
                                     badan biasa, dan bulatan beraksen berpanah
                                     yang mengambil bantalan kanannya sendiri.

                                     Badannya yang berbeda, dan itu terpaksa.
                                     Di hero pil ini berupa kaca gelap
                                     (shade/45) — tembus, supaya foto di
                                     belakangnya tetap terbaca. Di sini
                                     belakangnya kanvas gading, dan kaca gelap
                                     di atas terang cuma menghasilkan abu keruh
                                     yang tidak menyerupai apa pun. Jadi
                                     badannya dipejalkan ke forest: putih di
                                     atasnya 14,66:1, dan tepinya sendiri
                                     14,08:1 terhadap kanvas — tombolnya
                                     terbaca sebagai tombol tanpa perlu cincin.

                                     Bulatannya tetap beraksen, bukan putih. Di
                                     seluruh halaman ini aksen berarti satu hal
                                     saja: di sinilah yang penting. --}}
                                <a href="{{ route('products.index') }}"
                                   class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('products', 'cta', 'site.cta_explore_products') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        @if($featuredProducts->isNotEmpty())
                            <ul class="mt-12 grid auto-rows-fr gap-5 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3">
                                @foreach($featuredProducts as $product)
                                    <li class="flex">
                                        <x-site.product-card :product="$product"
                                                     :label="$isi('products', 'view_label', 'site.view_details')"
                                                     class="w-full" />
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
                            {{-- Cokelat yang sama dengan judul seksi produk di
                                 atasnya — 14,08:1 di atas kanvas. Label dan
                                 keterangannya memang sudah sama sejak awal;
                                 terukur 11px Jost aksen-dalam dan 16px Inter
                                 ink-muted di kedua seksi, jadi judul ini
                                 satu-satunya yang tertinggal hitam. --}}
                            <h2 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
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
                                {{-- Bentuknya sama persis dengan "Explore Products"
                                     di seksi produk: pil pejal setinggi 40px,
                                     huruf badan biasa, dan bulatan beraksen
                                     berpanah yang mengambil bantalan kanannya
                                     sendiri.

                                     Yang digantikan adalah .btn .btn-outline —
                                     tombol bergaris berlatar tembus, sudut 4px,
                                     tinggi 44px, dan huruf aksen kapital. Empat
                                     hal yang berbeda sekaligus dari tombol seksi
                                     di atasnya, padahal keduanya mengerjakan hal
                                     yang sama: mengantar ke halaman daftarnya. --}}
                                <a href="{{ route('export-markets.index') }}"
                                   class="group inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('export-markets', 'cta', 'site.cta_explore_markets') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
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
                    /* Ikonnya dipatok di kode, judul dan keterangannya diketik
                       dari panel — jadi keduanya bisa berjalan sendiri-sendiri.
                       Yang di sini dipilih untuk isi yang SEKARANG terpasang:
                       mutu, asal, standar, dukungan. Kalau isinya nanti diganti
                       jauh, ikonnya perlu ditinjau ulang di sini. */
                    $pillars = collect(['quality', 'origin', 'standard', 'support'])
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
                                {{-- Cokelat yang sama dengan judul seksi produk dan
                                     pasar ekspor di atasnya. Label dan keterangannya
                                     memang sudah seragam sejak awal — 11px Jost
                                     aksen-dalam dan 16px Inter ink-muted di ketiga
                                     seksi — jadi judul ini satu-satunya yang masih
                                     hitam. --}}
                                <h2 class="display mt-5 max-w-[16ch] text-site-h2 text-site-forest">
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

                        {{-- ── EMPAT PILAR, MELEBAR SAAT DISOROT ──────────────
                             Yang diam ramping dan cuma menunjukkan judulnya; yang
                             disorot memuai dan membuka keterangannya. Empat kartu
                             yang semuanya terbuka sekaligus memaksa pembaca memilih
                             dari empat paragraf; empat judul memberi satu pilihan,
                             dan paragrafnya menyusul setelah dipilih.

                             HANYA di lg ke atas. Di bawah itu tidak ada kursor yang
                             bisa menyorot, jadi kartunya kembali jadi baris gulir
                             bersnap dengan keterangan yang selalu terbuka — kalau
                             tidak, isinya tidak akan pernah bisa dibaca di ponsel. --}}
                        <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                                   [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8
                                   lg:mx-0 lg:mt-14 lg:overflow-visible lg:px-0">
                            @foreach($pillars as $pillar)
                                {{-- Yang dianimasikan flex-grow, bukan width. Lebar
                                     kartu di sini hasil bagi ruang yang tersisa;
                                     menganimasikan width berarti menetapkan angka untuk
                                     tiap keadaan dan menghitungnya ulang tiap kali
                                     jumlah pilarnya berubah. Dengan flex-grow, satu
                                     kartu naik dari 1 ke 2 dan sisanya menyusut
                                     sendiri — berapa pun jumlahnya. --}}
                                <li class="group w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)]
                                           lg:w-auto lg:min-w-0 lg:shrink lg:basis-0 lg:grow
                                           lg:transition-[flex-grow] lg:duration-500 lg:ease-out
                                           lg:hover:grow-[2] lg:focus-within:grow-[2]">
                                    {{-- Isinya bertambat di KAKI kartu. Sejak
                                         keterangannya tersembunyi saat diam, keempat
                                         kartu berisi hal yang sama persis — nomor, ikon,
                                         judul — jadi tidak ada lagi kartu yang judulnya
                                         turun sendiri. Dan saat satu kartu membuka
                                         keterangannya, blok itu terdorong ke atas dari
                                         kaki: gerakannya terbaca sebagai sesuatu yang
                                         TUMBUH, bukan sebagai teks yang tiba-tiba ada.

                                         Tingginya sengaja jangkung. Jarak jauh antara
                                         nomor di puncak dan judul di kaki itu yang
                                         memberi kartu ini bentuk memanjang, sekaligus
                                         menyediakan ruang bagi keterangannya nanti tanpa
                                         kartunya berubah tinggi. --}}
                                    <div class="card relative flex h-full min-h-[256px] flex-col justify-end p-6
                                                transition-colors duration-300 hover:border-forest hover:bg-forest
                                                sm:min-h-[288px] lg:min-h-[332px]">

                                        {{-- Nomor besar yang nyaris tak terbaca, mengikuti
                                             acuan. Ia BUKAN teks yang harus dibaca — kalau
                                             cukup pekat untuk dibaca, ia jadi hal paling
                                             mencolok di kartu dan judulnya yang kalah.
                                             Tugasnya cuma menandai urutan lewat sudut mata,
                                             jadi ia dikunci aria-hidden: pembaca layar akan
                                             melafalkannya sebagai "nol satu titik" di depan
                                             tiap judul, yang tidak menambah apa pun. --}}
                                        <span aria-hidden="true" data-hias
                                              class="pointer-events-none absolute left-6 top-3 select-none
                                                     font-site-display text-[76px] font-bold leading-none tracking-[-0.04em]
                                                     text-ink/[0.07] transition-colors duration-300
                                                     group-hover:text-white/[0.13]">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                                        </span>

                                        {{-- Keping BULAT beraksen emas — rupa yang sama dengan keping kontak
                                             di kaki halaman dan di halaman Contact Us.

                                             Warnanya MEMBALIK saat disorot: bidang cokelat berlambang emas jadi
                                             bidang emas berlambang cokelat. Yang ditukar bidang dan tintanya,
                                             bukan diredupkan — jadi lambangnya sama pekatnya di kedua keadaan,
                                             6,99:1 dua-duanya.

                                             Pembalikan itu juga yang menjaganya tetap TERLIHAT. Kartu ini sendiri
                                             berubah cokelat saat disorot, dan keping cokelat akan lenyap ke dalam
                                             kartunya. --}}
                                        <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center
                                                     rounded-full bg-site-forest text-site-gilt
                                                     transition-colors duration-300
                                                     group-hover:bg-site-gilt group-hover:text-site-forest">
                                            <x-icon.pillar :name="$pillar['icon']" />
                                        </span>

                                        {{-- Rupa judulnya huruf JUDUL, sama dengan nama
                                             produk — dan itu bukan sekadar ganti kelas:
                                             font-display di lingkup .situs sengaja dipetakan
                                             ke huruf BADAN, jadi kelas itu menghasilkan
                                             Inter, bukan serifnya. Yang dipakai di sini
                                             font-site-display, kelas yang sama dengan kartu
                                             produk. Ukurannya satu langkah di bawah nama
                                             produk: 21px, bukan 26px.

                                             Warnanya cokelat judul seksi, bukan tinta
                                             badan. Empat judul kartu ini berdiri sebagai
                                             satu deret bersama judul seksi di atasnya —
                                             tinta badan menempatkannya sebagai teks
                                             biasa yang kebetulan tebal. --}}
                                        <h3 class="relative mt-5 font-site-display font-bold leading-snug tracking-[-0.01em] text-site-forest transition-colors duration-300 group-hover:text-white text-site-title">
                                            {{ $pillar['title'] }}
                                        </h3>

                                        {{-- Yang menutup keterangannya grid-rows 0fr→1fr,
                                             bukan max-height. max-height menuntut angka yang
                                             ditebak: kekecilan memotong keterangan yang
                                             panjang, kebesaran membuat separuh animasinya
                                             berjalan di ruang kosong dan geraknya terasa
                                             telat. 1fr artinya "setinggi isinya", berapa pun
                                             panjangnya.

                                             Yang disembunyikan cuma tinggi dan alfa — BUKAN
                                             display:none. Teksnya tetap ada di pohon
                                             aksesibilitas, jadi pembaca layar dan penelusur
                                             mesin pencari tetap mendapatkannya walau tidak
                                             ada kursor yang menyorot. Kartunya sendiri tidak
                                             bisa disasar papan ketik — ia bukan tautan dan
                                             bukan tombol — jadi ini satu-satunya cara isinya
                                             tidak hilang bagi yang tidak memakai tetikus.

                                             Keterangannya 16px, mengikuti keterangan SEKSI —
                                             bukan 17px kategori produk. --}}
                                        <div class="relative grid grid-rows-[1fr] transition-all duration-500 ease-out
                                                    lg:grid-rows-[0fr] lg:opacity-0
                                                    lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                                    lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                            <p class="overflow-hidden leading-relaxed text-ink-muted transition-colors duration-300 group-hover:text-white/70 text-site-body">
                                                <span class="mt-2.5 block">{{ $pillar['body'] }}</span>
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
                @php
                    $beritaBody = $isi('news', 'body', 'site.news_body');
                    $beritaUtama = $latestNews->first();
                    $beritaSisa  = $latestNews->slice(1)->take(2);
                @endphp

                {{-- Bidangnya TERANG, sama dengan tiga seksi di atasnya. --}}
                <section class="section border-t border-line">
                    <div class="shell">

                        {{-- Kepala seksi disusun persis seperti seksi produk: label
                             dan judul di kiri, keterangan dan tombolnya di kanan,
                             ditambatkan ke dasar judul. --}}
                        <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                            <div class="lg:col-span-7">
                                <p class="eyebrow">{{ $isi('news', 'eyebrow', 'site.news_eyebrow') }}</p>
                                <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                                    {!! \App\Support\Judul::sorot($isi('news', 'title', 'site.news_title')) !!}
                                </h2>
                            </div>

                            <div class="lg:col-span-5 lg:self-end">
                                @if($beritaBody !== strip_tags($beritaBody))
                                    <div class="rich max-w-[46ch]">{!! $beritaBody !!}</div>
                                @else
                                    <p class="lede max-w-[46ch]">{{ $beritaBody }}</p>
                                @endif

                                {{-- Tombolnya pindah ke sini dari kartu ajakan yang
                                     dulu berdiri di kanan. Bentuknya jadi sama persis
                                     dengan "Explore Products" — pil forest pejal
                                     setinggi 40px dengan bulatan emas berpanah — dan
                                     tidak lagi butuh cincin putih, karena sekarang ia
                                     berdiri di atas kanvas, bukan di kaki foto gelap. --}}
                                <a href="{{ route('news.index') }}"
                                   class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                          bg-site-forest text-white
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          transition-colors duration-300 hover:bg-site-brand-deep">
                                    {{ $isi('news', 'cta', 'site.cta_see_more_news') }}
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                 bg-site-gilt text-site-forest transition-transform duration-200
                                                 group-hover:translate-x-0.5">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        @if($beritaUtama)
                            {{-- Ketiga kartu memakai rupa yang SAMA dengan kartu di
                                 halaman News: tanpa bingkai, sampul berbulatan penuh
                                 di keempat sisinya, tulisan jatuh langsung di bidang
                                 halaman. Yang mengikat keduanya tinggal jaraknya.

                                 Susunannya tetap seperti sekarang — satu kartu besar
                                 di kiri, dua bertumpuk di kanan. Yang membedakan kartu
                                 utama cuma UKURAN sampul dan hurufnya.

                                 Tinggi barisnya ditentukan kolom KANAN: dua kartu
                                 bertumpuk selalu lebih jangkung daripada satu. Karena
                                 itu sampul kartu utama tidak dipatok nisbah melainkan
                                 dibiarkan memanjang mengisi sisa; kalau dipatok, sisa
                                 ruang itu jatuh ke bidang teksnya dan kartunya
                                 berlubang di bawah. --}}
                            <div class="mt-10 grid items-stretch gap-x-8 gap-y-8 lg:mt-12 lg:grid-cols-12">

                                @php
                                    $sampulUtama = $beritaUtama->getFirstMediaUrl('covers', 'webp')
                                                ?: $beritaUtama->getFirstMediaUrl('covers', 'thumb');
                                @endphp
                                <article class="group flex flex-col lg:col-span-7">
                                    <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                       class="relative block min-h-[260px] flex-1 overflow-hidden
                                              rounded-panel bg-site-paper"
                                       tabindex="-1" aria-hidden="true">
                                        @if($sampulUtama)
                                            <img src="{{ $sampulUtama }}" alt="" loading="lazy"
                                                 class="absolute inset-0 h-full w-full object-cover
                                                        transition-transform duration-500 group-hover:scale-[1.03]">
                                        @else
                                            <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-12 w-12" />
                                        @endif
                                    </a>

                                    {{-- Iramanya lebih longgar daripada kartu pendamping,
                                         dan itu yang menaikkan tinggi blok ini 300px → 350px.

                                         Bukan hiasan: tinggi blok inilah pengimbang sampul
                                         di atasnya, yang memanjang mengisi sisa tinggi baris.
                                         Tiap piksel di sini piksel yang tidak jatuh ke
                                         sampul. Kartu ini juga memang berhak beriramanya
                                         sendiri — ia dua kali lebih lebar daripada kartu di
                                         seberangnya. --}}
                                    <div class="flex flex-col pt-6">
                                        {{-- Kategori dan tanggal berbagi satu baris di ATAS
                                             judul: keduanya keterangan tentang artikelnya,
                                             bukan bagian dari judulnya. --}}
                                        <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1.5">
                                            @if($beritaUtama->category)
                                                <p class="eyebrow">{{ $beritaUtama->category->name }}</p>
                                            @else
                                                <span></span>
                                            @endif

                                            @if($beritaUtama->published_at)
                                                <time datetime="{{ $beritaUtama->published_at->toDateString() }}"
                                                      class="shrink-0 text-ink-muted text-site-micro">
                                                    {{ $beritaUtama->published_at->translatedFormat('d M Y') }}
                                                </time>
                                            @endif
                                        </div>

                                        {{-- min-h dalam satuan em, bukan piksel: em di sini
                                             ukuran huruf judulnya sendiri, dan ukuran itu
                                             sebuah clamp yang menyusut di layar sempit.
                                             2,75em = dua baris pada leading-snug. --}}
                                        <h3 class="mt-5 min-h-[2.75em] font-site-display text-site-h3 font-bold
                                                   leading-snug tracking-[-0.015em] text-site-forest">
                                            <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                               class="transition-colors hover:text-site-gilt-deep">
                                                {{ $beritaUtama->translated_title }}
                                            </a>
                                        </h3>

                                        {{-- Empat baris, dipatok mati lewat min-h — bukan
                                             sekadar dibatasi jumlah hurufnya.

                                             Tinggi sampul kartu ini BUKAN nisbah melainkan
                                             sisa: ia memanjang mengisi apa pun yang tidak
                                             dipakai tulisan. Jadi tiap baris yang ditambahkan
                                             di sini memendekkan sampulnya satu baris pula,
                                             dan itulah tuas yang menyeimbangkan keduanya.

                                             Lima baris, dan itu batas jujurnya: ringkasan
                                             terpanjang di basis data 347 huruf, yang pada
                                             lebar ini persis lima baris. Baris keenam akan
                                             kosong pada SEMUA artikel yang ada.

                                             min-h, supaya artikel berringkasan pendek tetap
                                             memesan lima baris dan nisbah itu tidak berubah
                                             dari satu artikel ke artikel lain. --}}
                                        @if($beritaUtama->translated_excerpt)
                                            <p class="mt-5 line-clamp-5 min-h-[8.125em] leading-relaxed
                                                      text-ink-muted text-site-lede">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($beritaUtama->translated_excerpt), 380) }}
                                            </p>
                                        @endif

                                        {{-- Tautan baca berdiri SENDIRI di kiri, di bawah
                                             ringkasannya — sama dengan halaman News. Yang
                                             lama mendorongnya ke kanan sebaris dengan
                                             tanggal; sekarang tanggalnya sudah naik ke atas
                                             judul, dan yang tersisa di baris itu cuma satu
                                             unsur. --}}
                                        <a href="{{ route('news.show', $beritaUtama->slug) }}"
                                           class="mt-10 inline-flex w-max items-center gap-2
                                                  font-site-body text-site-small font-semibold
                                                  text-site-forest transition-colors hover:text-site-gilt-deep">
                                            {{ $isi('news', 'read_label', 'site.read_article') }}
                                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                                 viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </article>

                                <div class="flex flex-col gap-8 lg:col-span-5">
                                    @foreach($beritaSisa as $article)
                                        @php
                                            $sampul = $article->getFirstMediaUrl('covers', 'webp')
                                                   ?: $article->getFirstMediaUrl('covers', 'thumb');
                                        @endphp

                                        <article class="group flex flex-col">
                                            {{-- Sampul pendamping 5:2 — 188px, kira-kira
                                                 setinggi blok tulisan 186px di bawahnya.

                                                 Nisbah ini muncul dua kali di kolom kanan,
                                                 dan kolom kanan itulah yang menetapkan tinggi
                                                 baris yang harus diisi kartu kiri. Jadi
                                                 memanjangkan sampul di sini memanjangkan
                                                 sampul kartu utama DUA KALI lipatnya. Yang
                                                 mengimbanginya blok tulisan kartu utama yang
                                                 ikut ditinggikan; tanpa itu, 5:2 di sini
                                                 melemparkan kartu utama ke 1,59. --}}
                                            <a href="{{ route('news.show', $article->slug) }}"
                                               class="relative block aspect-[5/2] w-full shrink-0 overflow-hidden
                                                      rounded-panel bg-site-paper"
                                               tabindex="-1" aria-hidden="true">
                                                @if($sampul)
                                                    <img src="{{ $sampul }}" alt="" loading="lazy"
                                                         class="absolute inset-0 h-full w-full object-cover
                                                                transition-transform duration-500 group-hover:scale-[1.03]">
                                                @else
                                                    <x-site.image-placeholder class="absolute inset-0 h-full w-full" icon="h-9 w-9" />
                                                @endif
                                            </a>

                                            <div class="flex flex-1 flex-col pt-4">
                                                <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1.5">
                                                    @if($article->category)
                                                        <p class="eyebrow">{{ $article->category->name }}</p>
                                                    @else
                                                        <span></span>
                                                    @endif

                                                    @if($article->published_at)
                                                        <time datetime="{{ $article->published_at->toDateString() }}"
                                                              class="shrink-0 text-ink-muted text-site-micro">
                                                            {{ $article->published_at->translatedFormat('d M Y') }}
                                                        </time>
                                                    @endif
                                                </div>

                                                <h3 class="mt-2.5 line-clamp-2 min-h-[2.75em] font-site-display
                                                           text-site-title font-bold leading-snug
                                                           tracking-[-0.01em] text-site-forest">
                                                    <a href="{{ route('news.show', $article->slug) }}"
                                                       class="transition-colors hover:text-site-gilt-deep">
                                                        {{ $article->translated_title }}
                                                    </a>
                                                </h3>

                                                {{-- Ringkasan DUA baris, bukan tiga seperti di
                                                     halaman News. Tiap baris di sini dibayar
                                                     dua kali — kartu ini muncul dua kali —
                                                     lalu menyeret tinggi kartu utama ikut
                                                     naik. Baris ketiga saja menambah 42px ke
                                                     kolom kanan dan mendorong nisbah kartu
                                                     utama dari 1,25 ke 1,37. --}}
                                                @if($article->translated_excerpt)
                                                    <p class="mt-2 line-clamp-2 min-h-[3.25em] leading-relaxed
                                                              text-ink-muted text-site-small">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($article->translated_excerpt), 120) }}
                                                    </p>
                                                @endif

                                                <a href="{{ route('news.show', $article->slug) }}"
                                                   class="mt-auto inline-flex w-max items-center gap-2 pt-4
                                                          font-site-body text-site-small font-semibold
                                                          text-site-forest transition-colors hover:text-site-gilt-deep">
                                                    {{ $isi('news', 'read_label', 'site.read_article') }}
                                                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
                                                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="lede mt-12 rounded-corner border border-dashed border-line px-6 py-14 text-center">
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
                        <div class="relative isolate overflow-hidden rounded-panel bg-site-forest px-6 py-16 text-center sm:px-10 md:py-20 lg:py-24">

                            @if($ctaImage)
                                <img src="{{ $ctaImage }}" alt="" aria-hidden="true" loading="lazy"
                                     class="absolute inset-0 -z-10 h-full w-full object-cover">

                                {{-- Peredam 0,75 — turun dari 0,85, dan berhenti di
                                     situ karena kata beraksennya, bukan karena
                                     keterangannya.

                                     Teks putih longgar: pada 0,75 ia dapat 5,90:1 di
                                     atas foto PUTIH POLOS sekalipun. Yang menahan
                                     angka ini kata beraksen emas — ia teks berwarna,
                                     bukan putih, jadi jarak terangnya ke latar jauh
                                     lebih tipis. Terhadap foto putih ia dapat 3,03:1,
                                     tepat di atas ambang teks besar; pada 0,70 sudah
                                     2,64:1 dan gagal.

                                     Terhadap foto yang SEKARANG terpasang jaraknya
                                     jauh lebih lega — 4,4:1 pada seperseratus bagian
                                     paling terang di belakang judulnya — tapi foto itu
                                     bisa diganti kapan saja dari panel, jadi yang
                                     dipatok kasus terburuknya.

                                     Yang dibayar: keterangannya kini PUTIH PENUH,
                                     bukan putih 70%. Putih 70% baru lulus pada peredam
                                     0,78, dan pada angka itu fotonya nyaris tidak
                                     bergerak dari yang lama. Tingkatannya sekarang
                                     dijaga ukuran dan bobot huruf, bukan alfa. --}}
                                <div class="absolute inset-0 -z-10 bg-site-forest/75" aria-hidden="true"></div>
                            @endif

                            <h2 class="display display-invert mx-auto max-w-[18ch] text-site-h2">
                                {!! \App\Support\Judul::sorot($isi('contact', 'title', 'site.cta_title')) !!}
                            </h2>

                            @if($kontakBody !== strip_tags($kontakBody))
                                {{-- [&_p]:text-white, bukan sekadar text-white di pembungkusnya:
                                     .rich-invert p sudah memasang white/70 sendiri, dan
                                     warna anak selalu menang atas warisan induknya. --}}
                                <div class="rich rich-invert mx-auto mt-6 max-w-[56ch] [&_p]:text-white [&_ul]:text-white [&_ol]:text-white">{!! $kontakBody !!}</div>
                            @else
                                <p class="mx-auto mt-6 max-w-[56ch] leading-relaxed text-white text-site-body">
                                    {{ $kontakBody }}
                                </p>
                            @endif

                            {{-- Kedua tombol memakai rupa yang sama persis dengan
                                 sepasang tombol di hero: yang pertama pil aksen pejal
                                 berhuruf gelap, yang kedua kaca gelap bercincin putih.

                                 Keduanya sama-sama berdiri di atas foto beredam, jadi
                                 rupa itu memang lahir untuk keadaan ini — pil pejal
                                 tidak bergantung pada foto di belakangnya, dan kaca
                                 gelap justru menumpang peredam yang sudah ada.

                                 Yang lama .btn-pill dan .btn-outline-invert: sudut 4px,
                                 tinggi 44px, huruf aksen kapital. Tiga hal berbeda
                                 sekaligus dari tombol hero, padahal keduanya meminta
                                 tindakan yang sama. --}}
                            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('inquiry.index') }}"
                                   class="inline-flex h-10 items-center rounded-full bg-site-gilt px-6
                                          font-site-body text-site-small font-semibold whitespace-nowrap
                                          text-site-forest ring-1 ring-site-gilt-deep/70
                                          shadow-[0_2px_10px_-2px_rgba(11,13,12,0.45)]
                                          transition-colors duration-300 hover:bg-site-gilt-soft">
                                    {{ $isi('contact', 'cta_primary', 'site.cta_request_quote') }}
                                </a>

                                @if($waLink)
                                    {{-- Lambang WhatsApp berdiri di kiri tulisan, bukan
                                         di dalam bulatan aksen seperti panah di hero.
                                         Bulatan itu menandai ARAH — lanjut ke halaman
                                         berikutnya; lambang merek menandai KE MANA, dan
                                         menaruhnya di dalam bulatan aksen akan membuat
                                         tombol kedua terbaca sepenting yang pertama. --}}
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex h-10 items-center gap-2.5 rounded-full px-5
                                              font-site-body text-site-small font-semibold whitespace-nowrap
                                              bg-site-shade/45 text-white ring-1 ring-white/25
                                              transition-colors duration-300 hover:bg-site-shade/65">
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
