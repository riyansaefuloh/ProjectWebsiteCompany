<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class NamaSitus
{
    public const KUNCI = 'nama-situs';

    public static function ambil(): string
    {
        $cadangan = (string) config('app.name', 'Export Company');

        try {
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

    public static function lupakan(): void
    {
        try {
            Cache::forget(self::KUNCI);
        } catch (Throwable) {
            //
        }
    }
}
