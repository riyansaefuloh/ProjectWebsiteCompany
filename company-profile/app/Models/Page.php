<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslation;

class Page extends Model
{
    use HasUlids, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'status',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }
}
