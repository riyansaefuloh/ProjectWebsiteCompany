<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
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
        'file_path',
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

    //Relasi ke data terjemahan sertifikat.
    public function translations(): HasMany
    {
        return $this->hasMany(CertificationTranslation::class);
    }    
    //  Relasi Many-to-Many ke tabel Produk (Sertifikat ini dimiliki oleh produk apa saja).
    //  Pivot Table: product_certification
    
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_certification');
    }
}
