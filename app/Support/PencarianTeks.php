<?php

namespace App\Support;

class PencarianTeks
{
    public static function kueriAwalan(string $kata): ?string
    {
        $bersih = preg_split('/[^\p{L}\p{N}]+/u', $kata, -1, PREG_SPLIT_NO_EMPTY);

        if (! $bersih) {
            return null;
        }

        $bersih = array_slice($bersih, 0, 8);

        return implode(' & ', array_map(fn ($k) => $k . ':*', $bersih));
    }

    public static function kamus(): string
    {
        return 'simple';
    }
}
