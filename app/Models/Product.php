<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasTranslation;

class Product extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia, SoftDeletes, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    // Field fillable disesuaikan dengan kolom migrasi database produk ekspor
    protected $fillable = [
        'category_id',
        'slug',
        'hs_code',
        'moq',
        'supply_capacity',
        'packaging',
        'origin',
        'indicative_price',
        'currency',
        'incoterms',
        'is_featured',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'indicative_price' => 'decimal:2',
        ];
    }

    
    //  Relasi ke Kategori (Satu produk berasosiasi dengan satu kategori).
     
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    
    
    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    
    //  Relasi ke spesifikasi teknis dinamis (seperti moisture, grade, dll.).
    
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    
     
    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class, 'product_certification');
    }
    public function scopeSearch($query, string $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

        /*
         * Kamus 'simple' dan pencocokan awalan — lihat App\Support\PencarianTeks.
         * Kata pencarian yang habis sesudah dibersihkan jatuh ke LIKE di bawah.
         */
        $kueri = \App\Support\PencarianTeks::kueriAwalan($term);

        if ($driver === 'pgsql' && $kueri !== null) {
            $kamus = \App\Support\PencarianTeks::kamus();

            return $query->where(function ($q) use ($term, $kueri, $kamus) {
                $q->whereHas('translations', function ($transQ) use ($kueri, $kamus) {
                    $transQ->whereRaw(
                        "to_tsvector('{$kamus}', name || ' ' || COALESCE(description, ''))"
                        . " @@ to_tsquery('{$kamus}', ?)",
                        [$kueri]
                    );
                })
                ->orWhere('hs_code', 'ILIKE', "%{$term}%")
                ->orWhere('origin', 'ILIKE', "%{$term}%");
            });
        }

        // Fallback untuk driver selain PostgreSQL (SQLite / MySQL)
        return $query->where(function ($q) use ($term) {
            $q->where('hs_code', 'LIKE', "%{$term}%")
              ->orWhere('origin', 'LIKE', "%{$term}%")
              ->orWhereHas('translations', function ($transQ) use ($term) {
                  $transQ->where('name', 'LIKE', "%{$term}%")
                         ->orWhere('description', 'LIKE', "%{$term}%");
              });
        });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->nonQueued(); // Dijalankan langsung saat upload

        // Thumbnail WebP 400px — untuk kartu produk di katalog publik
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(400)
            ->height(300)
            ->quality(80)
            ->nonQueued();

        // Medium WebP 800px — untuk lightbox & detail produk
        $this->addMediaConversion('medium')
            ->format('webp')
            ->width(800)
            ->quality(85)
            ->nonQueued();
    }

    /**
     * Daftarkan koleksi media untuk produk.
     */
    public function registerMediaCollections(): void
    {
        // Koleksi galeri gambar produk (multi-gambar)
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }
}
