<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Data Kategori & Translasinya
        $categoriesData = [
            [
                'slug' => 'coconut-products',
                'icon' => 'coconut-icon',
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    'id' => [
                        'name' => 'Turunan Kelapa',
                        'description' => 'Produk kelapa berkualitas tinggi seperti briket arang, kelapa parut kering, dan VCO.'
                    ],
                    'en' => [
                        'name' => 'Coconut Derivatives',
                        'description' => 'Premium coconut products including charcoal briquettes, desiccated coconut, and VCO.'
                    ],
                ]
            ],
            [
                'slug' => 'coffee-and-spices',
                'icon' => 'coffee-icon',
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    'id' => [
                        'name' => 'Kopi & Rempah',
                        'description' => 'Biji kopi pilihan Nusantara dan rempah-rempah eksotis Indonesia.'
                    ],
                    'en' => [
                        'name' => 'Coffee & Spices',
                        'description' => 'Select Indonesian coffee beans and exotic premium spices.'
                    ],
                ]
            ],
        ];

        foreach ($categoriesData as $data) {
            // 1. Buat Kategori Induk
            $category = Category::create([
                'slug' => $data['slug'],
                'icon' => $data['icon'],
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
            ]);

            // 2. Simpan Translasinya
            foreach ($data['translations'] as $locale => $trans) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                ]);
            }
        }
    }
}
