<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder sesuai urutan ketergantungan relasinya
        $this->call([
            RolePermissionSeeder::class, // Otorisasi
            AdminSeeder::class,          // User Admin
            SettingSeeder::class,        // Config Web
            CategorySeeder::class,       // Kategori Produk
            CertificationSeeder::class,  // Sertifikat Global
            ExportMarketSeeder::class,   // Negara Tujuan Ekspor
            ProductSeeder::class,        // Produk Kopi (Bergantung pada Kategori & Sertifikat)
            NewsSeeder::class,           // Berita Ekspor Kopi (Bergantung pada User Admin)
            GallerySeeder::class,        // Foto & Video Fasilitas
            DownloadSeeder::class,       // Katalog PDF
            PageSeeder::class,           // Halaman Statis (About, Privacy)
        ]);
    }
}
