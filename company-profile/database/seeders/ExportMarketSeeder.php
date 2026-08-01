<?php

namespace Database\Seeders;

use App\Models\ExportMarket;
use Illuminate\Database\Seeder;

class ExportMarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            [
                'country_code' => 'US',
                'region' => 'North America',
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'id' => [
                        'name' => 'Amerika Serikat',
                        'note' => 'Pasar utama ekspor briket arang kelapa untuk kebutuhan panggangan/BBQ.'
                    ],
                    'en' => [
                        'name' => 'United States',
                        'note' => 'Main export market for coconut charcoal briquettes for BBQ purposes.'
                    ]
                ]
            ],
            [
                'country_code' => 'DE',
                'region' => 'Europe',
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'id' => [
                        'name' => 'Jerman',
                        'note' => 'Konsumen terbesar produk biji kopi Arabika Gayo organik.'
                    ],
                    'en' => [
                        'name' => 'Germany',
                        'note' => 'Largest consumer of organic Gayo Arabica coffee bean products.'
                    ]
                ]
            ]
        ];

        foreach ($markets as $data) {
            $market = ExportMarket::create([
                'country_code' => $data['country_code'],
                'region' => $data['region'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);

            foreach ($data['translations'] as $locale => $trans) {
                $market->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'note' => $trans['note'],
                ]);
            }
        }
    }
}
