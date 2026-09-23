<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    // Timestamps disabled
    public $timestamps = false; 

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
