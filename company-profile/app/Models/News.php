<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasTranslation;

class News extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'author_id',
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
     * Scope query pencarian Full-Text Search (FTS) untuk Berita/Artikel.
     * Penggunaan: News::search($searchTerm)->get();
     */
    public function scopeSearch($query, string $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        $driver = \DB::connection()->getDriverName();

        // Jika menggunakan PostgreSQL, gunakan FTS tsvector & plainto_tsquery
        if ($driver === 'pgsql') {
            return $query->whereHas('translations', function ($q) use ($term) {
                $q->whereRaw(
                    "to_tsvector('english', title || ' ' || COALESCE(excerpt, '') || ' ' || COALESCE(content, '')) @@ plainto_tsquery('english', ?)",
                    [$term]
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

}
