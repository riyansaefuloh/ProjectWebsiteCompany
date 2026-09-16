<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            [
                'slug' => 'iso-9001',
                'issuer' => 'SGS Certification',
                'certificate_number' => 'ISO-9001-2026-XYZ',
                'issued_at' => Carbon::parse('2024-01-01'),
                'expires_at' => Carbon::parse('2029-01-01'),
                'status' => 'active',
                'sort_order' => 1,
                'translations' => [
                    'id' => [
                        'name' => 'ISO 9001:2015 (Manajemen Mutu)',
                        'description' => 'Sertifikasi standar internasional untuk sistem manajemen mutu perusahaan.'
                    ],
                    'en' => [
                        'name' => 'ISO 9001:2015 (Quality Management)',
                        'description' => 'International standard certification for corporate quality management systems.'
                    ]
                ]
            ],
            [
                'slug' => 'haccp',
                'issuer' => 'TUV SUD',
                'certificate_number' => 'HACCP-2026-ABC',
                'issued_at' => Carbon::parse('2024-05-10'),
                'expires_at' => Carbon::parse('2027-05-10'),
                'status' => 'active',
                'sort_order' => 2,
                'translations' => [
                    'id' => [
                        'name' => 'HACCP (Keamanan Pangan)',
                        'description' => 'Sertifikasi keamanan pangan untuk menjamin produk bebas dari bahaya biologis, kimia, dan fisik.'
                    ],
                    'en' => [
                        'name' => 'HACCP (Food Safety)',
                        'description' => 'Food safety certification ensuring products are free from biological, chemical, and physical hazards.'
                    ]
                ]
            ]
        ];

        foreach ($certifications as $data) {
            $cert = Certification::create([
                'slug' => $data['slug'],
                'issuer' => $data['issuer'],
                'certificate_number' => $data['certificate_number'],
                'issued_at' => $data['issued_at'],
                'expires_at' => $data['expires_at'],
                'status' => $data['status'],
                'sort_order' => $data['sort_order'],
            ]);

            foreach ($data['translations'] as $locale => $trans) {
                $cert->translations()->create([
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                ]);
            }
        }
    }
}
