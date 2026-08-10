<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;

class PageShow extends Component
{
    public $page;

    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)->firstOrFail();
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.public.page-show');
    }
}
