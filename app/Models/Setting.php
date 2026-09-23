<?php

namespace App\Models;

use App\Support\NamaSitus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Setting extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'value',
    ];

    protected static function booted(): void
    {
        $bersihkan = function (self $setting) {
            if ($setting->key === 'company_name') {
                NamaSitus::lupakan();
            }
        };

        static::saved($bersihkan);
        static::deleted($bersihkan);
    }
}
