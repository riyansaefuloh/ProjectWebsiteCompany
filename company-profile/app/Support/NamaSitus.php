<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Nama perusahaan dari Pengaturan, dipakai sebagai config('app.name').
 *
 * Sebelum ini nama itu hidup di DUA tempat: APP_NAME di berkas .env untuk judul
 * tab dan berkas PDF, dan settings.company_name untuk bilah kepala dan kaki
 * situs. Dua sumber untuk satu nama selalu berakhir berbeda — dan memang
 * begitu: judul tiap tab bertuliskan "Laravel" sementara kepala situs
 * bertuliskan "Coffee Nusantara".
 *
 * Sekarang tabel settings yang jadi sumbernya, dan .env cuma cadangan kalau
 * tabelnya belum terisi. Nama yang diubah dari panel langsung mengubah judul
 * tab dan hasil pencarian, tanpa menyentuh berkas lingkungan.
 */
final class NamaSitus
{
    public const KUNCI = 'nama-situs';

    /**
     * Nama yang berlaku sekarang.
     *
     * TIDAK PERNAH melempar. Ia dipanggil dari boot() service provider, yang
     * ikut berjalan saat `migrate` di basis data kosong dan saat basis datanya
     * sedang tidak bisa dihubungi. Galat di sana berarti seluruh aplikasi mati
     * — termasuk perintah yang justru dipakai untuk memperbaikinya.
     */
    public static function ambil(): string
    {
        $cadangan = (string) config('app.name', 'Export Company');

        try {
            /* Mengembalikan null saat tabelnya belum ada berarti TIDAK
               tersinggahkan: Cache::remember menganggap null sebagai luput dan
               menjalankan ulang penutupnya. Persis yang diinginkan — begitu
               pemasangannya selesai, panggilan berikutnya menemukan namanya. */
            $nama = Cache::rememberForever(self::KUNCI, function () {
                if (! Schema::hasTable('settings')) {
                    return null;
                }

                return Setting::where('key', 'company_name')->value('value') ?: null;
            });
        } catch (Throwable) {
            return $cadangan;
        }

        return filled($nama) ? (string) $nama : $cadangan;
    }

    /**
     * Dipanggil dari model Setting tiap kali barisnya berubah.
     */
    public static function lupakan(): void
    {
        try {
            Cache::forget(self::KUNCI);
        } catch (Throwable) {
            // Singgahan yang tidak bisa dihapus bukan alasan menggagalkan simpan.
        }
    }
}
