<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about-us',
                'status' => 'published',
                'id' => [
                    'title' => 'Tentang Kami',
                    'content' => '<h1>Sejarah Eksportir Kopi Terbaik</h1><p>Berdiri sejak tahun 2010, perusahaan kami berdedikasi untuk membawa biji kopi asli Indonesia ke seluruh penjuru dunia. Kami bekerja sama dengan lebih dari 5.000 petani lokal untuk memastikan kualitas kopi terbaik standar internasional.</p>',
                ],
                'en' => [
                    'title' => 'About Us',
                    'content' => '<h1>History of The Best Coffee Exporter</h1><p>Established in 2010, our company is dedicated to bringing authentic Indonesian coffee beans to the world. We work directly with over 5,000 local farmers to ensure top-notch international standard coffee quality.</p>',
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'status' => 'published',
                'id' => [
                    'title' => 'Kebijakan Privasi',
                    'content' => '<h2>1. Pengumpulan Data Buyer</h2><p>Kami menjamin kerahasiaan seluruh data Inquiry (RFQ) yang Anda masukkan melalui form website ini. Data spesifikasi produk dan informasi kontak perusahaan Anda hanya akan digunakan untuk keperluan transaksi ekspor secara profesional.</p>',
                ],
                'en' => [
                    'title' => 'Privacy Policy',
                    'content' => '<h2>1. Buyer Data Collection</h2><p>We guarantee the confidentiality of all Inquiry (RFQ) data submitted through this website. Your product specifications and company contact information will only be used for professional export transaction purposes.</p>',
                ],
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::create([
                'slug' => $data['slug'],
                'status' => $data['status'],
            ]);

            $page->translations()->createMany([
                ['locale' => 'id', 'title' => $data['id']['title'], 'content' => $data['id']['content']],
                ['locale' => 'en', 'title' => $data['en']['title'], 'content' => $data['en']['content']],
            ]);
        }
    }
}
