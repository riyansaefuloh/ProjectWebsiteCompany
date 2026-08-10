<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\News;
use App\Models\ExportMarket;
use App\Models\Setting;

class Home extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        $featuredProducts = Product::where('status', 'published')
            ->where('is_featured', true)
            ->with(['translations', 'media'])
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

        $sectionsSetting = Setting::where('key', 'home_sections')->value('value');
        $homeSections = $sectionsSetting ? json_decode($sectionsSetting, true) : [];
        
        // Filter only active sections and sort by order
        $activeSections = array_filter($homeSections, function($sec) {
            return $sec['active'] === true;
        });
        usort($activeSections, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        $heroPage = \App\Models\Page::where('slug', 'hero')->first();

        return view('livewire.public.home', [
            'featuredProducts' => $featuredProducts,
            'latestNews' => $latestNews,
            'exportMarkets' => $exportMarkets,
            'homeSections' => $activeSections,
            'heroPage' => $heroPage,
        ]);
    }
}
