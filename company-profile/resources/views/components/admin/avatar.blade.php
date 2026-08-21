@props([
    'name' => null,
    'size' => 'md',    // sm | md | lg
    'tone' => 'quiet', // quiet | brand
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
        'sm' => 'h-9 w-9 text-[12px]',
        'md' => 'h-9 w-9 text-[13px]',

        /*
         * 40px — seukuran petak gambar di kolom pertama halaman Produk,
         * Kategori, Berita, dan Unduhan. Dipakai saat avatarnya berdiri
         * sendiri sebagai penanda baris, bukan menemani datum lain: tinggi
         * barisnya jadi 73px, sama dengan tabel-tabel itu.
         */
        'lg' => 'h-10 w-10 text-[13px]',
    ];

    // Hijau merek disimpan untuk avatar pengguna yang sedang masuk — itu
    // penanda "ini kamu". Kalau pembeli ikut hijau, penandanya hilang.
    $nada = [
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
        $nada[$tone] ?? $nada['quiet'],
    ]) }}>{{ $inisial }}</span>
