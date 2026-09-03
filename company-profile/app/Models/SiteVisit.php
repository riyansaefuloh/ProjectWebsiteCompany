<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu halaman publik yang dibuka seseorang.
 *
 * Diisi oleh App\Http\Middleware\RecordSiteVisit dan dibaca oleh dasbor admin.
 * Tanpa created_at/updated_at bawaan: barisnya tidak pernah disunting, jadi
 * updated_at akan selamanya sama dengan created_at — satu kolom penuh yang
 * tidak pernah menjawab pertanyaan apa pun. Yang dipakai `visited_at`, dan
 * namanya menyebut sendiri apa yang dicatatnya.
 */
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
