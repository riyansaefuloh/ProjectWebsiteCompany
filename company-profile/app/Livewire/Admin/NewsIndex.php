<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\News;
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
    public $coverFile;

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
        $this->status = $news->status;
        $this->published_at = $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $news = $this->editingId 
            ? News::findOrFail($this->editingId)
            : new News();

        $news->slug = Str::slug($this->title_en);
        $news->author_id = auth()->id();
        $news->status = $this->status;
        $news->published_at = $this->published_at ?? now();
        $news->save();

        // Simpan Terjemahan (EN & ID)
        $news->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['title' => $this->title_en, 'excerpt' => $this->excerpt_en, 'content' => $this->content_en]
        );
        $news->translations()->updateOrCreate(
            ['locale' => 'id'],
            ['title' => $this->title_id, 'excerpt' => $this->excerpt_id, 'content' => $this->content_id]
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
        News::findOrFail($id)->delete();
        session()->flash('message', 'Article deleted successfully!');
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
        $this->coverFile = null;
    }

    public function render()
    {
        $newsList = News::with(['translations', 'author'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.news-index', [
            'newsList' => $newsList,
        ]);
    }
}
