<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslation
{
    public function getTranslation(string $field, ?string $locale = null, string $defaultLocale = 'en'): ?string
    {
        $targetLocale = $locale ?? App::getLocale();

        $translation = $this->translations
            ->firstWhere('locale', $targetLocale);

        if (!$translation && $targetLocale !== $defaultLocale) {
            $translation = $this->translations
                ->firstWhere('locale', $defaultLocale);
        }

        return $translation ? $translation->{$field} : null;
    }

    public function __get($key)
    {
        if (str_starts_with($key, 'translated_')) {
            return $this->getTranslation($this->namaBidangTerjemahan($key));
        }

        return parent::__get($key);
    }

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
