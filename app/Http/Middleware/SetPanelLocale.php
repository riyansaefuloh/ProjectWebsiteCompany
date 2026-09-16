<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyetel bahasa panel admin.
 *
 * Rute publik memakai awalan bahasa dan diatur laravel-localization, tapi rute
 * /admin berada di luar grup itu — jadi bahasanya selalu jatuh ke APP_LOCALE,
 * yang bernilai 'en'.
 *
 * Akibatnya seluruh panel yang tertulis dalam bahasa Indonesia tetap
 * memunculkan pesan galat berbahasa Inggris: "The company name field is
 * required." Berkas lang/id/validation.php tidak akan pernah tersentuh selama
 * bahasa aktifnya masih 'en'.
 *
 * Bahasanya diambil dari config supaya bisa diubah lewat .env tanpa menyunting
 * kode — panel yang nanti dipakai staf berbahasa Inggris tinggal menyetel
 * APP_PANEL_LOCALE=en.
 */
class SetPanelLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(config('app.panel_locale', 'id'));

        return $next($request);
    }
}
