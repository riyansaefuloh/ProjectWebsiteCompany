<?php

namespace App\Support;

/**
 * Tekanan di dalam judul, ditulis dari panel.
 *
 * Judul di situs ini memakai dua rupa dalam satu kalimat: badannya sans tebal,
 * dan kata yang ditekankan berpindah ke serif miring. Yang menentukan kata mana
 * bukan kode, melainkan yang menulis judulnya — karena kata mana yang pantas
 * ditekankan berbeda di tiap kalimat, dan berbeda pula antara Indonesia dan
 * English:
 *
 *     Your Partner in *Sourcing Premium* Indonesian Coffee
 *
 * Bintang dipilih sebagai penandanya karena tiga hal: ia sudah dikenal luas
 * sebagai tanda penekanan, ia tidak pernah muncul sendiri di dalam judul yang
 * wajar, dan kolomnya tetap berupa isian teks biasa — bukan penyunting kaya
 * yang menyimpan HTML dan membuka pintu bagi markah apa pun.
 */
class Judul
{
    /**
     * Mengubah *kata* jadi <em>kata</em>, dengan sisanya tetap aman.
     *
     * Urutannya penting dan tidak boleh dibalik: teksnya DIKABURKAN LEBIH DULU,
     * baru penandanya diterjemahkan. Kalau dibalik, judul yang memuat < atau &
     * — atau yang sengaja diisi markah — akan lolos apa adanya ke halaman.
     * Sesudah e(), satu-satunya markah yang bisa ada di dalam untai ini adalah
     * <em> yang kita tulis sendiri.
     */
    public static function sorot(?string $teks): string
    {
        $aman = e((string) $teks);

        /*
         * [^*]+ — tidak rakus, dan tidak melewati bintang.
         *
         * Tanpa itu, "a *b* c *d* e" akan tercakup jadi satu tekanan panjang
         * dari bintang pertama sampai bintang terakhir, menelan " c " yang
         * tidak diminta.
         *
         * Bintang yang tidak berpasangan sengaja dibiarkan tergambar apa
         * adanya. Menyembunyikannya berarti judul yang salah ketik tampak
         * benar di halaman tapi tidak pernah menekankan apa pun — dan tidak
         * ada satu tanda pun yang menjelaskan kenapa.
         */
        return preg_replace('/\*([^*]+)\*/u', '<em>$1</em>', $aman);
    }

    /**
     * Teks tanpa penandanya, untuk tempat yang TIDAK boleh memuat markah.
     *
     * Judul yang sama dipakai ulang di luar halaman: judul tab peramban, meta
     * og:title, aria-label, dan atribut alt. Di sana <em> tidak bisa digambar
     * dan bintangnya akan terbaca sebagai salah ketik.
     */
    public static function polos(?string $teks): string
    {
        return preg_replace('/\*([^*]+)\*/u', '$1', (string) $teks);
    }
}
