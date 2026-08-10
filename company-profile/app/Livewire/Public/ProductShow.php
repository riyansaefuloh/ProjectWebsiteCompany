<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

class ProductShow extends Component
{
    public $product;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'translations', 
                'media', 
                'category.translations', 
                'certifications.translations', 
                'specifications'
            ])
            ->firstOrFail();
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.public.product-show');
    }
}
