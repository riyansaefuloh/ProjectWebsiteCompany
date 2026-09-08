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

    /*
     * Jendela "Kelola inquiry" beserta seluruh isinya — properti, aksi, dan
     * daftar pilihannya — tinggal di trait ini, dan dipakai bersama komponen
     * InquiryDetail yang memasang jendela yang sama di atas dasbor.
     */
    use MengelolaInquiry;

    public string $search = '';
    public string $selectedStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $selectedProduct = '';


    /**
     * Kembali ke halaman satu tiap kali penyaringnya diubah.
     *
     * Tanpa ini, menyaring saat sedang berada di halaman jauh meninggalkan
     * nomor halamannya apa adanya — dan halaman 20 dari hasil yang cuma 3
     * halaman menggambar tabel kosong beserta kalimat "tidak ada yang cocok",
     * padahal hasilnya ada, cuma tidak di halaman itu.
     */
    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus', 'selectedProduct', 'dateFrom', 'dateTo'], true)) {
            $this->resetPage();
        }
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
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

        /*
         * salesUsers tidak lagi dititipkan di sini.
         *
         * Ia hanya dipakai jendela "Kelola inquiry", dan jendela itu kini
         * mengambilnya sendiri lewat trait — artinya User::all() berhenti
         * dijalankan pada setiap kali halaman ini digambar, termasuk saat tidak
         * ada satu pun jendela yang terbuka. Menyaring, mengetik di kotak cari,
         * dan berpindah halaman semuanya memicu penggambaran ulang; ketiganya
         * dulu ikut menarik seluruh tabel pengguna tanpa ada yang memakainya.
         */
        return view('livewire.admin.inquiry-index', [
            'inquiries' => $inquiries,
            'products'  => Product::all(),
        ]);
    }
}
