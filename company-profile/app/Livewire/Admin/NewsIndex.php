<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use App\Models\User;
use Illuminate\Support\Str;

class NewsIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields (PRD Bab 9.1)
    public string $title_en = '';
    public string $title_id = '';
    public string $excerpt_en = '';
    public string $excerpt_id = '';
    public string $content_en = '';
    public string $content_id = '';
    public string $status = 'published';
    public ?string $published_at = null;
    
    // New Fields: Category, Tags, SEO, Media
    public ?string $news_category_id = null;
    public array $selectedTags = [];
    public string $meta_title_en = '';
    public string $meta_title_id = '';
    public string $meta_description_en = '';
    public string $meta_description_id = '';
    
    public $coverFile;
    public ?string $existingCoverUrl = null;
    public string $activeTab = 'en';

    protected function rules(): array
    {
        return [
            'title_en'     => 'required|string|max:200',
            'title_id'     => 'required|string|max:200',
            'excerpt_en'   => 'nullable|string|max:500',
            'excerpt_id'   => 'nullable|string|max:500',
            'content_en'   => 'required|string',
            'content_id'   => 'required|string',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'selectedTags'     => 'array',
            'meta_title_en'    => 'nullable|string|max:255',
            'meta_title_id'    => 'nullable|string|max:255',
            'meta_description_en' => 'nullable|string|max:500',
            'meta_description_id' => 'nullable|string|max:500',
            'coverFile'    => 'nullable|image|max:3072', // Max 3MB

        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->published_at = date('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $news = News::with('translations')->findOrFail($id);
        $this->editingId = $news->id;
        $this->title_en = $news->getTranslation('title', 'en') ?? '';
        $this->title_id = $news->getTranslation('title', 'id') ?? '';
        $this->excerpt_en = $news->getTranslation('excerpt', 'en') ?? '';
        $this->excerpt_id = $news->getTranslation('excerpt', 'id') ?? '';
        $this->content_en = $news->getTranslation('content', 'en') ?? '';
        $this->content_id = $news->getTranslation('content', 'id') ?? '';
        $this->meta_title_en = $news->getTranslation('meta_title', 'en') ?? '';
        $this->meta_title_id = $news->getTranslation('meta_title', 'id') ?? '';
        $this->meta_description_en = $news->getTranslation('meta_description', 'en') ?? '';
        $this->meta_description_id = $news->getTranslation('meta_description', 'id') ?? '';
        $this->status = $news->status;
        $this->published_at = $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : null;
        
        $this->news_category_id = $news->news_category_id;
        $this->selectedTags = $news->tags()->pluck('news_tags.id')->toArray();
        $this->existingCoverUrl = $news->getFirstMediaUrl('covers');
        $this->activeTab = 'en';

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $news = $this->editingId 
            ? News::findOrFail($this->editingId)
            : new News();

        $news->slug = Str::slug($this->title_en);
        if (!$this->editingId) {
            $news->author_id = auth()->id() ?? User::first()->id; // Fallback if no auth
        }
        $news->news_category_id = $this->news_category_id ?: null;
        $news->status = $this->status;
        $news->published_at = $this->published_at ?? now();
        $news->save();

        // Sync Tags
        $news->tags()->sync($this->selectedTags);

        // Simpan Terjemahan (EN & ID)
        $news->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => $this->title_en, 'excerpt' => $this->excerpt_en, 'content' => $this->content_en,
                'meta_title' => $this->meta_title_en, 'meta_description' => $this->meta_description_en
            ]
        );
        $news->translations()->updateOrCreate(
            ['locale' => 'id'],
            [
                'title' => $this->title_id, 'excerpt' => $this->excerpt_id, 'content' => $this->content_id,
                'meta_title' => $this->meta_title_id, 'meta_description' => $this->meta_description_id
            ]
        );

        // Process Upload Cover via Spatie MediaLibrary
        if ($this->coverFile) {
            $news->clearMediaCollection('covers');
            $news->addMedia($this->coverFile->getRealPath())->toMediaCollection('covers');
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Article saved successfully!');
    }

    public function delete(string $id): void
    {
        $news = News::findOrFail($id);
        $news->clearMediaCollection('covers');
        $news->delete();
        session()->flash('message', 'Article deleted successfully!');
    }

    public function deleteCover(): void
    {
        if ($this->editingId) {
            $news = News::findOrFail($this->editingId);
            $news->clearMediaCollection('covers');
            $this->existingCoverUrl = null;
            session()->flash('message', 'Cover deleted successfully!');
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title_en = '';
        $this->title_id = '';
        $this->excerpt_en = '';
        $this->excerpt_id = '';
        $this->content_en = '';
        $this->content_id = '';
        $this->status = 'published';
        $this->published_at = null;
        $this->news_category_id = null;
        $this->selectedTags = [];
        $this->meta_title_en = '';
        $this->meta_title_id = '';
        $this->meta_description_en = '';
        $this->meta_description_id = '';
        $this->coverFile = null;
        $this->existingCoverUrl = null;
        $this->activeTab = 'en';
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $newsList = News::with(['translations', 'author'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.news-index', [
            'newsList'   => $newsList,
            'categories' => NewsCategory::orderBy('name')->get(),
            'tags'       => NewsTag::orderBy('name')->get(),
        ]);
    }
}
