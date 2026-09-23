<?php

namespace App\Support;

class Judul
{
    public static function sorot(?string $teks): string
    {
        $aman = e((string) $teks);

        return preg_replace('/\*([^*]+)\*/u', '<em>$1</em>', $aman);
    }

    public static function polos(?string $teks): string
    {
        return preg_replace('/\*([^*]+)\*/u', '$1', (string) $teks);
    }
}
