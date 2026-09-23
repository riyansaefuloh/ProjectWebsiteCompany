<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Log kunjungan halaman publik
class SiteVisit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'visitor',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }
}
