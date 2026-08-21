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
        // 'file_path' dibuang: PDF sertifikat ditangani koleksi media 'pdfs'.
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

    /**
     * Konversi media otomatis ke WebP — PRD Bab 5.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Logo sertifikasi dalam format WebP — untuk halaman sertifikasi publik
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(90) // Kualitas lebih tinggi agar logo tajam
            ->nonQueued();

        // Thumbnail kecil 200px — untuk trust bar di beranda
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(200)
            ->quality(85)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        // Koleksi logo sertifikasi (hanya 1 gambar)
        $this->addMediaCollection('logos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);

        // Koleksi dokumen sertifikat (hanya 1 file PDF)
        $this->addMediaCollection('pdfs')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
