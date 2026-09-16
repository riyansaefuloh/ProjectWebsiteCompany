<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Certification;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Kopi & Rempah Category ID
        $category = Category::where('slug', 'coffee-and-spices')->first();
        if (!$category) return;

        // 2. Ambil Sertifikasi untuk direlasikan ke produk
        $iso = Certification::where('slug', 'iso-9001')->first();
        $haccp = Certification::where('slug', 'haccp')->first();

        // 3. Data Produk Kopi
        $productsData = [
            [
                'slug' => 'arabica-gayo-coffee-beans',
                'hs_code' => '0901.11.10',
                'moq' => '1 x 20ft Container (approx. 18 Metric Tons)',
                'supply_capacity' => '50 Metric Tons / Month',
                'packaging' => 'Jute Bags (60 kg) with GrainPro lining',
                'origin' => 'Gayo Highlands, Aceh, Indonesia',
                'indicative_price' => 4500.00, // FOB USD per Ton
                'currency' => 'USD',
                'incoterms' => 'FOB, CIF',
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 1,
                'translations' => [
                    'id' => [
                        'name' => 'Biji Kopi Arabika Gayo Green Bean',
                        'description' => 'Biji kopi hijau Arabika Gayo kualitas premium, ditanam di ketinggian 1.200 - 1.500 mdpl. Memiliki cita rasa khas buah (fruity) dan keasaman yang seimbang.'
                    ],
                    'en' => [
                        'name' => 'Arabica Gayo Coffee Green Beans',
                        'description' => 'Premium grade Arabica Gayo green coffee beans, grown at 1,200 - 1,500 masl. Featuring a distinctive fruity aroma, rich body, and balanced acidity.'
                    ]
                ],
                'specifications' => [
                    [
                        'spec_key' => 'Moisture',
                        'spec_value' => '12% Max',
                        'locale' => 'en',
                        'sort_order' => 1
                    ],
                    [
                        'spec_key' => 'Kadar Air',
                        'spec_value' => 'Maksimal 12%',
                        'locale' => 'id',
                        'sort_order' => 1
                    ],
                    [
                        'spec_key' => 'Defect Rate',
                        'spec_value' => 'Max 11% (Grade 1)',
                        'locale' => 'en',
                        'sort_order' => 2
                    ],
                    [
                        'spec_key' => 'Tingkat Cacat',
                        'spec_value' => 'Maksimal 11% (Mutu 1)',
                        'locale' => 'id',
                        'sort_order' => 2
                    ],
                    [
                        'spec_key' => 'Processing Method',
                        'spec_value' => 'Semi-washed (Wet Hulled)',
                        'locale' => 'en',
                        'sort_order' => 3
                    ],
                    [
                        'spec_key' => 'Metode Proses',
                        'spec_value' => 'Giling Basah (Semi-washed)',
                        'locale' => 'id',
                        'sort_order' => 3
                    ]
                ]
            ],
            [
                'slug' => 'robusta-lampung-coffee-beans',
                'hs_code' => '0901.11.20',
                'moq' => '1 x 20ft Container (approx. 18 Metric Tons)',
                'supply_capacity' => '100 Metric Tons / Month',
                'packaging' => 'Jute Bags (60 kg)',
                'origin' => 'Lampung, Sumatra, Indonesia',
                'indicative_price' => 3200.00,
                'currency' => 'USD',
                'incoterms' => 'FOB',
                'is_featured' => false,
                'status' => 'published',
                'sort_order' => 2,
                'translations' => [
                    'id' => [
                        'name' => 'Biji Kopi Robusta Lampung Green Bean',
                        'description' => 'Kopi Robusta Lampung terkenal dengan rasa cokelat hitam yang tebal dan aroma karamel yang kuat. Sangat cocok untuk bahan baku kopi instan dan espresso blend.'
                    ],
                    'en' => [
                        'name' => 'Robusta Lampung Coffee Green Beans',
                        'description' => 'Lampung Robusta coffee is renowned for its bold dark chocolate notes and strong caramel aroma. Perfect for instant coffee raw material and espresso blending.'
                    ]
                ],
                'specifications' => [
                    [
                        'spec_key' => 'Moisture',
                        'spec_value' => '13% Max',
                        'locale' => 'en',
                        'sort_order' => 1
                    ],
                    [
                        'spec_key' => 'Kadar Air',
                        'spec_value' => 'Maksimal 13%',
                        'locale' => 'id',
                        'sort_order' => 1
                    ],
                    [
                        'spec_key' => 'Processing Method',
                        'spec_value' => 'Dry Process (Natural)',
                        'locale' => 'en',
                        'sort_order' => 2
                    ],
                    [
                        'spec_key' => 'Metode Proses',
                        'spec_value' => 'Proses Kering (Natural)',
                        'locale' => 'id',
                        'sort_order' => 2
                    ]
                ]
            ]
        ];

        foreach ($productsData as $data) {
            // A. Buat Produk
            $product = Product::create([
                'category_id' => $category->id,
                'slug' => $data['slug'],
                'hs_code' => $data['hs_code'],
                'moq' => $data['moq'],
                'supply_capacity' => $data['supply_capacity'],
                'packaging' => $data['packaging'],
                'origin' => $data['origin'],
                'indicative_price' => $data['indicative_price'],
                'currency' => $data['currency'],
                'incoterms' => $data['incoterms'],
                'is_featured' => $data['is_featured'],
                'status' => $data['status'],
                'sort_order' => $data['sort_order'],
            ]);

            // B. Simpan Translasi Produk
            foreach ($data['translations'] as $locale => $trans) {
                $product->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                ]);
            }

            // C. Simpan Spesifikasi Produk
            foreach ($data['specifications'] as $spec) {
                $product->specifications()->create([
                    'spec_key' => $spec['spec_key'],
                    'spec_value' => $spec['spec_value'],
                    'locale' => $spec['locale'],
                    'sort_order' => $spec['sort_order'],
                ]);
            }

            // D. Hubungkan ke Sertifikasi (Many-to-Many)
            if ($iso) $product->certifications()->attach($iso->id);
            if ($haccp) $product->certifications()->attach($haccp->id);
        }
    }
}
