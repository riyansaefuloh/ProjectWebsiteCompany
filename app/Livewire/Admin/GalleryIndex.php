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
    public $gallery_id;
    public $name;
    public $photos = [];
    public $videoUrl = '';
    public $editingGallery = null;

    public function updating($property, $value): void
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
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

    public function create()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name'      => 'required|string|max:255',
            'photos.*'  => 'image|max:5120',
            'videoUrl'  => 'nullable|url',
        ]);

        $gallery = Gallery::updateOrCreate(['id' => $this->gallery_id], [
            'name' => $this->name,
        ]);

        if (!empty($this->videoUrl)) {
            $gallery->items()->create([
                'type'      => 'video',
                'video_url' => $this->videoUrl
            ]);
            $this->videoUrl = '';
        }

        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                // Buat GalleryItem lalu lampirkan media
                $item = $gallery->items()->create(['type' => 'image']);
                $item->addMedia($photo->getRealPath())
                     ->usingName($photo->getClientOriginalName())
                     ->toMediaCollection('gallery');
            }
        }

        session()->flash('message', 'Gallery saved successfully!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();

        $gallery = Gallery::with('items.media')->findOrFail($id);
        $this->gallery_id      = $id;
        $this->name            = $gallery->name;
        $this->editingGallery  = $gallery;
        $this->videoUrl        = '';
        $this->photos          = [];

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
        $this->gallery_id     = null;
        $this->name           = '';
        $this->photos         = [];
        $this->videoUrl       = '';
        $this->editingGallery = null;
    }
}
