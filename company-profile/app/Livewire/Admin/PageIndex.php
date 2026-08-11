<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Support\Str;

class PageIndex extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $isOpen = false;
    public $page_id;
    public $slug;
    public $status = 'draft';
    
    // Translation fields (id and en)
    public $title_id, $content_id;
    public $title_en, $content_en;

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $pages = Page::where('slug', 'like', '%' . $this->search . '%')
            ->with('translations')
            ->paginate(10);

        return view('livewire.admin.page-index', [
            'pages' => $pages
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'title_en' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $slug = Str::slug($this->title_en);

        $page = Page::updateOrCreate(['id' => $this->page_id], [
            'slug' => $slug,
            'status' => $this->status,
        ]);

        // Save English translation
        PageTranslation::updateOrCreate(
            ['page_id' => $page->id, 'locale' => 'en'],
            ['title' => $this->title_en, 'content' => $this->content_en]
        );

        // Save Indonesian translation
        PageTranslation::updateOrCreate(
            ['page_id' => $page->id, 'locale' => 'id'],
            ['title' => $this->title_id, 'content' => $this->content_id]
        );

        session()->flash('message', 
            $this->page_id ? 'Page Updated Successfully.' : 'Page Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $page = Page::with('translations')->findOrFail($id);
        $this->page_id = $id;
        $this->slug = $page->slug;
        $this->status = $page->status;

        $this->title_en = $page->getTranslation('title', 'en');
        $this->content_en = $page->getTranslation('content', 'en');
        
        $this->title_id = $page->getTranslation('title', 'id');
        $this->content_id = $page->getTranslation('content', 'id');
    
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Page::find($id)->delete();
        session()->flash('message', 'Page Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->page_id = null;
        $this->slug = '';
        $this->status = 'draft';
        $this->title_id = '';
        $this->content_id = '';
        $this->title_en = '';
        $this->content_en = '';
    }
}
