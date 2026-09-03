@props([
    'name' => null,
    'size' => 'md',    // sm | md | lg | xl
    'tone' => 'frame', // frame | quiet | brand
])

@php
    // Dua huruf pertama dari dua kata pertama. Nama asing seperti
    // "Fatima Al-Rashid" jadi "FA"; nama satu kata jadi satu huruf; nama
    // kosong jadi "?" — bukan lingkaran hampa yang tampak rusak.
    $inisial = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->implode('') ?: '?';

    // Ukurannya dipetakan ke kelas utuh, bukan dirangkai seperti h-{{ $size }}:
    // Tailwind memindai berkas sumber apa adanya, jadi kelas yang baru terbentuk
    // saat penyajian tidak pernah ikut dibuatkan gayanya.
    $ukuran = [
        'sm' => 'h-9 w-9 text-admin-label',
        'md' => 'h-9 w-9 text-admin-body',

        /*
         * 40px — seukuran petak gambar di kolom pertama halaman Produk,
         * Kategori, Berita, dan Unduhan. Dipakai saat avatarnya berdiri
         * sendiri sebagai penanda baris, bukan menemani datum lain: tinggi
         * barisnya jadi 73px, sama dengan tabel-tabel itu.
         */
        'lg' => 'h-10 w-10 text-admin-body',

        /*
         * 52px — setinggi blok dua baris di kepala jendela kelola inquiry:
         * nama admin-display (24 × 1,25 = 30px), jarak 6px, lalu keterangan
         * admin-label (12 × 1,4 = 17px). Jumlahnya 53px, dan 52 adalah
         * kelipatan spasi terdekat.
         *
         * Angkanya dihitung, bukan dikira-kira. Avatar yang lebih pendek dari
         * teks di sebelahnya membuat baris itu tampak menggantung — dan
         * selisih tiga belas piksel, yang tadinya ada di sini, cukup besar
         * untuk terlihat tanpa perlu diukur.
         */
        'xl' => 'h-13 w-13 text-admin-title',
    ];

    /*
     * 'frame' adalah BAWAANNYA, dan itu disengaja.
     *
     * Dandanannya sama persis dengan avatar di bilah atas dan bingkai logo
     * perusahaan — hijau sebagai huruf di dalam bingkai bernada terang —
     * sehingga bentuk berbingkai itu terbaca sebagai satu keluarga di seluruh
     * panel.
     *
     * Sebelumnya bawaannya 'quiet' dan tiap tempat memilih nadanya sendiri.
     * Hasilnya avatar pembeli abu di satu tabel, hijau penuh di kepala jendela,
     * dan abu lagi di dropdown lonceng — tiga dandanan untuk satu benda yang
     * sama. Menjadikan yang benar sebagai bawaan berarti avatar yang ditambahkan
     * besok ikut konsisten tanpa ada yang perlu mengingatnya.
     *
     * Dua nada lain tetap disediakan untuk saat sebuah avatar memang harus
     * berdiri sendiri — tapi keduanya kini TIDAK dipakai satu pun, dan itulah
     * yang membuat panel ini konsisten. Yang membedakan sesuatu sebaiknya
     * kata, bukan rona: halaman Pengguna dulu memakai hijau penuh untuk
     * menandai barisnya sendiri, dan keterangan "Akun Anda" di sampingnya
     * sudah mengatakan itu dengan lebih jelas.
     */
    $nada = [
        'frame' => 'border border-line bg-mist text-brand',
        'quiet' => 'bg-mist-deep text-ink-muted',
        'brand' => 'bg-brand text-white',
    ];
@endphp

{{--
    aria-hidden: namanya selalu tertulis lengkap tepat di sebelahnya, jadi
    tanpa ini pembaca layar mengucapkan "F A" dulu baru "Fatima Al-Rashid".
--}}
<span aria-hidden="true" {{ $attributes->class([
        'inline-flex shrink-0 select-none items-center justify-center rounded-full font-bold',
        $ukuran[$size] ?? $ukuran['md'],
        $nada[$tone] ?? $nada['frame'],
    ]) }}>{{ $inisial }}</span>
