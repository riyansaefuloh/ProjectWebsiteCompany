<?php

namespace App\Support;

/**
 * Lambang pengganti saat logo belum diunggah — inisial nama perusahaan.
 *
 * Kenapa monogram, bukan ikon "gambar kosong" seperti pada kartu produk:
 * placeholder produk mengisi SLOT ISI, dan di sana kotak abu berikon gambar
 * memang berarti "fotonya belum ada". Kepala situs bukan slot isi melainkan
 * chrome merek — kotak abu di sana terbaca sebagai gambar yang GAGAL DIMUAT,
 * bukan sebagai isian yang belum diisi.
 *
 * Dan kenapa bukan lambang bergambar seperti sebelumnya: lambang biji kopi
 * yang dulu dipakai berdiri persis di tempat logo seharusnya, jadi ia terbaca
 * sebagai logo — menghapus logo lalu tetap melihatnya membaca seperti
 * penghapusan yang gagal. Inisial tidak punya masalah itu: ia jelas diturunkan
 * dari nama yang tertulis di sebelahnya, bukan sesuatu yang dirancang.
 */
class Monogram
{
    /**
     * Satu atau dua huruf dari nama perusahaan.
     *
     * Bentuk badan hukum dibuang lebih dulu. "PT. Coffee Nusantara" berinisial
     * CN, bukan PC — huruf yang sama untuk setiap PT di Indonesia tidak
     * membedakan apa pun.
     */
    public static function inisial(?string $nama): string
    {
        $bentuk = ['pt', 'cv', 'ud', 'pd', 'tbk', 'persero', 'the'];

        $kata = preg_split('/[\s.,]+/u', (string) $nama, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $kata = array_values(array_filter(
            $kata,
            fn ($k) => ! in_array(mb_strtolower($k), $bentuk, true)
                    && preg_match('/^\p{L}/u', $k) === 1
        ));

        if ($kata === []) {
            return '';
        }

        /*
         * Nama satu kata cukup SATU huruf. Dua huruf dari kata yang sama —
         * "Nusantara" jadi "NU" — terbaca seperti singkatan yang tidak pernah
         * ada, sementara satu huruf terbaca jelas sebagai inisial.
         */
        $ambil = array_slice($kata, 0, 2);

        return mb_strtoupper(implode('', array_map(
            fn ($k) => mb_substr($k, 0, 1),
            $ambil
        )));
    }

    /**
     * Favicon bawaan: SVG monogram sebagai data URI.
     *
     * Selalu DIPASANG, termasuk ketika tidak ada favicon yang diunggah — dan
     * itu justru alasan utamanya.
     *
     * Halaman yang tidak menyebut ikon sama sekali membuat peramban jatuh ke
     * /favicon.ico, lalu mempertahankan ikon yang terakhir dikenalnya untuk
     * asal ini. Akibatnya favicon yang sudah dihapus tetap tampak di tab —
     * bukan karena aplikasinya masih menyimpannya, melainkan karena tidak ada
     * yang menggantikannya. Pernyataan yang eksplisit memutus itu: peramban
     * memakai apa yang disebutkan, bukan apa yang diingatnya.
     *
     * Ditulis sebagai data URI, bukan berkas: ia ikut berubah kalau nama
     * perusahaan diganti, dan tidak ada berkas yatim yang perlu dibersihkan.
     */
    public static function favicon(?string $nama, string $latar = '#332619', string $huruf = '#e2a862'): string
    {
        $inisial = self::inisial($nama);

        /*
         * Ukuran huruf mengikuti jumlahnya. Dua huruf pada ukuran satu huruf
         * akan meluber keluar bidangnya di 16px, dan yang terlihat di tab cuma
         * dua potongan huruf.
         */
        $ukuran = mb_strlen($inisial) > 1 ? 26 : 34;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
             . '<rect width="64" height="64" rx="14" fill="' . $latar . '"/>'
             . '<text x="32" y="32" fill="' . $huruf . '"'
             . ' font-family="Georgia, \'Times New Roman\', serif"'
             . ' font-size="' . $ukuran . '" font-weight="700"'
             . ' text-anchor="middle" dominant-baseline="central">'
             . e($inisial)
             . '</text></svg>';

        return 'data:image/svg+xml,' . rawurlencode($svg);
    }
}
