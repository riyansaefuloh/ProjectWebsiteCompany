@props([
    'markets',

    'detailed' => false,

    'showList' => true,
])

@php
    // ── PROYEKSI ─────────────────────────────────────────────────────────
    $R = 6381372.0;
    $centralMeridian = 11.5;
    $bboxX0 = -20004297.151525836;
    $bboxY0 = -12671671.123330014;
    $mapW = 900.0;
    $mapH = 440.70631074413296;
    $scale = $mapW / (20026572.39474939 - $bboxX0);

    $coordinates = config('country-coordinates', []);

    $markers = [];
    foreach ($markets as $market) {
        $point = $coordinates[strtoupper($market->country_code)] ?? null;

        if (! $point) {
            continue;
        }

        [$lat, $lng] = $point;

        $lngDelta = $lng - $centralMeridian;
        while ($lngDelta < -180) { $lngDelta += 360; }
        while ($lngDelta > 180) { $lngDelta -= 360; }

        $x = $R * $lngDelta * (M_PI / 180);
        $y = -$R * log(tan((45 + 0.4 * $lat) * (M_PI / 180))) / 0.8;

        $kode = strtoupper($market->country_code);

        /*
         * Tempat kartunya diambil dari config/country-callouts.php dan diubah
         * jadi persen di sini, sekali — supaya bladenya tidak mengulang
         * pembagian yang sama sepuluh kali, dan supaya satuan yang dipakai
         * garis penghubung (viewBox) dan yang dipakai kartu (persen CSS)
         * berasal dari angka yang sama.
         */
        $titikKartu = config('country-callouts.' . $kode);

        $markers[] = [
            'name'   => $market->translated_name,
            'region' => $market->region,
            'code'   => $kode,
            'left'   => round((($x - $bboxX0) * $scale) / $mapW * 100, 3),
            'top'    => round((($y - $bboxY0) * $scale) / $mapH * 100, 3),
            'petaX'  => round((($x - $bboxX0) * $scale), 3),
            'petaY'  => round((($y - $bboxY0) * $scale), 3),
            'kartu'  => $titikKartu ? [
                'x'       => $titikKartu['x'],
                'y'       => $titikKartu['y'],
                'kiri'    => round($titikKartu['x'] / $mapW * 100, 3),
                'atas'    => round($titikKartu['y'] / $mapH * 100, 3),
            ] : null,
        ];
    }

    $byRegion = $markets->groupBy('region');

    /*
     * BENTUK negara tujuan, untuk lapisan sorot di atas peta.
     *
     * Petanya sebuah <img>, dan negara di dalam berkas gambar tidak bisa
     * disentuh satu per satu. Lapisan SVG tipis di atasnya menyediakan itu:
     * isinya cuma bentuk negara yang benar-benar jadi tujuan — sembilan, bukan
     * 176 — dengan viewBox yang sama persis, jadi tiap bentuk jatuh tepat di
     * atas negaranya tanpa perlu digeser.
     *
     * Yang tidak punya bentuk dilewati begitu saja. Singapura terlalu kecil
     * untuk digambar pada tingkat penyederhanaan peta ini; penandanya tetap
     * menyala saat disorot, yang tidak terjadi cuma isian warnanya.
     */
    $bentukNegara = collect(config('country-shapes', []))
        ->only(collect($markers)->pluck('code'))
        ->all();

@endphp

{{-- DUA keadaan, bukan satu.

     tunjuk = negara yang sedang dilewati tetikus. kunci = negara yang DITEKAN,
     dan bertahan sampai ditekan lagi. Yang menyala adalah kunci kalau ada,
     tunjuk kalau tidak.

     Sebabnya bukan kerapian. Sebelumnya seluruh peta ini cuma menjawab
     hover — dan di layar sentuh, hover tidak pernah terjadi. Setengah isi
     halaman ini, sepuluh nama negara beserta kawasannya, tidak bisa dicapai
     sama sekali dari ponsel. Menekan juga yang membuat satu negara bisa
     DITAHAN menyala sementara matanya berpindah ke daftar di bawah peta;
     dengan hover saja, melepas tetikus berarti kehilangan tempatnya.

     Penunjuk dan penekan menulis ke tempat yang berbeda supaya keduanya tidak
     saling menghapus: melewati negara lain tidak boleh membatalkan yang sedang
     dikunci, dan meninggalkan peta tidak boleh memadamkannya. --}}
<div x-data="{
        tunjuk: null,
        kunci: null,
        get aktif() { return this.kunci ?? this.tunjuk },
        tekan(kode) { this.kunci = this.kunci === kode ? null : kode },
     }">
    {{-- ── PETA BERDARAT POLOS ────────────────────────────────────────────
         Berkasnya world-map.svg, dan sekarang ia satu-satunya peta di proyek
         ini: halaman masuk panel dulu memakai world-dotted.svg sebagai masker,
         tapi sudah ikut memakai berkas ini. world-dotted.svg tertinggal di
         resources/images tanpa satu pun yang merujuknya.

         Berisi 176 lintasan negara. Daratnya PUTIH pejal di atas
         kanvas halaman yang sedikit lebih hangat, jadi benua terbaca sebagai
         bidang tanpa perlu warna atau tekstur — dan negara yang disorot punya
         nada yang paling jauh untuk dituju.

         Garis batasnya TIDAK digambar dengan menggores tiap negara. Tiap negara
         di berkas ini lintasan tertutupnya sendiri yang berdiri terpisah, dan
         menggoresnya satu per satu berarti tiap batas darat dapat DUA garis —
         satu dari masing-masing tetangga — sementara garis pantai cuma dapat
         satu. Yang digambar adalah SELISIH DUA BIDANG: benua dimekarkan 0,88
         unit lalu negara aslinya dilubangkan kembali dari situ, jadi yang
         tersisa cincin selebar 0,78 unit yang sama tebalnya di batas darat
         maupun di garis pantai.

         Lubangnya dimekarkan 0,10 unit, bukan nol. Tanpa itu selanya sedikit
         lebih lebar daripada cincinnya dan pitanya melar; dengan pemekaran yang
         terlalu besar — 0,42 sudah cukup — sela sempit tertutup rapat dan batas
         negaranya HILANG sama sekali, tinggal garis pantai. Nilainya diukur
         dari selanya, bukan dikira-kira.

         Masker daratnya memakai pemekaran 0,10 yang sama. Kalau tidak, putihnya
         berhenti di tepi negara asli dan menyisakan halo kanvas setipis rambut
         di dalam tiap garis batas. --}}
    {{-- Petanya berdiri TELANJANG di atas bidang halaman, tanpa bingkai —
         rupa yang sama dengan bagian pasar ekspor di beranda.

         Sempat dikurung satu panel bersama daftarnya, dengan maksud menyatakan
         bahwa keduanya satu alat. Yang dihasilkan justru sebaliknya: bingkai
         mengubah peta jadi gambar yang dipajang di dalam kotak, padahal
         benuanya sudah punya garis tepinya sendiri dan tidak butuh garis kedua
         di sekelilingnya. Yang mengikat peta dengan daftarnya bukan bingkai,
         melainkan sorotnya — menunjuk satu nama menyalakan satu negara, dan itu
         sudah cukup dikatakan tanpa satu garis pun.

         Peta TIDAK lagi disembunyikan di layar kecil.

         Ia isi pokok halaman ini, dan menyembunyikannya di bawah md berarti
         pengunjung ponsel — bagian terbesar — mendapat halaman yang isinya
         cuma daftar nama. Yang menyusut cuma ukuran penandanya; jangkauannya
         tetap terbaca sekali pandang, dan itu yang dicari orang di halaman
         ini. --}}
    <div class="relative aspect-[900/441] w-full {{ $showList ? '' : 'hidden md:block' }}">
        <img src="{{ asset('images/world-map.svg') }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 h-full w-full select-none">

        {{-- ── LAPISAN SOROT ───────────────────────────────────────────────
             Bentuknya digambar sejak awal tapi tembus pandang, bukan
             ditambahkan saat disorot: menyisipkan lintasan sepanjang dua ribu
             titik ke dalam pohon dokumen di tengah gerakan tetikus menghasilkan
             jeda yang terasa, dan yang berubah cuma satu angka.

             opacity ditulis sebagai atribut statis 0 DAN diikat: sebelum Alpine
             sempat berjalan, atribut yang tidak ada berarti opacity 1 — dan
             sembilan negara akan berkedip pekat sekaligus saat halaman dimuat.

             pointer-events-none supaya lapisan ini tidak menghalangi penanda
             yang duduk di atasnya. --}}
        <svg viewBox="0 0 900 440.70631074413296" aria-hidden="true"
             class="pointer-events-none absolute inset-0 h-full w-full select-none">
            {{-- Negara yang menyala diisi BULATAN, bukan bidang pejal.

                 Petanya sendiri kisi bulatan; siluet utuh di atasnya berbicara
                 dengan bahasa yang lain, dan yang terlihat bukan "negara ini
                 yang dimaksud" melainkan "ada sesuatu yang ditempelkan di atas
                 peta".

                 Bulatannya digambar sebagai POLA, bukan sebagai ratusan
                 lingkaran per negara. Satu pola berukuran satu petak diulang
                 oleh peramban, dan sembilan negara memakainya bersama —
                 alternatifnya menyalin sekitar 1.200 lingkaran ke dalam HTML
                 tiap kali halaman ini digambar, dan HTML tidak disimpan
                 singgahan seperti berkas peta.

                 Petaknya 5 unit dengan pusat di 2,5 — angka yang sama dengan
                 kisi peta. Karena itu bulatan yang menyala jatuh TEPAT di atas
                 bulatan yang digantikannya, bukan bergeser setengah petak. --}}
            <defs>
                <pattern id="sorot-titik" width="5" height="5" patternUnits="userSpaceOnUse">
                    <circle cx="2.5" cy="2.5" r="1.98" fill="#332619"/>
                </pattern>
            </defs>

            {{-- Isian pola DITAMBAH bulatan yang menyusuri tepinya.

                 Isian saja cukup untuk negara seluas Australia, tapi tidak untuk
                 Belanda: negara yang lebih kecil daripada satu petak kisi tidak
                 memuat satu bulatan pun, dan yang menyala di sana tidak
                 tergambar sama sekali.

                 Goresan berputus berujung bulat menyusuri garis tepinya dengan
                 jarak 5 unit — irama yang sama dengan kisinya. Pada negara besar
                 bulatan tepi ini melebur ke dalam isiannya dan tidak terlihat;
                 pada negara kecil, ia satu-satunya yang tergambar, dan bentuknya
                 tetap terbaca sebagai deretan bulatan, bukan garis. --}}
            @foreach($bentukNegara as $kode => $bentuk)
                <path d="{{ $bentuk }}" fill="url(#sorot-titik)"
                      stroke="#332619" stroke-width="1.9" stroke-linecap="round"
                      stroke-dasharray="0.01 5" opacity="0"
                      class="transition-opacity duration-200"
                      x-bind:opacity="aktif === '{{ $kode }}' ? '1' : '0'"/>
            @endforeach

            {{-- Garis penghubung digambar DI DALAM lapisan yang sama dengan
                 bentuk negaranya, bukan sebagai batang HTML di sebelah
                 penandanya.

                 Alasannya satuan: ujung garis yang satu ada di titik negaranya
                 dan ujung yang lain di titik kartunya, dan keduanya dinyatakan
                 dalam viewBox peta. Menariknya dengan kotak HTML berarti
                 menghitung ulang panjang dan sudutnya dari persen — dan
                 hasilnya berubah tiap kali lebar petanya berubah. Di dalam SVG,
                 peramban yang menskalakannya.

                 Kartunya digambar SESUDAH lapisan ini dan berbidang pejal, jadi
                 garis yang ditarik sampai ke pusat kartu terlihat berhenti di
                 tepinya. Tidak ada pemotongan yang perlu dihitung. --}}
            @foreach($markers as $marker)
                @if($marker['kartu'])
                    <line x1="{{ $marker['petaX'] }}" y1="{{ $marker['petaY'] }}"
                          x2="{{ $marker['kartu']['x'] }}" y2="{{ $marker['kartu']['y'] }}"
                          stroke="#332619" stroke-width="0.9" stroke-linecap="round" opacity="0"
                          class="transition-opacity duration-200"
                          x-bind:opacity="aktif === '{{ $marker['code'] }}' ? '0.75' : '0'"/>
                @endif
            @endforeach
        </svg>

        @foreach($markers as $marker)
            <div class="group absolute -translate-x-1/2 -translate-y-1/2 {{ $showList ? '' : 'hidden md:block' }}"
                 style="left: {{ $marker['left'] }}%; top: {{ $marker['top'] }}%;"
                 x-on:mouseenter="tunjuk = '{{ $marker['code'] }}'"
                 x-on:mouseleave="tunjuk = null">

                {{-- Bulatannya DIAM di negaranya, tidak beranjak ke mana-mana.

                     Garis penghubungnya digambar di lapisan SVG di atas — dari
                     titik ini ke titik kartunya — jadi tidak ada lagi batang
                     tegak yang perlu ditumbuhkan dari sini.

                     Tombolnya bidang 24px, jauh lebih besar daripada bulatan
                     yang terlihat: penanda peta adalah sasaran kecil, dan
                     sasaran sentuh sebesar gambarnya sendiri menuntut ketepatan
                     yang tidak wajar. Ukuran itu TIDAK ikut menyusut di layar
                     kecil — justru di sana ia disentuh jari, bukan ditunjuk
                     tetikus. Yang menyusut cuma bulatannya, dari 12px ke 10px,
                     karena di peta selebar 390px sepuluh bulatan 12px berdesakan
                     jadi satu gumpalan di Eropa. --}}
                <button type="button"
                        x-on:click="tekan('{{ $marker['code'] }}')"
                        x-on:focus="tunjuk = '{{ $marker['code'] }}'"
                        x-on:blur="tunjuk = null"
                        x-bind:aria-pressed="kunci === '{{ $marker['code'] }}' ? 'true' : 'false'"
                        class="relative block h-6 w-6 focus:outline-none">
                    <span class="sr-only">{{ $marker['name'] }} — {{ $marker['region'] }}</span>

                    {{-- Berbalik EMAS saat negaranya menyala.

                         Isian sorotnya cokelat pekat, warna yang sama dengan
                         bulatan ini sendiri — dan bulatan itu berdiri tepat di
                         tengah bentuk yang baru saja diwarnai. Tanpa pembalikan,
                         ia lenyap ke dalam bentuk yang ditandainya.

                         Ditulis sebagai gaya sebaris, bukan kelas terikat: dua
                         kelas latar pada satu unsur diputuskan oleh urutannya di
                         dalam berkas CSS, bukan oleh urutan penulisannya di
                         sini. Gaya sebaris selalu menang. --}}
                    <span aria-hidden="true"
                          x-bind:style="aktif === '{{ $marker['code'] }}'
                              ? 'background-color:#e2a862'
                              : ''"
                          class="absolute left-1/2 top-1/2 block h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2
                                 rounded-full bg-site-forest ring-[3px] ring-site-gilt-deep/30
                                 transition-transform duration-200
                                 group-hover:scale-110 group-focus-within:scale-110
                                 sm:h-3 sm:w-3 sm:ring-4"></span>
                </button>

            </div>
        @endforeach

        {{-- ── KARTU KETERANGAN, DI RUANG KOSONG TERDEKAT ──────────────────
             Tiap negara punya tempat kartunya sendiri, dan tempat itu dicari,
             bukan ditaruh dengan mata: untuk tiap negara dicoba titik-titik di
             sekelilingnya mulai dari jarak terdekat, dan yang diambil yang
             pertama menghasilkan kotak 128x50 unit yang SELURUHNYA laut.
             Angkanya ada di config/country-callouts.php.

             Sebabnya negara tujuan tidak tersebar merata. Empat berdesakan di
             Eropa Barat dan dua lagi berjarak dua ratus kilometer di Asia
             Timur; kartu yang muncul begitu saja di atas penandanya akan
             menimbun tetangganya — justru negara-negara yang sedang ingin
             dilihat.

             Lebarnya 14,2% dari peta, persis selebar kotak yang diuji. Kalau ia
             dipatok dalam piksel, kotak yang terbukti kosong pada satu lebar
             layar akan meleber ke daratan pada lebar yang lain.

             Yang tidak punya tempat di config tidak digambar sama sekali —
             penandanya tetap bekerja dan negaranya tetap menyala. Kartu di
             tempat karangan lebih buruk daripada tidak ada kartu: ia akan
             berdiri di atas negara lain. --}}
        @foreach($markers as $marker)
            @if($marker['kartu'])
                {{-- Kartunya SEWARNA dengan negaranya: forest pejal.

                     Berbidang kanvas, ia nyaris tidak terlihat — kanvas kartu,
                     kanvas laut, dan #f6efe3 daratan cuma terpaut 1,1:1 satu
                     sama lain, dan yang memisahkannya dari peta tinggal garis
                     tepi setipis rambut. Di peta yang seluruhnya bernada terang,
                     satu-satunya cara sebuah benda menonjol adalah dengan
                     menjadi gelap.

                     Dan warnanya bukan gelap sembarang: ia forest yang sama
                     dengan isian negara yang sedang disorot. Keduanya jadi
                     terbaca sebagai satu benda yang dihubungkan seutas garis,
                     bukan sebagai negara berwarna yang kebetulan berdampingan
                     dengan sebuah kotak. --}}
                {{-- Kartunya cuma digambar dari lg ke atas.

                     Lebarnya 14,2% dari peta — angka yang diuji supaya kotaknya
                     jatuh di laut. Di bawah lg, 14,2% itu tinggal seratus piksel
                     dan min-w-[8rem] memaksanya melebar sampai seperlima peta,
                     jauh melewati kotak yang terbukti kosong. Di sana daftar di
                     bawah petanya yang memikul tugas menyebut nama: menekan satu
                     penanda menggarisbawahi negaranya di sana. --}}
                <div class="pointer-events-none absolute z-10 hidden w-[14.2%] min-w-[8rem]
                            -translate-x-1/2 -translate-y-1/2 rounded-control
                            bg-site-forest px-3.5 py-3 text-center lg:block
                            shadow-[0_20px_44px_-16px_rgba(51,38,25,0.55)]
                            transition-opacity duration-200"
                     style="left: {{ $marker['kartu']['kiri'] }}%; top: {{ $marker['kartu']['atas'] }}%; opacity: 0"
                     {{-- Ikatannya berupa OBJEK, bukan untai.

                          x-bind:style yang diberi untai mengganti seluruh atribut
                          style, dan letak kartunya ikut terhapus bersamanya —
                          kartu yang seharusnya berdiri di Atlantik Utara berakhir
                          di sudut kiri atas peta. Bentuk objek menyetel
                          propertinya satu per satu dan meninggalkan left dan top
                          apa adanya. --}}
                     x-bind:style="{ opacity: aktif === '{{ $marker['code'] }}' ? 1 : 0 }"
                     aria-hidden="true">
                    <span class="block font-site-display font-bold leading-snug text-white text-site-small">
                        {{ $marker['name'] }}
                    </span>

                    {{-- Emas nada terang, bukan nada dalam: gilt-deep dipilih
                         untuk bidang terang dan terbaca 1,4:1 di atas forest.
                         Nada terangnya 6,99:1. --}}
                    <span class="mt-1 block font-site-accent text-site-micro font-medium uppercase
                                 leading-tight tracking-[0.12em] text-site-gilt">
                        {{ $marker['region'] }}
                    </span>

                </div>
            @endif
        @endforeach
    </div>

    {{-- ── DAFTAR NEGARA PER KAWASAN ───────────────────────────────────
         Dua rupa yang berbeda untuk dua tempat yang berbeda.

         Di beranda ($detailed = false) yang dibutuhkan cuma nama-namanya:
         empat kolom nama, tanpa keterangan, karena bagian itu sekadar
         mengumumkan jangkauan sebelum mengajak ke halaman ini.

         Di halaman ini ia berdiri sebagai DAFTAR ISI di bawah petanya. --}}
    @if($showList)
        @if($detailed)
            {{-- Sebuah REGISTER, bukan kisi keping.

                 Yang sebelumnya di sini: lima lajur bersekat, tiap negara jadi
                 keping berbingkai dengan kode di dalam petak sendiri. Rapi,
                 tapi rapi dengan cara yang salah — sepuluh benda seukuran sama
                 berjajar di kisi yang sama, dan tidak ada satu pun yang
                 menyatakan mana yang penting. Bentuk seperti itu memang muncul
                 sendiri kalau yang dikerjakan "taruh daftar di dalam kotak".

                 Yang dipakai sekarang bentuk yang lebih tua: baris daftar isi.
                 Tiap kawasan satu baris selebar halaman, dipisah garis rambut,
                 dengan nomor urut kecil di pinggir kiri — perangkat yang sama
                 dengan daftar isi buku dan manifes muatan, dan kebetulan
                 memang itu yang sedang didaftar di sini.

                 Nama kawasan naik jadi judul sungguhan: Fraunces 26px, huruf
                 biasa, bukan kapital berjarak 13px. Kapital berjarak adalah
                 rupa LABEL di situs ini, dan label mengumumkan bagian; kawasan
                 di sini bukan pengumuman, ia isi.

                 Negaranya kembali jadi TEKS, bukan keping. Nama yang duduk di
                 dalam bingkai berlatar menuntut dibaca satu per satu; nama yang
                 mengalir sebagai teks terbaca sebagai satu deretan — dan
                 deretan itulah yang sebenarnya ingin disampaikan. Kodenya
                 menyusut jadi dua huruf emas di sebelah namanya, cukup untuk
                 menghubungkannya dengan penanda di peta, tidak cukup untuk
                 bersaing dengan namanya. --}}
            {{-- TIGA KARTU sejajar, satu per kawasan.

                 Yang sebelumnya di sini register selebar halaman: dua lajur
                 bersekat garis, kawasan demi kawasan ke bawah. Bentuk itu benar
                 untuk daftar yang panjang — tapi yang didaftar cuma lima
                 kawasan berisi sepuluh negara, dan register menyebarkannya
                 sepanjang layar penuh untuk isi yang sebenarnya muat dalam dua
                 baris kartu.

                 Kartu juga yang membuat tiap kawasan jadi benda utuh: judul,
                 jumlah, dan negaranya terkurung satu bingkai, jadi tidak ada
                 lagi pertanyaan negara mana milik kawasan mana — pertanyaan
                 yang di register dijawab oleh kedekatan saja.

                 Bingkainya garis rambut di atas kanvas, bukan bidang berwarna:
                 halaman ini kanvas, dan lima bidang paper di atasnya cuma
                 menghasilkan dua nada krem yang saling mengeruhkan. --}}
            <div class="mt-10 grid gap-5 sm:grid-cols-2 md:mt-14 lg:grid-cols-3">
                @foreach($byRegion as $region => $countries)
                    <div class="flex flex-col rounded-corner border border-line bg-site-canvas p-5">

                        <div>
                            <h3 class="display text-site-title text-site-forest">{{ $region }}</h3>

                            <p class="eyebrow mt-1">
                                {{ trans_choice('site.countries_count', $countries->count(), ['count' => $countries->count()]) }}
                            </p>
                        </div>

                        {{-- Sekat rambut memisahkan kepala kartu dari isinya.
                             auto-rows-fr TIDAK dipakai: ia menyamakan tinggi
                             SELURUH baris, jadi baris kedua — yang isinya dua
                             kartu bernegara satu — ikut setinggi baris pertama
                             yang memuat tiga pil dalam dua baris, dan separuh
                             kartunya jadi ruang kosong. Tanpa itu, kisi tetap
                             menyamakan tinggi kartu DI DALAM satu baris, yang
                             memang yang dibutuhkan. --}}
                        <div class="mt-4 border-t border-line pt-4">
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach($countries as $country)
                                    @php $k = $country->country_code; @endphp

                                    <li>
                                        {{-- Pil dua nada: kode beraksen emas di kiri,
                                             dipisah sekat rambut dari namanya.

                                             Kodenya bukan hiasan — ia yang
                                             menghubungkan tiap nama di sini dengan
                                             penanda di peta tepat di atasnya.

                                             Warnanya saat menyala ditukar lewat GAYA,
                                             bukan kelas: dua kelas latar pada satu
                                             unsur diputuskan oleh urutannya di dalam
                                             berkas CSS, bukan oleh urutan penulisannya
                                             di sini. --}}
                                        <button type="button"
                                                x-on:mouseenter="tunjuk = '{{ $k }}'"
                                                x-on:mouseleave="tunjuk = null"
                                                x-on:focus="tunjuk = '{{ $k }}'"
                                                x-on:blur="tunjuk = null"
                                                x-on:click="tekan('{{ $k }}')"
                                                x-bind:aria-pressed="kunci === '{{ $k }}' ? 'true' : 'false'"
                                                x-bind:style="aktif === '{{ $k }}'
                                                    ? { backgroundColor: '#332619', borderColor: '#332619' }
                                                    : { backgroundColor: '', borderColor: '' }"
                                                class="inline-flex items-center gap-2 rounded-full border border-line
                                                       bg-site-canvas py-1 pl-2 pr-3
                                                       transition-colors duration-200 focus:outline-none
                                                       focus-visible:ring-2 focus-visible:ring-site-gilt-deep/60
                                                       focus-visible:ring-offset-2 focus-visible:ring-offset-site-canvas">
                                            <span x-bind:style="aktif === '{{ $k }}'
                                                      ? { color: '#e2a862', borderColor: 'rgba(255,255,255,0.28)' }
                                                      : { color: '', borderColor: '' }"
                                                  class="shrink-0 border-r border-line pr-2 font-site-accent
                                                         text-site-micro font-semibold uppercase tracking-[0.08em]
                                                         text-site-gilt-deep transition-colors duration-200"
                                                  aria-hidden="true">
                                                {{ $k }}
                                            </span>

                                            <span x-bind:style="aktif === '{{ $k }}' ? { color: '#ffffff' } : { color: '' }"
                                                  class="font-site-body text-site-small font-semibold text-site-forest
                                                         transition-colors duration-200">
                                                {{ $country->translated_name }}
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid gap-x-8 gap-y-10 sm:grid-cols-2 md:mt-14 lg:grid-cols-4">
                @foreach($byRegion as $region => $countries)
                    <div>
                        <p class="eyebrow">{{ $region }}</p>

                        <ul class="mt-4 space-y-2.5">
                            @foreach($countries as $country)
                                <li class="text-ink-muted text-site-small">{{ $country->translated_name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
