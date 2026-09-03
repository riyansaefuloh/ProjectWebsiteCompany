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

    $sebutan = array_merge($sebutan, $khusus[$konteks] ?? []);

    /*
     * LIMA status inquiry, lima rona — satu-satu, tanpa ada yang menumpang
     * netral.
     *
     * Urutannya mengikuti perjalanan sebuah inquiry, dan ronanya ikut
     * bercerita: biru masuk, kuning sedang dikerjakan, ungu bola ada di pihak
     * pembeli, lalu berpisah dua arah — hijau jadi, merah tidak jadi.
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
        'closed'     => 'bg-brand/10 text-brand',
        'rejected'   => 'bg-status-rejected/10 text-status-rejected',

        /*
         * "Terbit" vs "Draf" hanya perlu dibedakan satu sama lain, dan
         * keduanya tidak pernah berdiri di halaman yang sama dengan status
         * inquiry — jadi hijau merek boleh dipakai ulang di sini. Yang
         * membedakan draf bukan rona lain melainkan BENTUKNYA: bergaris,
         * tidak terisi, sebagaimana sesuatu yang memang belum jadi.
         */
        'published'  => 'bg-brand/10 text-brand',
        'draft'      => 'border border-line-strong text-ink-muted',

        /*
         * "Nonaktif" beda dari "Draf": draf itu belum selesai, nonaktif itu
         * sengaja dimatikan. Yang pertama netral, yang kedua sebuah keputusan
         * — jadi ia memakai rona merah yang sama dengan "Ditolak", bukan
         * abu-abu yang lirih.
         */
        'active'     => 'bg-brand/10 text-brand',
        'inactive'   => 'bg-status-rejected/10 text-status-rejected',

        /*
         * Gerbang unduhan. Yang terisi justru "Perlu email" — itulah keadaan
         * yang MELAKUKAN sesuatu: berkasnya menangkap prospek sebelum diberikan.
         * "Terbuka" berarti tidak ada yang menghalangi, jadi ia bergaris saja,
         * mengikuti logika bentuk yang sama dengan "Draf".
         */
        'gated'      => 'bg-brand/10 text-brand',
        'open'       => 'border border-line-strong text-ink-muted',
    ];
@endphp

<span {{ $attributes->class([
        'inline-flex items-center rounded-full px-2.5 py-1 text-admin-caption font-semibold',
        $gaya[$status] ?? 'bg-mist-deep text-ink-muted',
    ]) }}>{{ $sebutan[$status] ?? $status }}</span>
