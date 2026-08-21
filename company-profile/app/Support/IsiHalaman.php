<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Isi teks halaman publik yang bisa disunting dari menu Halaman.
 *
 * Beranda menyimpan isinya menumpang di larik urutan bagiannya sendiri
 * ('home_sections'). Halaman lain tidak punya urutan untuk ditumpangi, jadi
 * isinya berdiri di kunci pengaturannya sendiri, 'page_contents':
 *
 *     { "certifications": { "isi": { "en": { "title": "…" },
 *                                    "id": { "title": "…" } },
 *                           "image": "settings/xxx.jpg" },
 *       "profile": { … } }
 *
 * Isian yang DIKOSONGKAN jatuh ke teks bawaan di berkas bahasa. Itu disengaja:
 * satu halaman tidak boleh tergambar hampa hanya karena satu kolom di panel
 * belum diisi, dan bawaan yang sudah diterjemahkan lebih baik daripada ruang
 * kosong.
 */
class IsiHalaman
{
    public const KUNCI = 'page_contents';

    /**
     * Membuat penutup pembaca isi untuk satu halaman.
     *
     * Dipakai di render() tiap komponen publik:
     *
     *     'isi' => IsiHalaman::untuk('certifications'),
     *
     * lalu di bladenya cukup $isi('title', 'site.page_certifications').
     *
     * Bahasa yang aktif dicoba lebih dulu, lalu bahasa cadangan, baru teks
     * bawaannya. Urutan itu sama dengan yang dipakai beranda, supaya perilaku
     * di seluruh situs tidak berbeda-beda menurut halamannya.
     */
    public static function untuk(string $halaman): \Closure
    {
        $sumber   = self::semua()[$halaman]['isi'] ?? [];
        $bahasa   = app()->getLocale();
        $cadangan = config('app.fallback_locale', 'en');

        /*
         * $lawas: nilai lama dari tempat penyimpanan sebelumnya, dicoba SESUDAH
         * kedua bahasa tapi SEBELUM teks bawaan.
         *
         * Dipakai selama masa perpindahan — misalnya isi halaman Profile yang
         * dulu ditulis sebagai halaman statis 'about-us'. Tanpa itu, isi yang
         * belum sempat dipindahkan akan langsung tergantikan teks bawaan, dan
         * tulisan yang sudah dibuat orang lenyap dari layar tanpa sebab yang
         * terlihat.
         */
        return function (string $nama, string $bawaan, array $ganti = [], ?string $lawas = null)
            use ($sumber, $bahasa, $cadangan) {
            foreach ([$bahasa, $cadangan] as $lokal) {
                $nilai = $sumber[$lokal][$nama] ?? null;

                if (filled($nilai)) {
                    /*
                     * Penanda seperti :count diganti DI SINI juga, bukan cuma
                     * lewat __(). Teks yang diketik pemakai tidak melewati
                     * berkas bahasa sama sekali — tanpa baris ini, judul buatan
                     * sendiri akan tergambar apa adanya beserta ":count".
                     */
                    foreach ($ganti as $kunci => $isi) {
                        $nilai = str_replace(':' . $kunci, (string) $isi, $nilai);
                    }

                    return $nilai;
                }
            }

            if (filled($lawas)) {
                return $lawas;
            }

            return __($bawaan, $ganti);
        };
    }

    /**
     * Tahun berdiri perusahaan.
     *
     * Disunting dari bagian Sejarah di halaman Profile, tapi tersimpan sebagai
     * kunci pengaturannya sendiri — ia dibaca dua tempat yang berbeda: garis
     * waktu di halaman Profile dan angka "tahun pengalaman" di hero beranda.
     * Menaruhnya di dalam isi halaman Profile akan membuat beranda bergantung
     * pada isi halaman lain.
     *
     * Kosong berarti tahun berjalan: garis waktunya jadi setitik, dan angka
     * pengalamannya jadi satu. Itu keliru, tapi tetap tergambar — lebih baik
     * daripada halaman yang mati karena pembagian dengan nol.
     */
    public static function tahunBerdiri(): int
    {
        $nilai = (int) (Setting::where('key', 'established_year')->value('value') ?: 0);

        return $nilai >= 1900 ? $nilai : (int) date('Y');
    }

    /**
     * Alamat foto satu halaman, atau null kalau belum ada.
     */
    public static function gambar(string $halaman): ?string
    {
        $alamat = self::semua()[$halaman]['image'] ?? null;

        return filled($alamat) ? $alamat : null;
    }

    /**
     * Seluruh isi halaman, apa adanya dari pengaturan.
     *
     * Dibaca sekali per permintaan lalu diingat: satu halaman publik bisa
     * memanggil untuk() dan gambar() beberapa kali, dan tanpa ini tiap
     * panggilan menembak kuerinya sendiri.
     */
    public static function semua(): array
    {
        if (self::$ingatan !== null) {
            return self::$ingatan;
        }

        $mentah = Setting::where('key', self::KUNCI)->value('value');
        $terurai = $mentah ? json_decode($mentah, true) : null;

        // json_decode() mengembalikan null untuk JSON yang rusak. Tanpa
        // pemeriksaan ini, seluruh situs publik mati dengan galat atas null.
        return self::$ingatan = is_array($terurai) ? $terurai : [];
    }

    /**
     * Melupakan yang sudah diingat.
     *
     * Dipanggil sesudah panel menyimpan, dan oleh uji yang mengubah pengaturan
     * lalu menggambar ulang halamannya dalam proses yang sama — tanpa ini,
     * yang tergambar adalah isi sebelum perubahan.
     */
    public static function lupakan(): void
    {
        self::$ingatan = null;
    }

    /** Isi yang sudah dibaca dari basis data pada permintaan ini. */
    private static ?array $ingatan = null;
}
