<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Gallery;

class GalleryIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isOpen = false;

    /**
     * Kembali ke halaman satu tiap kali pencariannya diubah.
     *
     * Tanpa ini, mencari saat sedang berada di halaman jauh meninggalkan nomor
     * halamannya apa adanya — dan halaman 20 dari hasil yang cuma 3 halaman
     * menggambar tabel kosong beserta kalimat "tidak ada yang cocok", padahal
     * hasilnya ada, cuma tidak di halaman itu.
     */
    public function updating($property, $value): void
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }
    
    public $gallery_id;
    public $name;
    public $photos = [];
    public $videoUrl = '';
    public $editingGallery = null;

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        /*
         * when(), bukan LIKE '%%' tanpa syarat. Keduanya menghasilkan baris
         * yang sama — galleries.name itu NOT NULL, jadi tidak ada baris yang
         * diam-diam tersaring — tapi yang lama menempelkan kondisi ke setiap
         * kueri tanpa alasan, dan bentuknya beda sendiri dari halaman admin
         * lain yang semuanya memakai when().
         *
         * latest('updated_at') supaya album yang baru ditambahi media naik ke
         * atas — kolom "Diperbarui" di tabelnya jadi berarti.
         */
        $galleries = Gallery::with('items.media')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.gallery-index', [
            'galleries' => $galleries
        ]);
    }

    /*
     * Kantong galatnya ikut dikosongkan tiap kali modalnya dibuka.
     *
     * Kantong itu bertahan lintas permintaan: sekali percobaan simpan gagal,
     * pesan merahnya masih menempel saat modalnya dibuka lagi untuk album yang
     * lain, padahal isiannya sudah benar. Yang terbaca pemakai: galat yang
     * tidak bisa dihilangkan.
     */
    public function create()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'photos.*' => 'image|max:5120', // 5MB Max per image
            'videoUrl' => 'nullable|url',
        ]);

        $gallery = Gallery::updateOrCreate(['id' => $this->gallery_id], [
            'name' => $this->name,
        ]);

        if (!empty($this->videoUrl)) {
            $gallery->items()->create([
                'type' => 'video',
                'video_url' => $this->videoUrl
            ]);
            $this->videoUrl = ''; // reset after adding
        }

        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                // Create GalleryItem and attach media
                $item = $gallery->items()->create([
                    'type' => 'image'
                ]);
                $item->addMedia($photo->getRealPath())
                     ->usingName($photo->getClientOriginalName())
                     ->toMediaCollection('gallery');
            }
        }

        // If editing, refresh the editingGallery so the view updates immediately
        if ($this->gallery_id) {
            $this->editingGallery = Gallery::with('items.media')->find($this->gallery_id);
            // Reset photos after upload so it's ready for next
            $this->photos = [];
            session()->flash('message', 'Media added successfully.');
            return; // Don't close modal if editing
        }

        session()->flash('message', 
            $this->gallery_id ? 'Gallery Updated Successfully.' : 'Gallery Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();

        $gallery = Gallery::with('items.media')->findOrFail($id);
        $this->gallery_id = $id;
        $this->name = $gallery->name;
        $this->editingGallery = $gallery;
        $this->videoUrl = '';
        $this->photos = [];
    
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Gallery::find($id)->delete();
        session()->flash('message', 'Gallery Deleted Successfully.');
    }

    public function deleteItem($itemId)
    {
        $item = \App\Models\GalleryItem::find($itemId);
        if ($item) {
            $item->clearMediaCollection('gallery');
            $item->delete();
            session()->flash('message', 'Media Item Deleted Successfully.');
            
            if ($this->editingGallery) {
                $this->editingGallery->load('items.media');
            }
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->gallery_id = null;
        $this->name = '';
        $this->photos = [];
        $this->videoUrl = '';
        $this->editingGallery = null;
    }
}
