<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Certification;

class CertificationIndex extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        $certifications = Certification::where('status', 'active')->orderBy('sort_order')->get();
        return view('livewire.public.certification-index', compact('certifications'));
    }
}
