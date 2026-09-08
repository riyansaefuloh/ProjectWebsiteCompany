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

    /**
     * Singgahan nama situs dibuang tiap kali barisnya berubah.
     *
     * Di model, bukan di SettingIndex::save(): panel bukan satu-satunya yang
     * menulis ke tabel ini — ada penyemai, ada tinker, dan ada apa pun yang
     * ditambahkan nanti. Yang dipasang di satu jalur simpan akan dilewati oleh
     * jalur berikutnya, dan namanya lalu basi tanpa ada yang tahu sebabnya.
     */
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
