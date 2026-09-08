<?php

namespace App\Support;

/**
 * Pengubah alamat Google Maps jadi alamat yang bisa DISEMAT.
 *
 * Yang disalin orang dari Google Maps hampir selalu tautan berbagi —
 * "maps.app.goo.gl/…" dari tombol Share, atau alamat panjang berawalan
 * "google.com/maps/place/…" dari bilah alamat peramban. Keduanya menolak
 * disemat: Google mengirim X-Frame-Options pada halaman petanya, jadi
 * iframe-nya tergambar kosong tanpa satu pesan galat pun. Itu yang terjadi di
 * halaman Kontak — tautannya tersimpan benar, petanya tetap tidak muncul.
 *
 * Yang bisa disemat cuma dua bentuk: alamat "/maps/embed" dari tombol
 * "Embed a map", dan bentuk kueri "?q=…&output=embed" yang tidak menuntut
 * kunci API. Kelas ini mengubah apa pun yang diberikan jadi salah satunya, dan
 * kalau tidak ada yang bisa dibaca, jatuh ke ALAMAT PERUSAHAAN — yang toh
 * sudah diisi di panel yang sama dan menunjuk tempat yang sama.
 */
class Peta
{
    /**
     * Alamat siap semat, atau null kalau tidak ada yang bisa dipakai.
     */
    public static function sematan(?string $tautan, ?string $alamat = null): ?string
    {
        $tautan = trim((string) $tautan);

        /*
         * Sudah berupa alamat sematan — dipakai apa adanya. Ini keluaran tombol
         * "Embed a map" di Google Maps, dan yang paling tepat: ia membawa
         * bingkai peta, tingkat perbesaran, dan penandanya sekaligus.
         */
        if ($tautan !== '' && str_contains($tautan, '/maps/embed')) {
            return $tautan;
        }

        /*
         * Koordinat di dalam alamat panjang. Dua bentuk yang beredar:
         *   .../@-6.2088,106.8456,15z/...      ← dari bilah alamat
         *   ...!3d-6.2088!4d106.8456...        ← dari tautan tempat
         */
        if ($tautan !== '') {
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $tautan, $c) === 1) {
                return self::kueri($c[1] . ',' . $c[2]);
            }

            if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $tautan, $c) === 1) {
                return self::kueri($c[1] . ',' . $c[2]);
            }

            /*
             * Parameter q= atau query= pada alamat pencarian.
             */
            $bagian = parse_url($tautan, PHP_URL_QUERY);

            if (is_string($bagian)) {
                parse_str($bagian, $par);

                foreach (['q', 'query'] as $kunci) {
                    if (filled($par[$kunci] ?? null)) {
                        return self::kueri((string) $par[$kunci]);
                    }
                }
            }
        }

        /*
         * Cadangan terakhir: alamat perusahaan. Tautan pendek maps.app.goo.gl
         * tidak memuat koordinat apa pun — yang menyimpannya cuma Google — dan
         * membukanya dari sisi peladen berarti satu permintaan jaringan pada
         * tiap kali halaman digambar. Alamat teksnya menunjuk tempat yang sama
         * dan sudah ada di panel yang sama.
         */
        return filled($alamat) ? self::kueri((string) $alamat) : null;
    }

    /**
     * Apakah tautannya perlu jalur cadangan — dipakai panel untuk memberi tahu
     * bahwa yang ditempel tidak bisa disemat apa adanya.
     */
    public static function perluCadangan(?string $tautan): bool
    {
        $tautan = trim((string) $tautan);

        if ($tautan === '') {
            return false;
        }

        return self::sematan($tautan) === null;
    }

    /*
     * maps.google.com, BUKAN www.google.com.
     *
     * Keduanya menerima ?output=embed, tapi cuma yang pertama yang benar-benar
     * bisa dibingkai: www.google.com/maps menjawab dengan
     * "X-Frame-Options: SAMEORIGIN" dan peramban menolak menggambarnya, jadi
     * yang terlihat bidang kosong. Diperiksa langsung terhadap tanggapan
     * keduanya, bukan dikira-kira.
     */
    private static function kueri(string $q): string
    {
        return 'https://maps.google.com/maps?q=' . rawurlencode($q)
             . '&hl=' . app()->getLocale() . '&z=15&output=embed';
    }
}
