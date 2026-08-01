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

    
    //  Relasi ke data translasi bahasa (menyimpan Nama & Deskripsi produk dalam ID/EN).
    
    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    
    //  Relasi ke spesifikasi teknis dinamis (seperti moisture, grade, dll.).
    
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    
    //  Relasi Banyak-ke-Banyak (Many-to-Many) ke tabel Sertifikasi.
    //  Pivot Table: product_certification
     
    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class, 'product_certification');
    }
        /**
     * Scope query untuk pencarian PostgreSQL Full-Text Search (FTS) dengan fallback.
     * Penggunaan di Controller/Livewire: Product::search($searchTerm)->get();
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
                    "to_tsvector('english', name || ' ' || COALESCE(description, '')) @@ plainto_tsquery('english', ?)",
                    [$term]
                );
            })
            ->orWhere('hs_code', 'ILIKE', "%{$term}%")
            ->orWhere('origin', 'ILIKE', "%{$term}%");
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

}
