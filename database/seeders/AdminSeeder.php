<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'Super Admin User',
                'password' => Hash::make('admin123')
            ]
        );
        $superAdmin->assignRole('super-admin');

        // 2. Buat User Admin CMS
        $adminCms = User::firstOrCreate(
            ['email' => 'cms@company.com'],
            [
                'name' => 'Admin CMS User',
                'password' => Hash::make('cms123')
            ]
        );
        $adminCms->assignRole('admin-cms');

        // 3. Buat User Sales
        $sales = User::firstOrCreate(
            ['email' => 'sales@company.com'],
            [
                'name' => 'Sales User',
                'password' => Hash::make('sales123')
            ]
        );
        $sales->assignRole('sales');
    }
}
