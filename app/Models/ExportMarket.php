<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslation;

class ExportMarket extends Model
{
    use HasUlids, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'country_code',
        'region',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    //  Relasi ke data terjemahan nama negara & catatan pasar ekspor.
    public function translations(): HasMany
    {
        return $this->hasMany(ExportMarketTranslation::class);
    }
}
