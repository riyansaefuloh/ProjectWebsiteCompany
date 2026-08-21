<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;

class DownloadIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    /*
     * Penyaring gerbang unduhan. Untai, bukan boolean: '' berarti "semua",
     * '1' berarti yang perlu email, '0' berarti yang terbuka.
     *
     * Nilai '0' inilah yang menuntut kehati-hatian di kuerinya — lihat
     * catatan di render().
     */
    public string $selectedGate = '';

    public bool $showModal = false;
    public ?string $editingId = null;

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
        if (in_array($property, ['search', 'selectedGate'], true)) {
            $this->resetPage();
        }
    }

    // Form Fields (PRD Bab 9.1)
    public string $title = '';
    public bool $require_email = true;
    public int $sort_order = 0;
    public $pdfFile;

    /*
     * Alamat berkas yang sudah tersimpan, supaya modalnya bisa menunjukkan
     * apa yang sedang dipakai. Sebelumnya edit() tidak memuatnya sama sekali,
     * jadi modal ubah selalu tampak seperti belum punya berkas — dan pemakai
     * mengunggah ulang berkas yang sebenarnya masih ada.
     */
    public ?string $existingFilePath = null;

    protected function rules(): array
    {
        return [
            'title'         => 'required|string|max:150',
            'require_email' => 'boolean',
            'sort_order'    => 'integer|min:0',

            /*
             * Wajib saat menambah baru, boleh kosong saat menyunting.
             *
             * downloads.file_path itu NOT NULL tanpa nilai bawaan. Dengan
             * 'nullable' apa adanya, menambah berkas tanpa mengunggah PDF
             * lolos validasi lalu jatuh sebagai galat basis data di layar
             * pemakai — bukan sebagai pesan merah di sebelah isiannya.
             */
            'pdfFile' => ($this->editingId ? 'nullable' : 'required')
                       . '|mimes:pdf|max:10240', // Maksimal 10 MB
        ];
    }

    /*
     * Kantong galatnya ikut dikosongkan tiap kali modalnya dibuka.
     *
     * Kantong itu bertahan lintas permintaan: sekali percobaan simpan gagal,
     * pesan merahnya masih menempel saat modalnya dibuka lagi untuk berkas
     * yang lain, padahal isiannya sudah benar.
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $this->resetValidation();

        $download = Download::findOrFail($id);
        $this->editingId = $download->id;
        $this->title = $download->title;
        $this->require_email = $download->require_email;
        $this->sort_order = $download->sort_order;
        $this->existingFilePath = $download->file_path;
        $this->pdfFile = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $download = $this->editingId 
            ? Download::findOrFail($this->editingId)
            : new Download();

        $download->title = $this->title;
        $download->require_email = $this->require_email;
        $download->sort_order = $this->sort_order;

        if ($this->pdfFile) {
            // Hapus file lama jika ada
            if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
                Storage::disk('public')->delete($download->file_path);
            }

            // Simpan file PDF baru di disk public/brochures
            $path = $this->pdfFile->store('brochures', 'public');
            $download->file_path = $path;
        }

        $download->save();

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Download brochure saved successfully!');
    }

    public function delete(string $id): void
    {
        $download = Download::findOrFail($id);
        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            Storage::disk('public')->delete($download->file_path);
        }
        $download->delete();

        session()->flash('message', 'Brochure deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->require_email = true;
        $this->sort_order = 0;
        $this->pdfFile = null;
        $this->existingFilePath = null;
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $downloads = Download::query()
            ->when($this->search, function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%");
            })
            /*
             * !== '', BUKAN when($this->selectedGate, ...).
             *
             * when() memakai kebenaran nilainya, dan untai '0' itu palsu di
             * PHP — jadi pilihan "Terbuka" tidak akan pernah menyaring apa pun,
             * dan diam-diam menampilkan seluruh berkas seolah tidak ada
             * penyaring yang menyala.
             */
            ->when($this->selectedGate !== '', function ($q) {
                $q->where('require_email', $this->selectedGate === '1');
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(10);

        return view('livewire.admin.download-index', [
            'downloads' => $downloads,
        ]);
    }
}
