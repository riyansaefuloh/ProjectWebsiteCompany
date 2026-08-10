<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ExportMarket;

class ExportMarketIndex extends Component
{
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
