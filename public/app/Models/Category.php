<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasUlids, HasTranslation, InteractsWithMedia;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'icon', // We'll keep icon field for backward compatibility, but won't use it actively for image. Or maybe use it to store string if no image.
        'sort_order',
        'status',
    ];

    /**
     * Relasi ke data terjemahan bahasa (Kategori mendukung ID/EN).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    /**
     * Relasi One-to-Many ke tabel Produk (Satu Kategori memiliki banyak Produk).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Register Media Conversions (Auto WebP).
     * PRD Bab 8.2 & 7.10: Konversi gambar otomatis ke WebP.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued(); // Langsung konversi saat upload (bisa diganti queued jika server kuat)
    }
}
