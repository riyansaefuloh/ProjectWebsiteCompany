<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\News;
use App\Models\ExportMarket;
use App\Models\Certification;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class Home extends Component
{
    private const DEFAULT_SECTIONS = [
        ['id' => 'hero',           'active' => true, 'order' => 1],
        ['id' => 'certifications', 'active' => true, 'order' => 2],
        ['id' => 'products',       'active' => true, 'order' => 3],
        ['id' => 'export_markets', 'active' => true, 'order' => 4],
        ['id' => 'about',          'active' => true, 'order' => 5],
        ['id' => 'news',           'active' => true, 'order' => 6],
        ['id' => 'contact',        'active' => true, 'order' => 7],
    ];

    public function mount(): void
    {
        $companyName = Setting::where('key', 'company_name')->value('value') ?? config('app.name');

        SEOMeta::setTitle($companyName);
        SEOMeta::setDescription('We are a trusted coffee exporter from Indonesia. Explore our wide range of Arabica and Robusta coffee products, certifications, and export markets.');
        SEOMeta::setCanonical(url('/'));

        // Open Graph (untuk WhatsApp, LinkedIn, Facebook share)
        OpenGraph::setTitle($companyName);
        OpenGraph::setDescription('Premium coffee export company from Indonesia. MOQ, FOB/CIF pricing available for wholesale buyers.');
        OpenGraph::setUrl(url('/'));
        OpenGraph::setType('website');

        // Twitter Card
        TwitterCard::setTitle($companyName);
        TwitterCard::setDescription('Premium coffee exporter from Indonesia.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        /*
         * Susunan bagiannya dibaca lebih dulu: berapa produk unggulan yang
         * diambil ditentukan oleh pengaturan bagian produk, jadi kuerinya tidak
         * bisa dirakit sebelum pengaturannya diketahui.
         */
        $settings = Setting::pluck('value', 'key')->toArray();

        $homeSections = json_decode($settings['home_sections'] ?? '[]', true) ?: [];

        $activeSections = array_values(array_filter(
            $homeSections,
            fn ($sec) => ($sec['active'] ?? false) === true && isset($sec['id'])
        ));

        if (empty($activeSections)) {
            $activeSections = self::DEFAULT_SECTIONS;
        }

        usort($activeSections, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        $activeSections = array_map(function ($sec) {
            $sec['id'] = str_replace('-', '_', $sec['id']);
            return $sec;
        }, $activeSections);

        /*
         * Nama bagian ditulis dua gaya di proyek ini: 'export-markets' di dalam
         * JSON yang tersimpan, dan 'export_markets' di @case bladenya — karena
         * baris di atas mengganti tanda hubungnya supaya jadi nama @case yang
         * sah. Tiga penutup di bawah menyamakannya sendiri, jadi pemanggilnya
         * boleh menulis yang mana saja tanpa diam-diam kehilangan isinya.
         */
        $samakan = fn (string $bagian) => str_replace('-', '_', $bagian);

        /*
         * Pengaturan bukan-teks tiap bagian. Kunci yang tidak tercatat berarti
         * "ikut bawaan", jadi bawaannya ditulis di pemanggilnya.
         */
        $opsiBagian = collect($activeSections)
            ->mapWithKeys(fn ($sec) => [$sec['id'] => $sec['opsi'] ?? []])
            ->all();

        $opsi = fn (string $bagian, string $nama, $bawaan)
            => $opsiBagian[$samakan($bagian)][$nama] ?? $bawaan;

        $featuredProducts = Product::where('status', 'published')
            ->where('is_featured', true)
            ->with(['translations', 'media', 'category.translations'])
            ->orderBy('sort_order')
            ->limit(max(1, (int) $opsi('products', 'jumlah', 6)))
            ->get();

        $latestNews = News::where('status', 'published')
            ->with(['translations', 'media'])
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $exportMarkets = ExportMarket::where('is_active', true)
            ->with('translations')
            ->get();

        $certifications = Certification::where('status', 'active')
            ->with(['translations', 'media'])
            ->orderBy('sort_order')
            ->get();

        /*
         * Isi tiap bagian dibaca dari larik yang sama dengan urutannya.
         *
         * Dibungkus jadi satu penutup supaya bladenya cukup menulis
         * $isi('hero', 'title') dan tidak perlu tahu apa-apa soal bentuk
         * JSON-nya. Bahasa yang aktif dicoba lebih dulu, lalu bahasa cadangan,
         * lalu barulah teks bawaan di berkas bahasa.
         *
         * Bawaannya WAJIB ada: beranda tidak boleh pernah tergambar hampa
         * hanya karena satu kolom di panel belum diisi.
         */
        $bahasa   = app()->getLocale();
        $cadangan = config('app.fallback_locale', 'en');

        $isiBagian = collect($activeSections)
            ->mapWithKeys(fn ($sec) => [$sec['id'] => $sec['isi'] ?? []])
            ->all();

        $isi = function (string $bagian, string $nama, string $bawaan) use ($isiBagian, $bahasa, $cadangan, $samakan) {
            $sumber = $isiBagian[$samakan($bagian)] ?? [];

            foreach ([$bahasa, $cadangan] as $lokal) {
                $nilai = $sumber[$lokal][$nama] ?? null;

                if (filled($nilai)) {
                    return $nilai;
                }
            }

            return __($bawaan);
        };

        $gambarBagian = collect($activeSections)
            ->mapWithKeys(fn ($sec) => [$samakan($sec['id']) => $sec['image'] ?? null])
            ->all();

        $establishedYear = \App\Support\IsiHalaman::tahunBerdiri();
        $yearsOfExperience = max(1, (int) date('Y') - $establishedYear);

        return view('livewire.public.home', [
            'featuredProducts'  => $featuredProducts,
            'latestNews'        => $latestNews,
            'exportMarkets'     => $exportMarkets,
            'certifications'    => $certifications,
            'homeSections'      => $activeSections,
            'isi'               => $isi,
            'gambarBagian'      => $gambarBagian,
            'settings'          => $settings,
            'yearsOfExperience' => $yearsOfExperience,
        ]);
    }
}
