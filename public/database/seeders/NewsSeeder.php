<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@company.com')->first();
        if (!$author) return;

        $newsData = [
            [
                'slug' => 'ekspor-perdana-kopi-gayo-jerman',
                'published_at' => Carbon::now(),
                'status' => 'published',
                'translations' => [
                    'id' => [
                        'title' => 'PT. Indo Export Global Sukses Ekspor Perdana Biji Kopi Gayo ke Jerman',
                        'excerpt' => 'Sebanyak 18 ton biji kopi Arabika Gayo dikirimkan ke Hamburg, Jerman guna memenuhi pasar Eropa.',
                        'content' => '<p><strong>Jakarta</strong> - Kami dengan bangga mengumumkan keberhasilan pengiriman perdana satu kontainer penuh biji kopi Arabika Gayo kualitas mutu 1 ke Jerman. Produk ini telah memenuhi persyaratan ketat keamanan pangan Eropa.</p>',
                    ],
                    'en' => [
                        'title' => 'PT. Indo Export Global Successfully Exports First Shipment of Gayo Coffee to Germany',
                        'excerpt' => 'A total of 18 tons of premium Gayo Arabica coffee beans were shipped to Hamburg, Germany to supply the European market.',
                        'content' => '<p><strong>Jakarta</strong> - We are proud to announce the successful initial shipment of a full container of Gayo Arabica coffee green beans to Hamburg, Germany. This shipment conforms to European regulations.</p>',
                    ]
                ]
            ]
        ];

        foreach ($newsData as $data) {
            $news = News::create([
                'slug' => $data['slug'],
                'author_id' => $author->id,
                'published_at' => $data['published_at'],
                'status' => $data['status'],
            ]);

            foreach ($data['translations'] as $locale => $trans) {
                $news->translations()->create([
                    'locale' => $locale,
                    'title' => $trans['title'],
                    'excerpt' => $trans['excerpt'],
                    'content' => $trans['content'],
                ]);
            }
        }
    }
}
