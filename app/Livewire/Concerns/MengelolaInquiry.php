<?php

namespace App\Livewire\Concerns;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Collection;

trait MengelolaInquiry
{
    public bool $showModal = false;
    public ?string $editingId = null;

    public ?Inquiry $selectedInquiry = null;
    public string $status = 'new';
    public ?string $assigned_to = null;
    public ?string $internal_note = null;

    public function viewDetails(string $id): void
    {
        $this->selectedInquiry = Inquiry::with(['product.translations', 'assignedSales'])->findOrFail($id);
        $this->editingId       = $this->selectedInquiry->id;
        $this->status          = $this->selectedInquiry->status;
        $this->assigned_to     = $this->selectedInquiry->assigned_to;
        $this->internal_note   = $this->selectedInquiry->internal_note;
        $this->showModal       = true;
    }

    public function tutupModal(): void
    {
        $this->showModal = false;
    }

    public function updateStatus(): void
    {
        if (! $this->editingId) {
            return;
        }

        $inquiry = Inquiry::findOrFail($this->editingId);
        $inquiry->status        = $this->status;
        $inquiry->assigned_to   = $this->assigned_to ?: null;
        $inquiry->internal_note = $this->internal_note;
        $inquiry->save();

        $this->showModal = false;
        session()->flash('message', 'Inquiry status updated successfully!');

        $this->sesudahSimpan();
    }

    protected function sesudahSimpan(): void
    {
        //
    }

    public function sebutanStatus(): array
    {
        return [
            'new'        => 'Baru',
            'processing' => 'Diproses',
            'quoted'     => 'Ditawar',
            'closed'     => 'Selesai',
            'rejected'   => 'Ditolak',
        ];
    }

    public function penggunaSales(): Collection
    {
        return User::all();
    }
}
