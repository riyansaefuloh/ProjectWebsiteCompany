@php
    /* Foto profil menempel pada bagiannya sendiri. Kunci pengaturan lama
       'about_image' tetap dibaca sebagai cadangan supaya foto yang sudah
       terlanjur diunggah tidak hilang. */
    $fotoProfil = \App\Support\IsiHalaman::gambar('profile')
        ?: ($settings['about_image'] ?? null);

    $aboutImage = !empty($fotoProfil)
        ? \Illuminate\Support\Facades\Storage::url($fotoProfil)
        : null;

    // ── Enam tonggak sejarah ─────────────────────────────────────────────
    $establishedYear = \App\Support\IsiHalaman::tahunBerdiri();
    $currentYear = (int) date('Y');

    $lastYear = max($currentYear, $establishedYear);

    $milestoneCount = 6;
    $milestones = [];

    /* Tahun yang diketik sendiri di panel, per tonggak. Yang tidak diisi tidak
       tercatat — dan yang tidak tercatat dihitung seperti dulu: dibagi rata dari
       tahun berdiri sampai tahun berjalan. Jadi kolom yang belum pernah disentuh
       menggambar angka yang sama persis dengan sebelum kolomnya ada. */
    $tahunDiketik = \App\Support\IsiHalaman::opsi('profile');

    /* Gambar tiap tonggak, diunggah satu per satu dari panel. Yang belum ada
       tidak digambar sama sekali — bukan diganti petak "foto kosong". Di
       garis waktu, tonggak tanpa gambar itu keadaan yang wajar; petak abu di
       sana malah terbaca seperti gambarnya gagal dimuat. */
    $gambarTonggak = \App\Support\IsiHalaman::gambarTonggak('profile');

    for ($i = 0; $i < $milestoneCount; $i++) {
        $sendiri = $tahunDiketik['milestone_' . ($i + 1) . '_year'] ?? null;
        $berkas  = $gambarTonggak['milestone_' . ($i + 1) . '_image'] ?? null;

        $milestones[] = [
            'year'  => filled($sendiri)
                ? (int) $sendiri
                : (int) round($establishedYear + $i * ($lastYear - $establishedYear) / ($milestoneCount - 1)),
            'title' => $isi('milestone_' . ($i + 1) . '_title', 'site.milestone_' . ($i + 1) . '_title'),
            'body'  => $isi('milestone_' . ($i + 1) . '_body',  'site.milestone_' . ($i + 1) . '_body'),
            'image' => $berkas ? \Illuminate\Support\Facades\Storage::url($berkas) : null,
        ];
    }

    $trackInset = 100 / (2 * $milestoneCount);
    $trackSpan  = round(100 - (2 * $trackInset), 4);

    // Dirakit di sini lalu dipasang lewat direktif @style, BUKAN ditulis
    // langsung sebagai atribut style="...". Isi atribut style dibaca editor
    // sebagai CSS, dan penanda Blade di dalamnya ditandai merah sebagai galat
    // padahal keluarannya sah — galat palsu semacam itu menyamarkan galat
    // sungguhan di berkas yang sama. Direktif @style menghasilkan atribut yang
    // sama persis tanpa pernah terlihat sebagai CSS oleh editor.
    /* Rel membentang PENUH dari tepi ke tepi, dan isiannya juga mulai dari
       tepi kiri — bukan dari titik pertama.

       Yang lama memulai keduanya di titik pertama, jadi pada tonggak pertama
       isiannya selebar nol dan tidak ada satu pun garis berwarna di layar.
       Dibaca sebagai garis yang hilang, bukan sebagai kemajuan yang belum
       berjalan. Sekarang tonggak pertama tetap punya potongan berwarna dari
       tepi kiri sampai titiknya. */
    $fillBase = $trackInset;
@endphp

<div>
    {{-- ══ Bagian halaman digambar menurut urutan dan tampil-tidaknya yang
         diatur di panel: Halaman → Susunan. ══ --}}
    @foreach($profilSections as $profilSection)
        @switch($profilSection['id'])
            @case('profil')
        {{-- Seluruh judul dan keterangan di halaman ini memakai ukuran, rupa,
             dan warna yang sama dengan seksi produk di beranda: label Jost 11px
             aksen-dalam, judul Fraunces 42px tebal cokelat, keterangan Inter
             16px.

             Perhatikan font-site-display, bukan font-display. Di lingkup .situs
             token font-display sengaja dipetakan ke huruf BADAN, jadi kelas itu
             menghasilkan Inter — bukan serifnya. Judul-judul kecil di halaman ini
             semuanya memakainya sebelum ini, dan itu sebabnya tidak ada satu pun
             yang berupa serif. --}}
        {{-- ══════════════════════════════════════════════════════════════════
             PROFIL
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="pb-20 pt-14 md:pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">

                <div class="grid gap-x-12 gap-y-8 lg:grid-cols-12">

                    <div class="lg:col-span-7">
                        {{-- Label dan paragraf kanan kini isian bagian
                             Profil; baris lama dari /page/about-us tetap
                             dibaca sebagai cadangan. --}}
                        <p class="eyebrow">
                            {{ $isi('eyebrow', 'site.nav_about', [], $page?->translated_title) }}
                        </p>

                        {{-- 42px cokelat, ukuran dan warna yang sama dengan judul
                             seksi produk di beranda. Tetap <h1> — ia judul halaman
                             ini — tapi tidak lagi 48px: ukuran itu membuatnya lebih
                             besar daripada judul seksi mana pun di situs, padahal
                             perannya sama. --}}
                        <h1 class="display mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                            {!! \App\Support\Judul::sorot($isi('headline', 'site.about_headline')) !!}
                        </h1>
                    </div>

                    <div class="lg:col-span-5 lg:self-end">
                        @php
                            $profilBody = $isi('body', 'site.about_empty', [], $page?->translated_content);
                        @endphp

                        @if($profilBody !== strip_tags($profilBody))
                            <div class="rich max-w-[46ch]">{!! $profilBody !!}</div>
                        @else
                            <p class="lede max-w-[46ch] text-site-body">{{ $profilBody }}</p>
                        @endif
                    </div>
                </div>

                {{-- ── Foto profil ────────────────────────────────────────────── --}}
                <div class="mt-12 overflow-hidden rounded-panel bg-mist-deep lg:mt-16">
                    @if($aboutImage)
                        <img src="{{ $aboutImage }}" alt="" aria-hidden="true" fetchpriority="high"
                             class="aspect-[4/3] w-full object-cover sm:aspect-[16/7]">
                    @else
                        <x-site.image-placeholder class="aspect-[4/3] w-full sm:aspect-[16/7]" icon="h-14 w-14" />
                    @endif
                </div>
            </div>
        </section>
                @break

            @case('vision_mission')
        {{-- ══════════════════════════════════════════════════════════════════
             VISI & MISI
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line">
            <div class="shell">

                <div class="max-w-[46rem]">
                    <p class="eyebrow">{{ $isi('vm_eyebrow', 'site.vision_mission_eyebrow') }}</p>
                    <h2 class="display mt-5 max-w-[22ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($isi('vm_title', 'site.vision_mission_title')) !!}
                    </h2>
                </div>

                {{-- Kolomnya SAMA LEBAR, enam petak masing-masing.

                     Pembagian rata ini sempat gagal sebelumnya karena pernyataan
                     visi menentukan tinggi panelnya sendiri: di kolom yang sempit
                     ia pecah tujuh baris dan panel kiri jadi bidang tertinggi di
                     seksi. Yang membuatnya berhasil sekarang bukan lebarnya
                     melainkan max-w-[30ch] pada kalimatnya — panjang barisnya
                     dipatok, jadi jumlah barisnya tidak lagi berubah mengikuti
                     lebar kolom, dan tinggi seksi kembali ditentukan daftar misi.

                     Keduanya MERENTANG sama tinggi, dan labelnya berdiri di luar —
                     satu di atas panel, satu di atas daftar, keduanya .eyebrow yang
                     sama. Selama label visi masih di DALAM panelnya, tepi atas panel
                     sejajar dengan label misi, bukan dengan daftarnya: kalimat visi
                     dan butir misi mulai di dua garis yang berbeda. --}}
                <div class="mt-12 grid items-stretch gap-6 lg:mt-14 lg:grid-cols-12 lg:gap-8">

                    {{-- ── VISI ───────────────────────────────────────────────── --}}
                    <div class="flex flex-col lg:col-span-6">
                        <p class="eyebrow">{{ $isi('vision_label', 'site.vision_label') }}</p>

                        {{-- Isinya DITENGAHKAN, mendatar maupun tegak. Panel ini cuma
                             memuat satu pernyataan; ditambatkan ke kaki, ia menyisakan
                             lubang di atasnya begitu panelnya merentang menyamai daftar
                             misi. Yang di tengah tidak punya sisi yang lebih kosong. --}}
                        <div class="relative mt-5 flex flex-1 flex-col justify-center overflow-hidden rounded-panel
                                    bg-site-forest p-8 text-center sm:p-10">

                            {{-- 21px, sama dengan judul tiap butir misi. Pada 26px
                                 pernyataan sepanjang ini memenuhi panelnya sampai ke
                                 tepi dan panel itu jadi bidang paling berat di seksi —
                                 padahal ia satu dari dua hal yang sederajat. Yang
                                 membedakannya dari butir misi bukan ukuran melainkan
                                 bidang gelapnya sendiri. --}}
                            <p class="relative mx-auto max-w-[30ch] font-site-display font-bold leading-[1.5] tracking-[-0.01em] text-white text-site-title">
                                {{ $isi('vision_body', 'site.vision_body') }}
                            </p>
                        </div>
                    </div>

                    {{-- ── MISI ───────────────────────────────────────────────── --}}
                    <div class="lg:col-span-6">
                        <p class="eyebrow">{{ $isi('mission_label', 'site.mission_label') }}</p>

                        @php $missionCount = 3; @endphp

                        <ol class="mt-5 list-none divide-y divide-line border-y border-line">
                            @for($i = 1; $i <= $missionCount; $i++)
                                <li class="flex items-start gap-3.5 py-4">
                                    {{-- Angka hantu, rupa yang sama dengan nomor kartu
                                         pilar di beranda: huruf judul tebal, bertitik,
                                         nyaris tak terbaca.

                                         Kepekatannya 0,14 di sini, bukan 0,07 seperti
                                         di kartu pilar. Di sana angkanya 76px dan punya
                                         seluruh sudut kartu untuk dirinya; di baris
                                         daftar ia 40px dan berdampingan dengan judul —
                                         pada 0,07 ia lenyap dan yang tersisa cuma
                                         lekukan kosong sebelum judulnya.

                                         Lebarnya dipatok supaya judul ketiga butirnya
                                         mulai di garis yang sama, dan tabular-nums
                                         menjaga "1" tidak lebih sempit daripada angka
                                         lain. --}}
                                    <span aria-hidden="true" data-hias
                                          class="w-[2.2rem] shrink-0 select-none pt-px font-site-display
                                                 text-[26px] font-bold leading-none tracking-[-0.04em]
                                                 tabular-nums text-ink/[0.18]">
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}.
                                    </span>

                                    <div class="min-w-0">
                                        {{-- 17px, bukan 21px. Ketiga butir inilah yang
                                             menentukan tinggi seluruh seksi — panel visi
                                             merentang mengikutinya — jadi tiap piksel di
                                             sini dibayar tiga kali lalu ditiru panel di
                                             sebelahnya. --}}
                                        <h3 class="font-site-display font-bold leading-snug tracking-[-0.01em] text-site-forest text-site-lede">
                                            {{ $isi('mission_' . $i . '_title', 'site.mission_' . $i . '_title') }}
                                        </h3>
                                        <p class="mt-2 max-w-[52ch] leading-relaxed text-ink-muted text-site-body">
                                            {{ $isi('mission_' . $i . '_body', 'site.mission_' . $i . '_body') }}
                                        </p>
                                    </div>
                                </li>
                            @endfor
                        </ol>
                    </div>
                </div>
            </div>
        </section>
                @break

            @case('values')
        {{-- ══════════════════════════════════════════════════════════════════
             CORE VALUES
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line">
            <div class="shell">

                @php
                    /* Ikonnya dipatok di kode, judul dan keterangannya diketik dari
                       panel — keduanya bisa berjalan sendiri-sendiri. Yang di sini
                       dipilih untuk isi yang SEKARANG terpasang: integritas, mutu,
                       kemitraan, tanggung jawab. Kalau isinya nanti diganti jauh,
                       ikonnya perlu ditinjau ulang di sini. */
                    $values = collect(['integrity', 'quality', 'partnership', 'responsibility'])
                        ->map(fn ($ikon, $i) => [
                            'icon'  => $ikon,
                            'title' => $isi('value_' . ($i + 1) . '_title', 'site.value_' . ($i + 1) . '_title'),
                            'body'  => $isi('value_' . ($i + 1) . '_body',  'site.value_' . ($i + 1) . '_body'),
                        ])
                        ->all();

                    $nilaiBody = $isi('values_body', 'site.values_body');
                @endphp

                {{-- Kepala seksi disusun seperti "Why Choose Us" di beranda: label
                     dan judul di kiri, keterangan di kanan yang ditambatkan ke dasar
                     judul. --}}
                <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                    <div class="lg:col-span-6">
                        <p class="eyebrow">{{ $isi('values_eyebrow', 'site.values_eyebrow') }}</p>
                        <h2 class="display mt-5 max-w-[16ch] text-site-h2 text-site-forest">
                            {!! \App\Support\Judul::sorot($isi('values_title', 'site.values_title')) !!}
                        </h2>
                    </div>

                    <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                        @if($nilaiBody !== strip_tags($nilaiBody))
                            <div class="rich max-w-[46ch]">{!! $nilaiBody !!}</div>
                        @else
                            <p class="lede max-w-[46ch] text-site-body">{{ $nilaiBody }}</p>
                        @endif
                    </div>
                </div>

                {{-- Kartunya sama persis dengan kartu pilar di beranda: nomor hantu
                     di puncak, petak ikon, lalu judul dan keterangan bertambat di
                     kaki. Yang lama sederet petak berpembatas rambut tanpa bidang
                     sendiri — di halaman yang seluruh seksinya berkartu, ia satu-
                     satunya yang terbaca sebagai tabel.

                     Melebar saat disorot juga ikut, dan HANYA di lg ke atas: di
                     bawah itu tidak ada kursor yang bisa menyorot, jadi kartunya
                     kembali jadi baris gulir bersnap dengan keterangan yang selalu
                     terbuka. --}}
                <ul class="mt-12 -mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2
                           [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:-mx-8 sm:px-8
                           lg:mx-0 lg:mt-14 lg:overflow-visible lg:px-0">
                    @foreach($values as $value)
                        <li class="group w-[78%] shrink-0 snap-start sm:w-[calc(50%-0.625rem)]
                                   lg:w-auto lg:min-w-0 lg:shrink lg:basis-0 lg:grow
                                   lg:transition-[flex-grow] lg:duration-500 lg:ease-out
                                   lg:hover:grow-[2] lg:focus-within:grow-[2]">
                            <div class="card relative flex h-full min-h-[256px] flex-col justify-end p-6
                                        transition-colors duration-300 hover:border-forest hover:bg-forest
                                        sm:min-h-[288px] lg:min-h-[332px]">

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
                                    <x-icon.value :name="$value['icon']" size="h-5 w-5" />
                                </span>

                                <h3 class="relative mt-5 font-site-display font-bold leading-snug tracking-[-0.01em]
                                           text-site-forest transition-colors duration-300 group-hover:text-white text-site-title">
                                    {{ $value['title'] }}
                                </h3>

                                <div class="relative grid grid-rows-[1fr] transition-all duration-500 ease-out
                                            lg:grid-rows-[0fr] lg:opacity-0
                                            lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                            lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                    <p class="overflow-hidden leading-relaxed text-ink-muted transition-colors duration-300
                                              group-hover:text-white/70 text-site-body">
                                        <span class="mt-2.5 block">{{ $value['body'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
                @break

            @case('history')
        {{-- ══════════════════════════════════════════════════════════════════
             SEJARAH
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="section border-t border-line"
                 x-data="{
                     active: 0,
                     total: {{ count($milestones) }},
                     go(dir) {
                         /* Dijepit di kedua ujung, tidak memutar balik ke awal:
                            ini garis waktu, dan melompat dari tonggak terakhir
                            kembali ke tahun berdiri membaca seperti kekeliruan. */
                         this.active = Math.min(Math.max(this.active + dir, 0), this.total - 1);
                     }
                 }">
            <div class="shell">

                <p class="eyebrow">{{ $isi('history_eyebrow', 'site.history_eyebrow') }}</p>

                {{-- SATU bidang judul, bukan dua. Bagian beraksennya ditandai
                     *…* di dalam judulnya sendiri — cara yang sama dengan judul
                     seksi mana pun di situs ini — dan .display em yang memberinya
                     rupa tulis tangan sekaligus warnanya.

                     Yang lama memisahkannya jadi kolom kedua lalu membungkusnya
                     .text-brand: warnanya berbeda dari kata beraksen di seluruh
                     halaman lain, rupanya tetap serif biasa, dan urutannya terkunci
                     di belakang — aksen di tengah kalimat mustahil ditulis. --}}
                <h2 class="display mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('history_title', 'site.history_title')) !!}
                </h2>

                {{-- ── Rel waktu, DI ATAS panel tonggaknya ────────────────────
                     Ia daftar isi bagi panel di bawahnya; menaruhnya sesudah
                     panel berarti pembaca melihat isi satu tonggak sebelum tahu
                     ada berapa tonggak dan yang mana yang sedang dibuka. --}}
                <div class="relative mt-12 lg:mt-14">

                    <div class="absolute inset-x-0 top-[7px] h-px bg-line" aria-hidden="true"></div>

                    {{-- Yang dianimasikan HANYA width.

                         transition-all menyuruh peramban mengawasi setiap sifat
                         yang bisa berubah pada elemen ini — termasuk yang tidak
                         pernah berubah — dan tiap gerak jadi lebih berat daripada
                         yang diperlukan. Durasinya juga dipangkas 300ms ke 200ms:
                         geser sejauh beberapa ratus piksel yang memakan sepertiga
                         detik terbaca sebagai lambat, bukan sebagai halus. --}}
                    <div class="absolute left-0 top-[7px] h-px bg-brand
                                transition-[width] duration-200 ease-out"
                         x-bind:style="`width: ${ {{ $fillBase }} + (active / (total - 1)) * {{ $trackSpan }} }%`"
                         aria-hidden="true"></div>

                    <ul class="relative grid grid-cols-6">
                        @foreach($milestones as $index => $milestone)
                            <li class="flex flex-col items-center">
                                <button type="button"
                                        x-on:click="active = {{ $index }}"
                                        x-bind:aria-current="active === {{ $index }} ? 'step' : 'false'"
                                        class="group flex flex-col items-center gap-3 pt-0">
                                    <span class="sr-only">{{ $milestone['year'] }} — {{ $milestone['title'] }}</span>

                                    {{-- Titiknya BERUKURAN TETAP; yang berubah cuma
                                         skala bulatan di dalamnya.

                                         Yang lama menukar h-2 w-2 jadi h-3.5 w-3.5,
                                         dan itu mengubah TATA LETAK: tiap kali tonggak
                                         berpindah, tinggi barisnya berubah 6px dan
                                         seluruh deret tahun di bawahnya ikut bergeser.
                                         Skala cuma menggambar ulang, tidak menghitung
                                         ulang letak apa pun — itu sebabnya ia terasa
                                         halus sementara yang lama tersendat. --}}
                                    <span class="flex h-3.5 w-3.5 items-center justify-center" aria-hidden="true">
                                        {{-- TIGA keadaan, bukan dua: sudah dilewati, sedang
                                             disorot, dan belum dilewati.

                                             Yang sudah dilewati berwarna sama dengan garis
                                             isian yang menghubungkannya — cokelat tua — tapi
                                             tetap kecil. Kalau ia sewarna titik yang belum
                                             dilewati, garis berwarna berhenti di titik yang
                                             warnanya bilang "belum", dan rel itu berhenti
                                             menceritakan apa pun: warnanya cuma menandai satu
                                             titik alih-alih menandai kemajuan.

                                             Ukurannya yang membedakannya dari titik yang
                                             sedang disorot. Besar berarti "di sinilah kamu",
                                             warna berarti "ini sudah lewat" — dua pertanyaan
                                             berbeda, dua penanda berbeda. --}}
                                        <span x-bind:class="active === {{ $index }}
                                                ? 'scale-100 bg-brand'
                                                : ({{ $index }} < active
                                                    ? 'scale-[0.57] bg-brand'
                                                    : 'scale-[0.57] bg-line-strong group-hover:bg-ink-faint')"
                                              class="block h-3.5 w-3.5 rounded-full
                                                     transition-[transform,background-color] duration-200 ease-out"></span>
                                    </span>

                                    <span x-bind:class="active === {{ $index }} ? 'text-ink' : 'text-ink-faint'"
                                          class="font-semibold transition-colors duration-200 text-site-micro"
                                          aria-hidden="true">
                                        {{ $milestone['year'] }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ── Panel tonggak ──────────────────────────────────────────── --}}
                <div class="mt-10 min-h-[240px] sm:min-h-[220px] lg:mt-12">
                    @foreach($milestones as $index => $milestone)
                        {{-- 180ms dan hanya alfa. Yang lama 300ms sambil menggeser
                             dua piksel; gerak tegak sependek itu tidak terbaca
                             sebagai gerak, cuma menunda munculnya isi. --}}
                        <div x-show="active === {{ $index }}"
                             @if($index > 0) x-cloak @endif
                             x-transition:enter="transition ease-out duration-[180ms]"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="grid gap-x-12 gap-y-6 lg:grid-cols-12">

                            {{-- Kolom kiri kini HANYA gambar.

                                 Ia digambar lazy dan bernisbah tetap 2:1: tinggi
                                 petaknya sudah dipesan sebelum berkasnya sampai, jadi
                                 garis waktu di bawahnya tidak melompat saat gambar
                                 tonggak berikutnya selesai dimuat.

                                 Tanpa aria-hidden dan tanpa alt kosong: gambar ini ISI,
                                 bukan hiasan — ia menunjukkan tonggak yang sedang
                                 diceritakan. Alt-nya diambil dari judul tonggaknya,
                                 satu-satunya keterangan yang benar tentang apa yang
                                 tergambar. --}}
                            @if($milestone['image'])
                                <div class="lg:col-span-5">
                                    {{-- Di layar lebar gambarnya MERENTANG setinggi
                                         kolomnya, bukan dipatok nisbah.

                                         Nisbah tetap membuat tingginya ditentukan
                                         lebarnya sendiri — dan panjang keterangan tiap
                                         tonggak berbeda-beda, jadi tepi bawah gambar
                                         hampir tidak pernah bertemu tepi bawah teks di
                                         sebelahnya. Dengan h-full, tinggi barisnya
                                         ditentukan teks dan gambarnya menyesuaikan;
                                         keduanya mulai dan berakhir di garis yang sama
                                         berapa pun panjang keterangannya.

                                         Nisbah 2:1 tetap dipasang untuk layar sempit,
                                         tempat kedua kolomnya bertumpuk dan tidak ada
                                         tinggi baris untuk diikuti. --}}
                                    <div class="max-w-[420px] overflow-hidden rounded-corner border border-line
                                                bg-site-paper lg:h-full">
                                        <img src="{{ $milestone['image'] }}" alt="{{ $milestone['title'] }}"
                                             loading="lazy"
                                             class="aspect-[2/1] w-full object-cover lg:aspect-auto lg:h-full">
                                    </div>
                                </div>
                            @endif

                            {{-- Tahun, judul, dan keterangannya kini SATU blok di kanan.

                                 Tempat blok itu ikut ada-tidaknya gambar: dengan gambar
                                 ia mulai di petak ketujuh, tanpa gambar ia melebar dari
                                 petak pertama. Kalau tempatnya dipatok, tonggak yang
                                 belum bergambar akan menggambar separuh baris kosong di
                                 kiri lalu teks yang terdorong sendirian ke kanan — dan
                                 itu terbaca seperti gambarnya gagal dimuat, bukan
                                 seperti tonggak yang memang belum berfoto. --}}
                            <div @class([
                                'lg:col-span-6 lg:col-start-7' => $milestone['image'],
                                'lg:col-span-8' => ! $milestone['image'],
                            ])>
                                <p class="font-site-display font-bold leading-none tracking-[-0.04em] text-site-forest text-site-metric-xl">
                                    {{ $milestone['year'] }}
                                </p>

                                <h3 class="mt-5 font-site-display font-bold leading-snug tracking-[-0.02em] text-site-forest text-site-h3">
                                    {{ $milestone['title'] }}
                                </h3>
                                <p class="lede mt-3 max-w-[48ch] text-site-body">{{ $milestone['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-10 flex items-center justify-center gap-3">
                    <button type="button" x-on:click="go(-1)" x-bind:disabled="active === 0"
                            x-bind:class="active === 0 ? 'opacity-30' : 'hover:border-brand hover:text-brand'"
                            aria-label="{{ $isi('history_eyebrow', 'site.history_eyebrow') }}"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong
                                   text-ink transition-colors disabled:cursor-not-allowed">
                        <svg class="h-4 w-4 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button type="button" x-on:click="go(1)" x-bind:disabled="active === total - 1"
                            x-bind:class="active === total - 1 ? 'opacity-30' : 'hover:border-brand hover:text-brand'"
                            aria-label="{{ $isi('history_eyebrow', 'site.history_eyebrow') }}"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong
                                   text-ink transition-colors disabled:cursor-not-allowed">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>
                @break

            @case('certification')
        {{-- ══════════════════════════════════════════════════════════════════
             BANNER SERTIFIKASI
             ══════════════════════════════════════════════════════════════════ --}}
        <section class="pb-20 pt-16 lg:pb-24 lg:pt-20">
            <div class="shell">
                <div class="rounded-panel border border-line bg-site-paper p-8 sm:p-10 lg:p-12">

                    {{-- Kepala banner disusun seperti seksi produk di beranda: label
                         dan judul di kiri, keterangan dan tombolnya di kanan yang
                         ditambatkan ke dasar judul. --}}
                    <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                        <div class="lg:col-span-6">
                            <p class="eyebrow">{{ $isi('cert_eyebrow', 'site.certifications') }}</p>
                            <h2 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                                {!! \App\Support\Judul::sorot($isi('cert_title', 'site.cert_card_title')) !!}
                            </h2>
                        </div>

                        <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                            <p class="lede max-w-[46ch] text-site-body">{{ $isi('cert_body', 'site.cert_card_body') }}</p>

                            {{-- Bentuknya sama dengan "Explore Products": pil forest
                                 pejal setinggi 40px dengan bulatan emas berpanah.

                                 Yang digantikan .btn .btn-outline .btn-arrow — tombol
                                 bergaris bersudut 4px setinggi 44px berhuruf aksen
                                 kapital. Empat hal berbeda sekaligus dari tombol
                                 seksi mana pun di situs ini, padahal tugasnya sama:
                                 mengantar ke halaman daftarnya. --}}
                            <a href="{{ route('certifications.index') }}"
                               class="group mt-7 inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                      bg-site-forest text-white
                                      font-site-body text-site-small font-semibold whitespace-nowrap
                                      transition-colors duration-300 hover:bg-site-brand-deep">
                                {{ $isi('cert_cta', 'site.cta_view_certifications') }}
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
            </div>
        </section>
                @break

        @endswitch
    @endforeach
</div>
