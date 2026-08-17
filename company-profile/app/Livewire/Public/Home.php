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
        $featuredProducts = Product::where('status', 'published')
            ->where('is_featured', true)
            ->with(['translations', 'media', 'category.translations'])
            ->orderBy('sort_order')
            ->limit(6)
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

        $heroPage = \App\Models\Page::where('slug', 'hero')->first();

        $establishedYear = (int) ($settings['established_year'] ?? date('Y'));
        $yearsOfExperience = max(1, (int) date('Y') - $establishedYear);

        return view('livewire.public.home', [
            'featuredProducts'  => $featuredProducts,
            'latestNews'        => $latestNews,
            'exportMarkets'     => $exportMarkets,
            'certifications'    => $certifications,
            'homeSections'      => $activeSections,
            'heroPage'          => $heroPage,
            'settings'          => $settings,
            'yearsOfExperience' => $yearsOfExperience,
        ]);
    }
}
