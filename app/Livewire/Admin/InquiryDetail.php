<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\MengelolaInquiry;
use Livewire\Attributes\On;
use Livewire\Component;

class InquiryDetail extends Component
{
    use MengelolaInquiry;

    // Nama rute tujuan setelah menyimpan
    public string $kembaliKe = 'admin.dashboard';

    #[On('buka-inquiry')]
    public function buka(string $id): void
    {
        $this->viewDetails($id);
    }

    protected function sesudahSimpan(): void
    {
        $this->redirectRoute($this->kembaliKe, navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.inquiry-detail');
    }
}
