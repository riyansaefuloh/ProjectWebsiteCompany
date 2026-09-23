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

    public function translations(): HasMany
    {
        return $this->hasMany(NewsTranslation::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_news_tag');
    }

    public function scopeSearch($query, string $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

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

        return $query->whereHas('translations', function ($transQ) use ($term) {
            $transQ->where('title', 'LIKE', "%{$term}%")
                   ->orWhere('excerpt', 'LIKE', "%{$term}%")
                   ->orWhere('content', 'LIKE', "%{$term}%");
        });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(600)
            ->height(400)
            ->quality(80)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('covers')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
