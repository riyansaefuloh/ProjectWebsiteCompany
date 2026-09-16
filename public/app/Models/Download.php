<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Download extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'file_path',
        'require_email',
        'download_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'require_email' => 'boolean',
            'download_count' => 'integer',
        ];
    }
}
