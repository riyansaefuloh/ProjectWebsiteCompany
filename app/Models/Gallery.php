<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    // Hanya kolom 'name' yang ada di migrasi database Anda
    protected $fillable = [
        'name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }
}
