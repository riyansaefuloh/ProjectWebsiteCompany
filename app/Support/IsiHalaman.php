<?php

namespace App\Support;

use App\Models\Setting;

class IsiHalaman
{
    public const KUNCI = 'page_contents';

    public static function untuk(string $halaman): \Closure
    {
        $sumber   = self::semua()[$halaman]['isi'] ?? [];
        $bahasa   = app()->getLocale();
        $cadangan = config('app.fallback_locale', 'en');

        return function (string $nama, string $bawaan, array $ganti = [], ?string $lawas = null)
            use ($sumber, $bahasa, $cadangan) {
            foreach ([$bahasa, $cadangan] as $lokal) {
                $nilai = $sumber[$lokal][$nama] ?? null;

                if (filled($nilai)) {
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

    public static function tahunBerdiri(): int
    {
        $nilai = (int) (Setting::where('key', 'established_year')->value('value') ?: 0);

        return $nilai >= 1900 ? $nilai : (int) date('Y');
    }

    public static function opsi(string $halaman): array
    {
        $nilai = self::semua()[$halaman]['opsi'] ?? [];

        return is_array($nilai) ? $nilai : [];
    }

    public static function gambar(string $halaman): ?string
    {
        $alamat = self::semua()[$halaman]['image'] ?? null;

        return filled($alamat) ? $alamat : null;
    }

    public static function gambarTonggak(string $halaman): array
    {
        $nilai = self::semua()[$halaman]['milestone_images'] ?? [];

        return is_array($nilai) ? array_filter($nilai, 'filled') : [];
    }

    public static function semua(): array
    {
        if (self::$ingatan !== null) {
            return self::$ingatan;
        }

        $mentah = Setting::where('key', self::KUNCI)->value('value');
        $terurai = $mentah ? json_decode($mentah, true) : null;

        return self::$ingatan = is_array($terurai) ? $terurai : [];
    }

    public static function lupakan(): void
    {
        self::$ingatan = null;
    }

    private static ?array $ingatan = null;
}
