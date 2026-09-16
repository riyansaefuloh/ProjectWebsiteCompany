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

        // Jika tidak ditemukan pada locale aktif, fallback ke default locale 
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
            return $this->getTranslation($this->namaBidangTerjemahan($key));
        }

        return parent::__get($key);
    }

    /**
     * Menjawab isset(), ??, dan empty() untuk properti translated_*.
     *
     * Tanpa ini PHP tidak pernah memanggil __get() untuk ketiga hal itu: ia
     * bertanya __isset() lebih dulu, jatuh ke milik Eloquent, yang tidak tahu
     * apa-apa soal 'translated_name' dan menjawab false. Akibatnya
     *
     *     {{ $product->translated_name ?? 'Product Name' }}
     *
     * SELALU mencetak cadangannya walau terjemahannya ada — itulah yang
     * membuat PDF katalog penuh tulisan "Product Name" dan judul meta halaman
     * jatuh ke slug.
     */
    public function __isset($key): bool
    {
        if (str_starts_with($key, 'translated_')) {
            return $this->getTranslation($this->namaBidangTerjemahan($key)) !== null;
        }

        return parent::__isset($key);
    }

    private function namaBidangTerjemahan(string $key): string
    {
        return substr($key, strlen('translated_'));
    }
}
