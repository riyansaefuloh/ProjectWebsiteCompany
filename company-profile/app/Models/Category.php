<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslation;

class Category extends Model
{
    use HasUlids, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'icon',
        'sort_order',
        'status',
    ];

    
     //Relasi ke data terjemahan bahasa (Kategori mendukung ID/EN).
     
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    
    //  Relasi One-to-Many ke tabel Produk (Satu Kategori memiliki banyak Produk).
     
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
