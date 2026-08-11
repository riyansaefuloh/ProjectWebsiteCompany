<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ExportMarket;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ExportMarketIndex extends Component
{
    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Our Export Markets - ' . $appName);
        SEOMeta::setDescription('We export premium Indonesian coffee to buyers across Asia, Europe, America, and the Middle East. Discover our global export market coverage.');
        SEOMeta::setCanonical(route('export-markets.index'));

        OpenGraph::setTitle('Our Export Markets - ' . $appName);
        OpenGraph::setDescription('Global coffee export destinations from Indonesia.');
        OpenGraph::setUrl(route('export-markets.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Our Export Markets - ' . $appName);
        TwitterCard::setDescription('We export premium Indonesian coffee globally.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $exportMarkets = ExportMarket::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.public.export-market-index', [
            'exportMarkets' => $exportMarkets,
        ]);
    }
}
