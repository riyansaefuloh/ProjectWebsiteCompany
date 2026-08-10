<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Gallery;

class GalleryIndex extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        // Assuming Gallery has items with media
        $galleries = Gallery::with('items.media')->get();

        return view('livewire.public.gallery-index', [
            'galleries' => $galleries,
        ]);
    }
}
