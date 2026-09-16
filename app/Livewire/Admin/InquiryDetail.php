<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\MengelolaInquiry;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Jendela "Kelola inquiry" yang bisa dipasang di halaman apa pun.
 *
 * Dipakai dasbor. Halaman itu bukan komponen Livewire dan tidak bisa
 * memanggil viewDetails() sendiri, jadi tombol "Aksi" di kartu Inquiry
 * terbaru cukup melempar satu peristiwa; komponen inilah yang menangkapnya.
 *
 * Sebelumnya tombol itu BERPINDAH ke halaman Inquiry sambil membawa id-nya.
 * Jendelanya memang terbuka, tapi yang kabur di belakangnya adalah tabel
 * Inquiry — halaman yang tidak pernah diminta — dan menu di bilah sisi ikut
 * berpindah. Sebuah jendela seharusnya berdiri di atas tempat orangnya
 * sedang berada, bukan memindahkannya lebih dulu.
 *
 * Komponen ini tidak menggambar apa pun selama jendelanya tertutup.
 */
class InquiryDetail extends Component
{
    use MengelolaInquiry;

    /**
     * Nama rute yang dituju sesudah menyimpan.
     *
     * Halaman pemasangnya tidak menggambar ulang dirinya sendiri, jadi baris
     * yang barusan diubah akan tetap memperlihatkan status lamanya. Memuat
     * ulang halamannya adalah cara paling jujur untuk menunjukkan bahwa
     * simpanannya jadi — sekaligus satu-satunya tanda yang tersedia, karena
     * kalimat "berhasil disimpan" hanya digambar oleh halaman Inquiry.
     *
     * Nama rute, BUKAN alamat: yang dirakit route() tidak mungkin menunjuk
     * keluar dari panel ini.
     */
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
