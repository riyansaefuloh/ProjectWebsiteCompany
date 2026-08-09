<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ============================================================
// SCHEDULER OTOMATIS — PRD Bab 10.4 (Backup DB Terjadwal)
// ============================================================

// Sitemap — generate ulang setiap hari tengah malam
Schedule::command('sitemap:generate')->daily();

// Backup DB — dijalankan setiap hari jam 01:00 WIB (18:00 UTC)
// Menyimpan dump PostgreSQL + file ke storage/app/Laravel/
Schedule::command('backup:run --only-db')
    ->dailyAt('18:00')                                        // 01:00 WIB = 18:00 UTC
    ->appendOutputTo(storage_path('logs/backup.log'))         // Log output ke file
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[BACKUP] Backup database harian GAGAL! Periksa storage/logs/backup.log');
    });

// Cleanup backup lama — hapus backup sesuai retention policy di config/backup.php
// (default: simpan 7 hari harian, 8 minggu mingguan, 4 bulan bulanan)
Schedule::command('backup:clean')
    ->dailyAt('18:30')                                        // 30 menit setelah backup selesai
    ->appendOutputTo(storage_path('logs/backup.log'));
