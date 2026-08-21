@props([
    'status',
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
     * Hanya TIGA rona untuk lima status; dua sisanya dibedakan BENTUKNYA
     * (terisi vs bergaris), bukan warnanya. Alasannya diukur: dengan empat
     * rona, pasangan terburuknya jatuh ke ΔE 6,0 pada deuteranopia — merah
     * dan hijau memang persis yang paling sering tertukar. Tiap pil juga
     * selalu membawa teksnya sendiri, jadi warnanya penanda kedua.
     */
    $gaya = [
        'new'        => 'bg-status-new/10 text-status-new',
        'processing' => 'bg-mist-deep text-ink-muted',
        'quoted'     => 'border border-line-strong text-ink-muted',
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
         * mengikuti logika bentuk yang sama dengan "Draf" dan "Ditawar".
         */
        'gated'      => 'bg-brand/10 text-brand',
        'open'       => 'border border-line-strong text-ink-muted',
    ];
@endphp

<span {{ $attributes->class([
        'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold',
        $gaya[$status] ?? 'bg-mist-deep text-ink-muted',
    ]) }}>{{ $sebutan[$status] ?? $status }}</span>
