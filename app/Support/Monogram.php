<?php

namespace App\Support;

class Monogram
{
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

        $ambil = array_slice($kata, 0, 2);

        return mb_strtoupper(implode('', array_map(
            fn ($k) => mb_substr($k, 0, 1),
            $ambil
        )));
    }

    public static function favicon(?string $nama, string $latar = '#332619', string $huruf = '#e2a862'): string
    {
        $inisial = self::inisial($nama);

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
