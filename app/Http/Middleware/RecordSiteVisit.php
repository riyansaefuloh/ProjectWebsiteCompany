<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mencatat kunjungan halaman publik.
 *
 * Dipasang HANYA pada kelompok rute publik di routes/web.php, bukan pada
 * seluruh kelompok `web`. Itu yang membuat panel admin, endpoint Livewire, dan
 * rute login tidak pernah ikut terhitung — bukan daftar pengecualian yang harus
 * diingat dan ditambah tiap kali ada rute baru.
 */
class RecordSiteVisit
{
    /**
     * Perayap yang tidak dihitung sebagai kunjungan.
     *
     * Tanpa saringan ini, angka di dasbor naik tiap kali Google mengindeks
     * ulang — dan yang paling menyesatkan bukan angkanya yang membesar,
     * melainkan grafiknya yang melonjak pada hari yang tidak ada hubungannya
     * dengan apa pun yang dikerjakan tim pemasaran.
     */
    private const ROBOT = '/bot|crawl|spider|slurp|search|fetch|monitor|preview|curl|wget|python|headless|lighthouse/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /*
         * Dicatat SESUDAH tanggapan tersusun, dan dibungkus try.
         *
         * Kunjungan dicatat demi laporan; halaman disajikan demi pembeli. Kalau
         * basis datanya sedang penuh atau tabelnya belum dimigrasikan, yang
         * pantas terjadi adalah satu baris hilang dari laporan — bukan layar
         * galat di beranda situs perusahaan.
         */
        try {
            if ($this->layakDicatat($request, $response)) {
                SiteVisit::create([
                    'path'       => mb_substr($request->path(), 0, 255),
                    'visitor'    => $this->sidikJari($request),
                    'visited_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Kunjungan gagal dicatat: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Halaman yang benar-benar dilihat orang — bukan pengalihan, bukan
     * potongan yang diminta di latar, bukan berkas yang diunduh.
     */
    private function layakDicatat(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Permintaan latar: penyegaran Livewire, pengambilan JSON, prefetch.
        if ($request->ajax() || $request->hasHeader('X-Livewire') || $request->expectsJson()) {
            return false;
        }

        // 301/302 belum halaman — pengalihan bahasa akan terhitung dua kali
        // untuk satu kunjungan yang sama.
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        return ! preg_match(self::ROBOT, (string) $request->userAgent());
    }

    /**
     * Sidik jari pengunjung — satu arah, tidak bisa dikembalikan jadi orang.
     *
     * Bahan utamanya id sesi, karena ia sudah menandai satu peramban tanpa
     * menyentuh apa pun yang bersifat pribadi. Kalau sesinya belum ada,
     * dipakai alamat dan tanda peramban sebagai gantinya. Keduanya diaduk
     * dengan APP_KEY supaya nilai yang tersimpan tidak bisa dicocokkan dengan
     * menebak bahannya dari luar.
     */
    private function sidikJari(Request $request): string
    {
        $bahan = $request->hasSession()
            ? $request->session()->getId()
            : $request->ip() . '|' . $request->userAgent();

        return hash('sha256', $bahan . '|' . config('app.key'));
    }
}
