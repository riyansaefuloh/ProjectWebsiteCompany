<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryItem extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia;

    protected $keyType = 'string';
    public $incrementing = false;

    // Kolom 'gallery_id', 'type', dan 'video_url'
    protected $fillable = [
        'gallery_id',
        'type',
        'video_url',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Konversi media otomatis ke WebP — PRD Bab 5.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // WebP full-size untuk lightbox galeri
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(90)  // dinaikkan dari 85 → 90 agar lebih tajam di lightbox
            ->nonQueued();

        // Thumbnail WebP untuk grid galeri publik
        // dinaikkan dari 500x375 → 1200x900 agar tidak blur di layar besar/retina
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(1200)
            ->height(900)
            ->quality(88)  // dinaikkan dari 80 → 88
            ->nonQueued();
    }

    /**
     * Daftarkan koleksi media untuk item galeri.
     */
    public function registerMediaCollections(): void
    {
        // Koleksi foto galeri (pameran, fasilitas, produk)
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }
}
