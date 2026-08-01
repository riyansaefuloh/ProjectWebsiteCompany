<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inquiry;
use App\Models\User;

class InquiryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Detail & Manage Fields
    public ?Inquiry $selectedInquiry = null;
    public string $status = 'new';
    public ?string $assigned_to = null;
    public ?string $internal_note = null;

    public function viewDetails(string $id): void
    {
        $this->selectedInquiry = Inquiry::with(['product.translations', 'assignedSales'])->findOrFail($id);
        $this->editingId = $this->selectedInquiry->id;
        $this->status = $this->selectedInquiry->status;
        $this->assigned_to = $this->selectedInquiry->assigned_to;
        $this->internal_note = $this->selectedInquiry->internal_note;
        $this->showModal = true;
    }

    public function updateStatus(): void
    {
        if (!$this->editingId) return;

        $inquiry = Inquiry::findOrFail($this->editingId);
        $inquiry->status = $this->status;
        $inquiry->assigned_to = $this->assigned_to ?: null;
        $inquiry->internal_note = $this->internal_note;
        $inquiry->save();

        $this->showModal = false;
        session()->flash('message', 'Inquiry status updated successfully!');
    }

    public function render()
    {
        $inquiries = Inquiry::with(['product.translations', 'assignedSales'])
            ->when($this->search, function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%")
                  ->orWhere('company', 'LIKE', "%{$this->search}%")
                  ->orWhere('email', 'LIKE', "%{$this->search}%")
                  ->orWhere('country_code', 'LIKE', "%{$this->search}%");
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.inquiry-index', [
            'inquiries'  => $inquiries,
            'salesUsers' => User::all(),
        ]);
    }
}
