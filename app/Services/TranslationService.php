<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    public ?string $sebabGagal = null;

    public function translate(string $text, string $from = 'id', string $to = 'en'): string
    {
        $this->sebabGagal = null;

        if (empty(trim($text))) {
            return '';
        }

        $hasil = $this->lewatGoogle($text, $from, $to);

        if ($hasil !== '') {
            return $hasil;
        }

        $hasil = $this->lewatMyMemory($text, $from, $to);

        if ($hasil !== '') {
            return $hasil;
        }

        $this->sebabGagal = 'Layanan terjemahan sedang tidak bisa dihubungi. '
                          . 'Coba lagi beberapa saat, atau isi terjemahannya sendiri.';

        return '';
    }

    private function lewatGoogle(string $text, string $from, string $to): string
    {
        try {
            $response = Http::timeout(15)
                ->when(app()->environment('local'), fn ($http) => $http->withoutVerifying())
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Laravel/TranslationService)',
                ])
                ->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl'     => $from,
                    'tl'     => $to,
                    'dt'     => 't',
                    'q'      => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data[0]) && is_array($data[0])) {
                    return trim(
                        collect($data[0])
                            ->filter(fn ($part) => isset($part[0]))
                            ->map(fn ($part) => $part[0])
                            ->implode('')
                    );
                }
            }

            $response->status() === 429
                ? Log::warning('Google Translate membatasi permintaan (429), beralih ke MyMemory')
                : Log::error('Google Translate menjawab tidak wajar', ['status' => $response->status()]);

            return '';
        } catch (\Throwable $e) {
            Log::error('Google Translate: ' . $e->getMessage());

            return '';
        }
    }

    private function lewatMyMemory(string $text, string $from, string $to): string
    {
        try {
            $response = Http::timeout(15)
                ->when(app()->environment('local'), fn ($http) => $http->withoutVerifying())
                ->get('https://api.mymemory.translated.net/get', [
                    'q'        => $text,
                    'langpair' => $from . '|' . $to,
                ]);

            if (! $response->successful()) {
                Log::error('MyMemory menjawab tidak wajar', ['status' => $response->status()]);

                return '';
            }

            $data  = $response->json();
            $hasil = trim((string) ($data['responseData']['translatedText'] ?? ''));
            $mutu  = (float) ($data['responseData']['match'] ?? 0);

            if ($hasil === '' || $mutu < 0.4) {
                Log::warning('MyMemory: mutu terjemahan terlalu rendah', ['match' => $mutu]);

                return '';
            }

            return $hasil;
        } catch (\Throwable $e) {
            Log::error('MyMemory: ' . $e->getMessage());

            return '';
        }
    }

    public function translateMany(array $fields, string $from = 'id', string $to = 'en'): array
    {
        $results = [];
        $gagalPertama = null;

        foreach ($fields as $key => $text) {
            if (empty(trim((string) $text))) {
                $results[$key] = '';
                continue;
            }

            $results[$key] = $this->translate((string) $text, $from, $to);

            if ($results[$key] === '' && $gagalPertama === null) {
                $gagalPertama = $this->sebabGagal;
            }

            if (count($fields) > 1) {
                usleep(300000);
            }
        }

        $this->sebabGagal = $gagalPertama;

        return $results;
    }
}
