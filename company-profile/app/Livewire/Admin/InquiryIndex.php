<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Product;

class InquiryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $selectedProduct = '';
    
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
                $q->where(function ($subQ) {
                    $subQ->where('name', 'LIKE', "%{$this->search}%")
                         ->orWhere('company', 'LIKE', "%{$this->search}%")
                         ->orWhere('email', 'LIKE', "%{$this->search}%")
                         ->orWhere('country_code', 'LIKE', "%{$this->search}%");
                });
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->when($this->selectedProduct, function ($q) {
                if ($this->selectedProduct === 'general') {
                    $q->whereNull('product_id');
                } else {
                    $q->where('product_id', $this->selectedProduct);
                }
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.inquiry-index', [
            'inquiries'  => $inquiries,
            'salesUsers' => User::all(),
            'products'   => Product::all(),
        ]);
    }
}
