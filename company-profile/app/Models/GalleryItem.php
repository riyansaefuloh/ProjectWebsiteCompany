<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class GalleryItem extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia;

    protected $keyType = 'string';
    public $incrementing = false;

    // Hanya kolom 'gallery_id' yang ada di tabel migrasi asli Anda
    protected $fillable = [
        'gallery_id',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
