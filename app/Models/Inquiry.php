<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'company',
        'email',
        'country_code',
        'phone',
        'product_id',
        'volume',
        'incoterms',
        'message',
        'status',          // 'new', 'processing', 'quoted', 'closed', 'rejected'
        'assigned_to',     // ID user sales yang menangani
        'internal_note',
        'ip_address',
    ];

    
    //  Relasi ke Produk yang ditanyakan (jika ada).
     
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    
    //  Relasi ke User Sales yang ditugaskan menangani inquiry ini.
     
    public function assignedSales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
