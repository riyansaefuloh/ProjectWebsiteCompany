@props([
    'status',

    /*
     * Konteks pemakainya — hanya dipakai kalau sebutan bawaannya perlu
     * ditimpa untuk jenis benda tertentu. Lihat $khusus di bawah.
     */
    'konteks' => null,
])

@php
    /*
     * Pil status inquiry.
     *
     * Dikumpulkan jadi satu komponen karena status yang sama muncul di dua
     * tempat — tabel ringkas di dasbor dan tabel penuh di halaman inquiry.
     * Kalau petanya disalin ke dua berkas, cepat atau lambat salah satunya
     * ketinggalan saat ada status baru, dan "Ditawar" tampil biru di satu
     * halaman dan abu di halaman lain.
     */
    $sebutan = [
        // Inquiry
        'new'        => 'Baru',
        'processing' => 'Diproses',
        'quoted'     => 'Ditawar',
        'closed'     => 'Selesai',
        'rejected'   => 'Ditolak',

        // Keadaan terbit — dipakai produk, berita, halaman, galeri.
        'published'  => 'Terbit',
        'draft'      => 'Draf',

        // Keadaan hidup-mati — dipakai kategori, sertifikasi, pasar ekspor.
        'active'     => 'Aktif',
        'inactive'   => 'Nonaktif',

        // Gerbang unduhan — dipakai halaman Unduhan.
        'gated'      => 'Perlu email',
        'open'       => 'Terbuka',
    ];

    /*
     * Sebutan yang ditimpa untuk jenis benda tertentu.
     *
     * 'published' dipakai bertiga: Produk, Berita, dan Halaman. Satu nilai di
     * basis data, tapi katanya yang wajar berbeda menurut bendanya — sebuah
     * artikel memang "Terbit", sedangkan sebuah produk lebih tepat disebut
     * "Aktif": ia tidak diterbitkan, ia tersedia atau tidak.
     *
     * Kartu produk di dasbor sudah lama memakai "Aktif" untuk hitungan yang
     * sama, jadi timpaan ini menyamakan keduanya — bukan menciptakan kata
     * baru.
     *
     * Ditimpa DI SINI, bukan dengan mengganti peta di atas: mengganti yang di
     * atas akan membuat artikel berita ikut bertulis "Aktif", dan itu bukan
     * bahasa Indonesia yang wajar untuk sebuah tulisan.
     */
    $khusus = [
        'produk' => ['published' => 'Aktif'],
    ];

    /* $konteks ?? '' — bukan $konteks langsung. Komponen ini biasa dipanggil
       tanpa konteks, dan PHP menganggap null sebagai kunci larik itu usang:
       tiap halaman admin yang menggambar pil status ikut menulis peringatan
       ke log. Untai kosong bukan kunci yang ada di $khusus, jadi hasilnya
       sama persis — tanpa peringatannya. */
    $sebutan = array_merge($sebutan, $khusus[$konteks ?? ''] ?? []);

    /*
     * LIMA status inquiry, lima rona — satu-satu, tanpa ada yang menumpang
     * netral.
     *
     * Urutannya mengikuti perjalanan sebuah inquiry, dan ronanya ikut
     * bercerita: biru masuk, kuning sedang dikerjakan, ungu bola ada di pihak
     * pembeli, lalu berpisah dua arah — hijau jadi, merah tidak jadi.
     *
     * Hijaunya --color-status-done, BUKAN --color-brand seperti dulu. Warna
     * merek panel kini karamel, dan pil karamel akan berdiri sekolom dengan
     * pil merah pada jarak yang tidak bisa dibedakan di deuteranopia.
     *
     * Jaraknya diukur di ΔE2000, termasuk pada simulasi deuteranopia; angkanya
     * beserta alasan mengapa kuningnya harus gelap ada di --color-status-* di
     * app.css. Tiap pil tetap membawa teksnya sendiri, jadi warnanya penanda
     * kedua, bukan satu-satunya.
     */
    $gaya = [
        'new'        => 'bg-status-new/10 text-status-new',
        'processing' => 'bg-status-processing/10 text-status-processing',
        'quoted'     => 'bg-status-quoted/10 text-status-quoted',
        'closed'     => 'bg-status-done/10 text-status-done',
        'rejected'   => 'bg-status-rejected/10 text-status-rejected',

        /*
         * "Terbit" vs "Draf" hanya perlu dibedakan satu sama lain, dan
         * keduanya tidak pernah berdiri di halaman yang sama dengan status
         * inquiry — jadi rona "selesai" boleh dipakai ulang di sini. Yang
         * membedakan draf bukan rona lain melainkan BENTUKNYA: bergaris,
         * tidak terisi, sebagaimana sesuatu yang memang belum jadi.
         */
        'published'  => 'bg-status-done/10 text-status-done',
        'draft'      => 'border-line-strong text-ink-muted',

        /*
         * "Nonaktif" beda dari "Draf": draf itu belum selesai, nonaktif itu
         * sengaja dimatikan. Yang pertama netral, yang kedua sebuah keputusan
         * — jadi ia memakai rona merah yang sama dengan "Ditolak", bukan
         * abu-abu yang lirih.
         */
        'active'     => 'bg-status-done/10 text-status-done',
        'inactive'   => 'bg-status-rejected/10 text-status-rejected',

        /*
         * Gerbang unduhan. Yang terisi justru "Perlu email" — itulah keadaan
         * yang MELAKUKAN sesuatu: berkasnya menangkap prospek sebelum diberikan.
         * "Terbuka" berarti tidak ada yang menghalangi, jadi ia bergaris saja,
         * mengikuti logika bentuk yang sama dengan "Draf".
         */
        'gated'      => 'bg-status-done/10 text-status-done',
        'open'       => 'border-line-strong text-ink-muted',
    ];
@endphp

<span {{ $attributes->class([
        'admin-pill font-semibold',
        $gaya[$status] ?? 'bg-mist-deep text-ink-muted',
    ]) }}>{{ $sebutan[$status] ?? $status }}</span>
