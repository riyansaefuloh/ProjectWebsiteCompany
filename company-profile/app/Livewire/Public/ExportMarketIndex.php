<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\ExportMarket;

class ExportMarketIndex extends Component
{
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
