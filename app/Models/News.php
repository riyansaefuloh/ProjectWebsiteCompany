<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasTranslation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'author_id',
        'news_category_id',
        'cover',
        'published_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    
    //  Relasi ke data terjemahan berita (ID/EN).
     
    public function translations(): HasMany
    {
        return $this->hasMany(NewsTranslation::class);
    }

    
    //  Relasi ke User pembuat berita (Penulis / Author).
     
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relasi ke kategori berita.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * Relasi ke tag berita (Many-to-Many).
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_news_tag');
    }

        /**
     * Scope query pencarian Full-Text Search (FTS) untuk Berita/Artikel.
     * Penggunaan: News::search($searchTerm)->get();
     */
    public function scopeSearch($query, string $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

        /*
         * PostgreSQL: pencarian teks penuh dengan kamus 'simple' dan pencocokan
         * awalan — lihat App\Support\PencarianTeks untuk alasan keduanya.
         *
         * Kalau kata pencariannya habis sesudah dibersihkan (misalnya cuma
         * berisi tanda baca), kueri FTS-nya dilewati dan pencariannya jatuh ke
         * pencocokan LIKE di bawah. Kueri kosong lebih baik tidak menyaring
         * apa-apa daripada mengembalikan nol hasil tanpa sebab yang terlihat.
         */
        $kueri = \App\Support\PencarianTeks::kueriAwalan($term);

        if ($driver === 'pgsql' && $kueri !== null) {
            $kamus = \App\Support\PencarianTeks::kamus();

            return $query->whereHas('translations', function ($q) use ($kueri, $kamus) {
                $q->whereRaw(
                    "to_tsvector('{$kamus}', title || ' ' || COALESCE(excerpt, '') || ' ' || COALESCE(content, ''))"
                    . " @@ to_tsquery('{$kamus}', ?)",
                    [$kueri]
                );
            });
        }

        // Fallback untuk driver selain PostgreSQL (SQLite / MySQL)
        return $query->whereHas('translations', function ($transQ) use ($term) {
            $transQ->where('title', 'LIKE', "%{$term}%")
                   ->orWhere('excerpt', 'LIKE', "%{$term}%")
                   ->orWhere('content', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Konversi media otomatis ke WebP — PRD Bab 5.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // WebP full-size untuk cover berita di halaman detail
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->nonQueued();

        // Thumbnail WebP 600x400px — untuk kartu berita di listing
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(600)
            ->height(400)
            ->quality(80)
            ->nonQueued();
    }

    /**
     * Daftarkan koleksi media untuk berita.
     */
    public function registerMediaCollections(): void
    {
        // Koleksi cover berita (hanya 1 gambar, menimpa yang lama)
        $this->addMediaCollection('covers')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
