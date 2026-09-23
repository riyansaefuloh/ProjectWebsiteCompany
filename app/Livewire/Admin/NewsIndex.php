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
use App\Services\TranslationService;
use Illuminate\Support\Str;

class NewsIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $selectedStatus = '';
    public string $selectedCategory = '';

    public bool $showModal = false;
    public ?string $editingId = null;

    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'selectedStatus', 'selectedCategory'], true)) {
            $this->resetPage();
        }
    }

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
    public bool $isTranslating = false;
    public ?string $galatTerjemah = null;

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
            'coverFile'    => 'nullable|image|max:3072',
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->galatTerjemah = null;
        $this->resetForm();
        $this->published_at = date('Y-m-d\TH:i');
        $this->showModal = true;
        $this->dispatch('open-tinymce');
    }

    public function autoTranslate(): void
    {
        $this->galatTerjemah = null;

        if (empty(trim($this->title_id)) && empty(trim($this->content_id))) {
            $this->galatTerjemah = 'Isi dulu judul atau isi artikelnya dalam Bahasa Indonesia.';
            return;
        }

        $this->isTranslating = true;

        $layanan    = app(TranslationService::class);
        $translated = $layanan->translateMany([
            'title'            => $this->title_id,
            'excerpt'          => $this->excerpt_id,
            'content'          => $this->content_id,
            'meta_title'       => $this->meta_title_id,
            'meta_description' => $this->meta_description_id,
        ]);

        if (!empty($translated['title']))            $this->title_en            = $translated['title'];
        if (!empty($translated['excerpt']))          $this->excerpt_en          = $translated['excerpt'];
        if (!empty($translated['content']))          $this->content_en          = $translated['content'];
        if (!empty($translated['meta_title']))       $this->meta_title_en       = $translated['meta_title'];
        if (!empty($translated['meta_description'])) $this->meta_description_en = $translated['meta_description'];

        if ($layanan->sebabGagal) {
            $this->galatTerjemah = $layanan->sebabGagal;
        }

        $this->isTranslating = false;
        $this->activeTab = 'en';
    }

    public function edit(string $id): void
    {
        $this->resetValidation();
        $this->galatTerjemah = null;

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

    private function slugUnik(string $kelas, string $nama): string
    {
        $dasar = Str::slug($nama) ?: 'item';
        $slug  = $dasar;
        $n     = 2;

        while ($kelas::where('slug', $slug)->exists()) {
            $slug = $dasar . '-' . $n++;
        }

        return $slug;
    }

    public function tambahKategori(string $nama): void
    {
        $nama = trim($nama);

        $this->resetValidation('news_category_id');

        if ($nama === '' || mb_strlen($nama) > 100) {
            $this->addError('news_category_id', 'Nama kategori 1–100 karakter.');

            return;
        }

        $kategori = NewsCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($nama)])->first()
            ?? NewsCategory::create([
                'name' => $nama,
                'slug' => $this->slugUnik(NewsCategory::class, $nama),
            ]);

        $this->news_category_id = $kategori->id;
    }

    public function hapusKategori(string $id): void
    {
        $this->resetValidation('news_category_id');

        $kategori = NewsCategory::withCount('news')->find($id);

        if (! $kategori) {
            return;
        }

        if ($kategori->news_count > 0) {
            $this->addError('news_category_id', 'Kategori "' . $kategori->name . '" masih dipakai '
                . $kategori->news_count . ' berita. Pindahkan berita itu dulu.');

            return;
        }

        if ((string) $this->news_category_id === (string) $id) {
            $this->news_category_id = null;
        }

        $kategori->delete();
    }

    public function tambahTag(string $nama): void
    {
        $nama = trim($nama);

        $this->resetValidation('selectedTags');

        if ($nama === '' || mb_strlen($nama) > 50) {
            $this->addError('selectedTags', 'Nama tag 1–50 karakter.');

            return;
        }

        $tag = NewsTag::whereRaw('LOWER(name) = ?', [mb_strtolower($nama)])->first()
            ?? NewsTag::create([
                'name' => $nama,
                'slug' => $this->slugUnik(NewsTag::class, $nama),
            ]);

        if (! in_array((string) $tag->id, array_map('strval', $this->selectedTags), true)) {
            $this->selectedTags[] = (string) $tag->id;
        }
    }

    public function hapusTag(string $id): void
    {
        $tag = NewsTag::find($id);

        if (! $tag) {
            return;
        }

        $tag->news()->detach();
        $tag->delete();

        $this->selectedTags = array_values(array_filter(
            $this->selectedTags,
            fn ($x) => (string) $x !== (string) $id
        ));
    }

    public function save(): void
    {
        $this->validate();

        $news = $this->editingId
            ? News::findOrFail($this->editingId)
            : new News();

        $news->slug = Str::slug($this->title_en);
        if (!$this->editingId) {
            $news->author_id = auth()->id() ?? User::first()->id;
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

    #[Layout('components.layouts.app')]
    public function render()
    {
        $newsList = News::with(['translations', 'author', 'category', 'tags', 'media'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->when($this->selectedStatus, function ($q) {
                $q->where('status', $this->selectedStatus);
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('news_category_id', $this->selectedCategory);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.news-index', [
            'newsList'       => $newsList,
            'categories'     => NewsCategory::orderBy('name')->get(),
            'tags'           => NewsTag::orderBy('name')->get(),
            'daftarKategori' => NewsCategory::orderBy('name')->get(),
        ]);
    }
}
