<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Sebab kegagalan terakhir, untuk ditampilkan pemanggilnya.
     *
     * Kosong berarti terjemahan terakhir berhasil. Ini ADA karena tanpanya
     * kegagalan tidak meninggalkan jejak apa pun di layar: pemanggil menerima
     * untai kosong, memeriksanya dengan !empty(), lalu diam. Bagi yang menekan
     * tombolnya, tidak terjadi apa-apa — dan "tidak terjadi apa-apa" adalah
     * satu-satunya keadaan yang tidak bisa dibedakan dari tombol rusak.
     */
    public ?string $sebabGagal = null;

    /**
     * Menerjemahkan satu untai, dengan DUA sumber.
     *
     * Google dicoba lebih dulu — hasilnya lebih baik untuk bahasa Indonesia.
     * Tapi titik ujung yang dipakai di sini tidak resmi dan tanpa kunci API,
     * jadi Google membatasinya per alamat IP: sesudah beberapa permintaan ia
     * menjawab 429 dan berhenti menjawab sama sekali untuk waktu yang tidak
     * diumumkan. Itu bukan kemungkinan yang jauh — ia terjadi di mesin ini,
     * dan seluruh tombol "Terjemahkan" di panel berhenti bekerja tanpa satu
     * pun pesan.
     *
     * MyMemory jadi cadangannya: juga bebas dan tanpa kunci, batasnya lain,
     * dan mutunya cukup untuk nama dan keterangan pendek. Ia dipanggil HANYA
     * kalau Google gagal, jadi selama Google mau menjawab, hasilnya tidak
     * berubah dari sebelumnya.
     */
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

    /**
     * Google Translate, titik ujung tidak resmi.
     *
     * Jawabannya larik bersarang: [[["hasil","asli",...],...],...] — dan untuk
     * kalimat panjang ia dipecah jadi beberapa bagian yang harus disambung
     * lagi.
     */
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

            /*
             * 429 dicatat sebagai peringatan, bukan galat: ia keadaan yang
             * memang diperkirakan terjadi pada titik ujung tanpa kunci, dan
             * mencatatnya sebagai galat membuat berkas log penuh oleh sesuatu
             * yang sudah ada penanganannya.
             */
            $response->status() === 429
                ? Log::warning('Google Translate membatasi permintaan (429), beralih ke MyMemory')
                : Log::error('Google Translate menjawab tidak wajar', ['status' => $response->status()]);

            return '';
        } catch (\Throwable $e) {
            Log::error('Google Translate: ' . $e->getMessage());

            return '';
        }
    }

    /**
     * MyMemory — cadangan.
     *
     * Jawabannya datar: responseData.translatedText. Ia juga mengembalikan
     * angka mutu (match); yang di bawah 0,4 dibuang, karena pada nilai
     * serendah itu MyMemory biasanya cuma mengembalikan untai aslinya atau
     * tebakan yang jauh — dan terjemahan yang salah lebih merepotkan daripada
     * kolom yang dibiarkan kosong untuk diisi sendiri.
     */
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

        /*
         * Sebab kegagalan dikumpulkan SENDIRI di sini, bukan dibiarkan pada
         * $this->sebabGagal.
         *
         * translate() mengosongkan sebabGagal setiap kali dipanggil, jadi
         * kalau kolom pertama gagal dan kolom kedua berhasil, sebabnya hilang
         * dan pemanggilnya menyimpulkan semuanya baik-baik saja — padahal satu
         * kolom tertinggal kosong tanpa penjelasan.
         */
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

            // Small delay between consecutive requests to avoid rate limiting
            if (count($fields) > 1) {
                usleep(300000); // 300ms
            }
        }

        $this->sebabGagal = $gagalPertama;

        return $results;
    }
}
