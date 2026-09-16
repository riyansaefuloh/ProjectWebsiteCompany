<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTranslation extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'news_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
