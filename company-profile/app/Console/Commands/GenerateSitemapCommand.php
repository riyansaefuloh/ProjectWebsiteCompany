<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\News;

class GenerateSitemapCommand extends Command
{
    /**
     * Nama command artisan.
     * Penggunaan di terminal: php artisan sitemap:generate
     */
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.xml otomatis untuk produk, berita, dan halaman publik multibahasa';

    public function handle(): int
    {
        $this->info('Generating sitemap.xml...');

        $sitemap = Sitemap::create();

        // 1. Tambahkan Rute Statis Publik (EN & ID)
        $sitemap->add(Url::create('/en')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/id')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/en/products')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/id/products')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/en/about')->setPriority(0.8));
        $sitemap->add(Url::create('/id/about')->setPriority(0.8));
        $sitemap->add(Url::create('/en/inquiry')->setPriority(0.9));
        $sitemap->add(Url::create('/id/inquiry')->setPriority(0.9));

        // 2. Tambahkan URL Produk Aktif
        $products = Product::where('status', 'published')->get();
        foreach ($products as $product) {
            $sitemap->add(
                Url::create("/en/products/{$product->slug}")
                    ->setLastModificationDate($product->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
            $sitemap->add(
                Url::create("/id/products/{$product->slug}")
                    ->setLastModificationDate($product->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        }

        // 3. Tambahkan URL Berita Aktif
        $newsList = News::where('status', 'published')->get();
        foreach ($newsList as $news) {
            $sitemap->add(
                Url::create("/en/news/{$news->slug}")
                    ->setLastModificationDate($news->updated_at)
                    ->setPriority(0.7)
            );
            $sitemap->add(
                Url::create("/id/news/{$news->slug}")
                    ->setLastModificationDate($news->updated_at)
                    ->setPriority(0.7)
            );
        }

        // Simpan sitemap.xml di folder public/
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('sitemap.xml successfully generated at public/sitemap.xml!');

        return Command::SUCCESS;
    }
}
