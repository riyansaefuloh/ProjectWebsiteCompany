<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;

class About extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        $page = Page::where('slug', 'about-us')->first();
        return view('livewire.public.about', ['page' => $page]);
    }
}
