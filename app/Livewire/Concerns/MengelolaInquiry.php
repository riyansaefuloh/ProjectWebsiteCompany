<?php

namespace App\Livewire\Concerns;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Kelakuan jendela "Kelola inquiry", dipakai bersama dua komponen.
 *
 * InquiryIndex membukanya dari tabel halaman Inquiry; InquiryDetail
 * membukanya di atas dasbor. Keduanya menjalankan hal yang sama persis —
 * mengubah status, menugaskan ke sales, menulis catatan internal — dan
 * satu-satunya bedanya adalah apa yang terlihat kabur di belakangnya.
 *
 * Karena itu kelakuannya tinggal di satu tempat. Nama properti dan nama
 * aksinya juga jadi sama di kedua komponen, dan itulah yang membuat satu
 * berkas blade (partials/inquiry-modal) bisa melayani keduanya.
 */
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

        /*
         * Apa yang terjadi SESUDAH menyimpan berbeda di tiap tempat, dan
         * hanya itu yang berbeda.
         *
         * Halaman Inquiry menggambar ulang tabelnya sendiri sebagai bagian
         * dari daur Livewire, jadi ia tidak perlu berbuat apa-apa. Dasbor
         * bukan komponen Livewire — tabelnya tidak ikut tergambar ulang, dan
         * baris yang barusan diubah akan tetap memperlihatkan status lamanya
         * sampai halamannya dimuat lagi.
         */
        $this->sesudahSimpan();
    }

    /**
     * Dibiarkan kosong: yang tidak butuh apa-apa tidak perlu menuliskan
     * apa-apa.
     */
    protected function sesudahSimpan(): void
    {
        //
    }

    /**
     * Sebutan status dalam bahasa Indonesia.
     *
     * Tinggal di sini, bukan di dalam blade, karena halaman Inquiry
     * memakainya juga untuk menu penyaring dan keping "penyaring aktif".
     * Sebelumnya petanya ditulis ulang di tiap tempat yang membutuhkannya,
     * dan satu status baru berarti mencari semua salinannya.
     *
     * Pil statusnya sendiri TIDAK memakai peta ini — ia punya komponennya
     * sendiri, x-admin.status-pill, yang dipakai dasbor juga.
     */
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

    /**
     * Daftar orang yang bisa ditugaskan menangani inquiry.
     */
    public function penggunaSales(): Collection
    {
        return User::all();
    }
}
