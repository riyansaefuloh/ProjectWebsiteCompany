<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ProductShow extends Component
{
    public $product;

    public function mount($slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'translations',
                'media',
                'category.translations',
                'certifications.translations',
                'specifications',
            ])
            ->firstOrFail();

        $appName    = config('app.name');
        $name       = $this->product->getTranslation('name', app()->getLocale()) ?? $this->product->getTranslation('name', 'en');
        $desc       = strip_tags($this->product->getTranslation('description', app()->getLocale()) ?? $this->product->getTranslation('description', 'en') ?? '');
        $shortDesc  = mb_substr($desc, 0, 160);
        $imageUrl   = $this->product->getFirstMediaUrl('gallery') ?: null;

        // Meta Title & Description
        SEOMeta::setTitle($name . ' - ' . $appName);
        SEOMeta::setDescription($shortDesc ?: 'Premium export product from Indonesia. MOQ and pricing available upon inquiry.');
        SEOMeta::setCanonical(route('products.show', $this->product->slug));

        // Open Graph
        OpenGraph::setTitle($name . ' - ' . $appName);
        OpenGraph::setDescription($shortDesc ?: 'Premium export product from Indonesia.');
        OpenGraph::setUrl(route('products.show', $this->product->slug));
        OpenGraph::setType('og:product');
        if ($imageUrl) {
            OpenGraph::addImage($imageUrl);
        }

        // Twitter Card
        TwitterCard::setTitle($name . ' - ' . $appName);
        TwitterCard::setDescription($shortDesc ?: 'Premium export product from Indonesia.');
        if ($imageUrl) {
            TwitterCard::setImage($imageUrl);
        }
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $locale = app()->getLocale();

        $specs = $this->product->specifications
            ->filter(fn ($spec) => blank($spec->locale) || $spec->locale === $locale)
            ->sortBy('sort_order')
            ->values();

        $facts = collect([
            ['label' => __('site.origin'),           'value' => $this->product->origin],
            ['label' => __('site.hs_code'),          'value' => $this->product->hs_code],
            ['label' => __('site.moq'),              'value' => $this->product->moq],
            ['label' => __('site.indicative_price'), 'value' => $this->product->indicative_price
                ? number_format((float) $this->product->indicative_price, 2) . ' USD'
                : null],
        ])
        ->concat($specs->map(fn ($spec) => [
            'label' => $spec->spec_key,
            'value' => $spec->spec_value,
        ]))
        ->filter(fn ($row) => filled($row['value']))
        ->values();

        $whatsapp = Setting::where('key', 'whatsapp_number')->value('value');

        return view('livewire.public.product-show', [
            'facts'  => $facts,
            'waLink' => $whatsapp
                ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp)
                : null,
        ]);
    }
}
