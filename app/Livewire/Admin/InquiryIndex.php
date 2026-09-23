<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\MengelolaInquiry;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Inquiry;
use App\Models\Product;

class InquiryIndex extends Component
{
    use WithPagination;
    use MengelolaInquiry;

    public string $search = '';
    public string $selectedStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $selectedProduct = '';

    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo'], true)) {
            $this->resetPage();
        }
    }

    #[Layout('components.layouts.app')]
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
            'inquiries' => $inquiries,
            'products'  => Product::all(),
        ]);
    }
}
