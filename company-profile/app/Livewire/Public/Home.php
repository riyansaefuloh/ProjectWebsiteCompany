<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Product;
use App\Models\News;
use App\Models\ExportMarket;

class Home extends Component
{
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

        return view('livewire.public.home', [
            'featuredProducts' => $featuredProducts,
            'latestNews' => $latestNews,
            'exportMarkets' => $exportMarkets,
        ]);
    }
}
