<?php

namespace App\Support;

/**
 * Pengurai alamat YouTube.
 *
 * Yang diketik orang di panel bisa berbentuk apa saja — alamat tontonan biasa,
 * alamat pendek youtu.be, alamat Shorts, atau alamat sematan yang sudah jadi.
 * Yang dibutuhkan halaman publik cuma satu: sebelas huruf id videonya.
 *
 * Diurai di PHP, bukan di peramban, karena keputusannya diambil sebelum
 * halamannya digambar: alamat yang tidak bisa dibaca berarti bloknya tidak
 * digambar sama sekali. Menyerahkannya ke JavaScript berarti bidang 16:9
 * terlanjur berdiri di halaman, lalu berisi kekosongan.
 */
class Youtube
{
    /** Sebelas huruf id video, atau null kalau alamatnya tidak terbaca. */
    public static function id(?string $alamat): ?string
    {
        $alamat = trim((string) $alamat);

        if ($alamat === '') {
            return null;
        }

        /*
         * Awalan 'youtube:' dibuang lebih dulu. Album galeri menyimpan video
         * dengan awalan itu supaya lightbox bisa membedakannya dari foto, dan
         * alamat yang sama bisa saja disalin ke kolom ini.
         */
        $alamat = preg_replace('/^youtube:/i', '', $alamat);

        /*
         * Id video YouTube selalu 11 huruf dari [A-Za-z0-9_-]. Yang ditangkap
         * empat bentuk alamat yang benar-benar beredar; sisanya — termasuk id
         * telanjang yang diketik langsung — ditangkap cabang terakhir.
         */
        $pola = '~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([\w-]{11})~i';

        if (preg_match($pola, $alamat, $cocok) === 1) {
            return $cocok[1];
        }

        return preg_match('/^[\w-]{11}$/', $alamat) === 1 ? $alamat : null;
    }

    /**
     * Alamat sematan siap pakai, atau null.
     *
     * rel=0 menahan YouTube menawarkan video saluran lain saat videonya
     * selesai — di halaman perusahaan, deretan saran itu bisa berisi apa pun.
     * TANPA autoplay: video yang mulai sendiri di halaman yang dibuka untuk
     * melihat foto adalah gangguan, bukan sambutan.
     */
    public static function sematan(?string $alamat): ?string
    {
        $id = self::id($alamat);

        return $id ? 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0' : null;
    }
}
