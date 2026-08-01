<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company_name' => 'PT. Indo Export Global',
            'company_email' => 'info@indoexportglobal.com',
            'company_phone' => '+6281234567890',
            'whatsapp_number' => '+6281234567890',
            'company_address' => 'Jl. Jenderal Sudirman No. 123, Jakarta, Indonesia',
            'brand_color' => '#4f46e5',
            'timezone' => 'Asia/Jakarta',
            'google_analytics_id' => 'G-EX12345678',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
