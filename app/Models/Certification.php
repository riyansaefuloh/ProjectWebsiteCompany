<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasTranslation;

class Certification extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'issuer',
        'certificate_number',
        'issued_at',
        'expires_at',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function scopeBerlaku($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhereDate('expires_at', '>=', now()->toDateString());
            });
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CertificationTranslation::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_certification');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(90)
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(200)
            ->quality(85)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);

        $this->addMediaCollection('pdfs')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
