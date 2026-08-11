<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Certification;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class CertificationIndex extends Component
{
    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Our Certifications - ' . $appName);
        SEOMeta::setDescription('We hold internationally recognized certifications including Organic, Fair Trade, Rainforest Alliance, and food safety standards for our coffee export products.');
        SEOMeta::setCanonical(route('certifications.index'));

        OpenGraph::setTitle('Our Certifications - ' . $appName);
        OpenGraph::setDescription('International certifications held by ' . $appName . ' for coffee export quality assurance.');
        OpenGraph::setUrl(route('certifications.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Our Certifications - ' . $appName);
        TwitterCard::setDescription('Internationally certified coffee exporter from Indonesia.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $certifications = Certification::where('status', 'active')->orderBy('sort_order')->get();
        return view('livewire.public.certification-index', compact('certifications'));
    }
}
