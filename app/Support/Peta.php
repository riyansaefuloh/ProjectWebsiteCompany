<?php

namespace App\Support;

class Peta
{
    public static function sematan(?string $tautan, ?string $alamat = null): ?string
    {
        $tautan = trim((string) $tautan);

        if ($tautan !== '' && str_contains($tautan, '/maps/embed')) {
            return $tautan;
        }

        if ($tautan !== '') {
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $tautan, $c) === 1) {
                return self::kueri($c[1] . ',' . $c[2]);
            }

            if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $tautan, $c) === 1) {
                return self::kueri($c[1] . ',' . $c[2]);
            }

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

        return filled($alamat) ? self::kueri((string) $alamat) : null;
    }

    public static function perluCadangan(?string $tautan): bool
    {
        $tautan = trim((string) $tautan);

        if ($tautan === '') {
            return false;
        }

        return self::sematan($tautan) === null;
    }

    private static function kueri(string $q): string
    {
        return 'https://maps.google.com/maps?q=' . rawurlencode($q)
             . '&hl=' . app()->getLocale() . '&z=15&output=embed';
    }
}
