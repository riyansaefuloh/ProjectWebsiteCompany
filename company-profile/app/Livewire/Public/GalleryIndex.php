<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Gallery;

class GalleryIndex extends Component
{
    public function render()
    {
        // Assuming Gallery has items with media
        $galleries = Gallery::with('items.media')->get();

        return view('livewire.public.gallery-index', [
            'galleries' => $galleries,
        ]);
    }
}
