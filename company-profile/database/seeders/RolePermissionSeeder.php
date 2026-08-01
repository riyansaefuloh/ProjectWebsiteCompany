<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache permission bawaan spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definisikan semua Permission berdasarkan matriks
        $permissions = [
            // Kelola User & Role
            'manage users',

            // Kelola Konten (Produk, Kategori, Berita, Galeri)
            'manage products',
            'manage categories',
            'manage news',
            'manage galleries',

            // Kelola Sertifikasi & Pasar Ekspor
            'manage certifications',
            'manage export markets',

            // Kelola Unduhan & Halaman
            'manage downloads',
            'manage pages',

            // Inquiry (Pesan Masuk)
            'view inquiries',    // Hanya melihat list & detail
            'manage inquiries',  // Mengubah status, membalas, menghapus
            'export inquiries',  // Mengekspor data inquiry

            // Pengaturan Global
            'manage global settings',   // Pengaturan penuh
            'manage partial settings',  // Pengaturan sebagian
        ];

        // Buat atau update semua permission di database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 2. Buat / Ambil Role
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminCms   = Role::firstOrCreate(['name' => 'admin-cms', 'guard_name' => 'web']);
        $sales      = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);

        // 3. Assign Permission ke masing-masing Role

        // A. Super Admin mendapatkan semua permission
        $superAdmin->syncPermissions(Permission::all());

        // B. Admin CMS (Sesuai matriks)
        $adminCms->syncPermissions([
            'manage products',
            'manage categories',
            'manage news',
            'manage galleries',
            'manage certifications',
            'manage export markets',
            'manage downloads',
            'manage pages',
            'view inquiries',           // Hanya melihat (tidak bisa kelola/export)
            'manage partial settings',  // Pengaturan sebagian
        ]);

        // C. Sales (Sesuai matriks)
        $sales->syncPermissions([
            'view inquiries',    // Bisa melihat list
            'manage inquiries',  // Bisa mengelola (misal membalas pesan masuk)
            'export inquiries',  // Bisa meng-export
        ]);
    }
}
