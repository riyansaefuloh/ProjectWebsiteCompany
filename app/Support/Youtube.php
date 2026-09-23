<?php

namespace App\Support;

class Youtube
{
    public static function id(?string $alamat): ?string
    {
        $alamat = trim((string) $alamat);

        if ($alamat === '') {
            return null;
        }

        $alamat = preg_replace('/^youtube:/i', '', $alamat);

        $pola = '~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([\\w-]{11})~i';

        if (preg_match($pola, $alamat, $cocok) === 1) {
            return $cocok[1];
        }

        return preg_match('/^[\w-]{11}$/', $alamat) === 1 ? $alamat : null;
    }

    public static function sematan(?string $alamat): ?string
    {
        $id = self::id($alamat);

        return $id ? 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0' : null;
    }
}
