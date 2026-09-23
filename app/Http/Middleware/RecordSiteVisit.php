<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RecordSiteVisit
{
    private const ROBOT = '/bot|crawl|spider|slurp|search|fetch|monitor|preview|curl|wget|python|headless|lighthouse/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

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

    private function layakDicatat(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->hasHeader('X-Livewire') || $request->expectsJson()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        return ! preg_match(self::ROBOT, (string) $request->userAgent());
    }

    private function sidikJari(Request $request): string
    {
        $bahan = $request->hasSession()
            ? $request->session()->getId()
            : $request->ip() . '|' . $request->userAgent();

        return hash('sha256', $bahan . '|' . config('app.key'));
    }
}
