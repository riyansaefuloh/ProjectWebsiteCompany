<?php

namespace App\Providers;

use App\Support\NamaSitus;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Nama perusahaan dari Pengaturan menimpa APP_NAME.
         *
         * Disetel di sini, bukan di tiap tempat yang membacanya: config('app.name')
         * sudah dipanggil dari sebelas komponen publik untuk judul SEO, dari
         * halaman galat, dan dari layanan PDF. Menggantinya satu per satu berarti
         * yang berikutnya ditambahkan akan lupa lagi.
         */
        config(['app.name' => NamaSitus::ambil()]);
    }
}
