<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportMarketTranslation extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'export_market_id',
        'locale',
        'name',
        'note',
    ];

    public function exportMarket(): BelongsTo
    {
        return $this->belongsTo(ExportMarket::class, 'export_market_id');
    }
}
