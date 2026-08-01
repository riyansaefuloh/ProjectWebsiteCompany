<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslation
{
    /**
     * Mengambil atribut terjemahan berdasarkan field dan locale aktif.
     * 
     * Contoh penggunaan: $product->getTranslation('name', 'en')
     */
    public function getTranslation(string $field, ?string $locale = null, string $defaultLocale = 'en'): ?string
    {
        $targetLocale = $locale ?? App::getLocale();

        // Cari translasi sesuai locale yang diminta
        $translation = $this->translations
            ->firstWhere('locale', $targetLocale);

        // Jika tidak ditemukan pada locale aktif, fallback ke default locale (misal 'id')
        if (!$translation && $targetLocale !== $defaultLocale) {
            $translation = $this->translations
                ->firstWhere('locale', $defaultLocale);
        }

        return $translation ? $translation->{$field} : null;
    }

    /**
     * Accessor dinamis untuk memanggil properti terjemahan langsung.
     * Contoh: $product->translated_name, $product->translated_description
     */
    public function __get($key)
    {
        if (str_starts_with($key, 'translated_')) {
            $field = str_replace('translated_', '', $key);
            return $this->getTranslation($field);
        }

        return parent::__get($key);
    }
}
