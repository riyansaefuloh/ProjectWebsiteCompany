<?php

namespace Database\Seeders;

use App\Models\Download;
use Illuminate\Database\Seeder;

class DownloadSeeder extends Seeder
{
    public function run(): void
    {
        Download::create([
            'title' => 'Indonesian Premium Coffee Bean Catalog',
            'file_path' => 'downloads/coffee-catalog-2026.pdf',
            'require_email' => true,
            'download_count' => 0,
            'sort_order' => 1,
        ]);
    }
}
