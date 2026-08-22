<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate a single string using Google Translate (unofficial endpoint).
     * More accurate than MyMemory, especially for Indonesian.
     * No API key required for basic usage.
     */
    public function translate(string $text, string $from = 'id', string $to = 'en'): string
    {
        if (empty(trim($text))) {
            return '';
        }

        try {
            $url = 'https://translate.googleapis.com/translate_a/single';

            $response = Http::timeout(15)
                ->when(app()->environment('local'), fn($http) => $http->withoutVerifying())
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Laravel/TranslationService)',
                ])
                ->get($url, [
                    'client' => 'gtx',
                    'sl'     => $from,
                    'tl'     => $to,
                    'dt'     => 't',
                    'q'      => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Google Translate returns nested arrays: [[["translated","original",...],...],...]
                if (isset($data[0]) && is_array($data[0])) {
                    $translated = collect($data[0])
                        ->filter(fn($part) => isset($part[0]))
                        ->map(fn($part) => $part[0])
                        ->implode('');

                    return trim($translated);
                }
            }

            Log::error('Google Translate API error', ['status' => $response->status()]);
            return '';

        } catch (\Exception $e) {
            Log::error('TranslationService: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Translate multiple fields at once.
     *
     * Usage:
     *   $translated = app(TranslationService::class)->translateMany([
     *       'name'        => $this->name_id,
     *       'description' => $this->description_id,
     *   ]);
     *   $this->name_en        = $translated['name'];
     *   $this->description_en = $translated['description'];
     */
    public function translateMany(array $fields, string $from = 'id', string $to = 'en'): array
    {
        $results = [];

        foreach ($fields as $key => $text) {
            if (empty(trim((string) $text))) {
                $results[$key] = '';
                continue;
            }

            $results[$key] = $this->translate((string) $text, $from, $to);

            // Small delay between consecutive requests to avoid rate limiting
            if (count($fields) > 1) {
                usleep(300000); // 300ms
            }
        }

        return $results;
    }
}
