<?php

namespace App\Support;

/**
 * Perkakas bersama untuk pencarian teks penuh PostgreSQL.
 *
 * Dua keputusan yang berlaku untuk seluruh pencarian di situs ini:
 *
 * 1. KAMUSNYA 'simple', BUKAN 'english'.
 *
 *    Sebelumnya seluruh kueri memakai kamus 'english' untuk isi berbahasa
 *    Indonesia. Kamus itu memenggal kata menurut aturan Inggris: "kopi" tidak
 *    berhubungan dengan "kopinya", sementara "biji" bisa dipenggal jadi bentuk
 *    yang tidak berarti apa-apa. PostgreSQL tidak membawa kamus Indonesia, dan
 *    'simple' — yang tidak memenggal sama sekali — lebih jujur untuk isi
 *    dwibahasa daripada memakai aturan bahasa yang salah.
 *
 * 2. COCOK SEBAGIAN KATA.
 *
 *    plainto_tsquery hanya mencocokkan kata utuh, jadi mengetik "kop" tidak
 *    menemukan "kopi". Untuk kotak cari yang menyaring sambil diketik, itu
 *    terasa seperti kotaknya rusak. Di sini tiap kata diberi akhiran :* supaya
 *    dicocokkan sebagai awalan.
 */
class PencarianTeks
{
    /**
     * Merangkai tsquery awalan dari kata-kata yang diketik pemakai.
     *
     * Masukannya dibersihkan, BUKAN diloloskan apa adanya: to_tsquery memakai
     * tata bahasanya sendiri, dan tanda seperti & | ! : ( ) di dalam kata
     * pencarian akan membuat kueri gagal dengan galat sintaks — pencarian yang
     * mati total hanya karena seseorang mengetik tanda tanya.
     *
     * Mengembalikan null kalau tidak ada satu pun kata yang tersisa sesudah
     * dibersihkan; pemanggilnya harus memperlakukan itu sebagai "jangan
     * saring", bukan "tidak ada hasil".
     */
    public static function kueriAwalan(string $kata): ?string
    {
        $bersih = preg_split('/[^\p{L}\p{N}]+/u', $kata, -1, PREG_SPLIT_NO_EMPTY);

        if (! $bersih) {
            return null;
        }

        // Dibatasi supaya satu tempelan paragraf tidak berubah jadi kueri
        // beruntai ratusan kata yang memberatkan basis data.
        $bersih = array_slice($bersih, 0, 8);

        return implode(' & ', array_map(fn ($k) => $k . ':*', $bersih));
    }

    /**
     * Nama kamus yang dipakai seluruh pencarian.
     */
    public static function kamus(): string
    {
        return 'simple';
    }
}
