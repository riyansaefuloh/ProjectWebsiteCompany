<?php

namespace App\Providers;

use App\Support\NamaSitus;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Timpa APP_NAME dengan nama perusahaan dari Pengaturan
        config(['app.name' => NamaSitus::ambil()]);
    }
}
