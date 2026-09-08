<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasTranslation;

class Certification extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia, HasTranslation;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'issuer',
        'certificate_number',
        'issued_at',
        'expires_at',
        // 'file_path' dibuang: PDF sertifikat ditangani koleksi media 'pdfs'.
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    /**
     * Sertifikat yang MASIH BERLAKU hari ini.
     *
     * status = 'active' saja tidak cukup. Status itu disetel tangan dan tidak
     * ada yang membaliknya saat tanggalnya lewat, jadi sertifikat yang sudah
     * kedaluwarsa tetap berstatus aktif sampai ada yang ingat menyuntingnya.
     * Tanggalnya yang tahu, bukan statusnya.
     *
     * expires_at kosong berarti tidak berbatas waktu — bukan berarti
     * kedaluwarsa. Tanpa cabang IS NULL, sertifikat tanpa tanggal habis justru
     * yang pertama hilang.
     *
     * SENGAJA tidak dipakai di halaman Sertifikasi. Halaman itu catatan
     * lengkap: ia menampilkan yang kedaluwarsa juga, dengan label "Expired on"
     * alih-alih "Valid until" — dan itu jujur. Yang tidak boleh menampilkannya
     * adalah bilah kepercayaan di beranda, karena di sana tidak ada ruang untuk
     * mengatakan bahwa satu di antaranya sudah lewat; deretan lambang di sana
     * terbaca sebagai klaim yang berlaku SEKARANG.
     */
    public function scopeBerlaku($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhereDate('expires_at', '>=', now()->toDateString());
            });
    }

    //Relasi ke data terjemahan sertifikat.
    public function translations(): HasMany
    {
        return $this->hasMany(CertificationTranslation::class);
    }    
    //  Relasi Many-to-Many ke tabel Produk (Sertifikat ini dimiliki oleh produk apa saja).
    //  Pivot Table: product_certification
    
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_certification');
    }

    /**
     * Konversi media otomatis ke WebP — PRD Bab 5.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Logo sertifikasi dalam format WebP — untuk halaman sertifikasi publik
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(90) // Kualitas lebih tinggi agar logo tajam
            ->nonQueued();

        // Thumbnail kecil 200px — untuk trust bar di beranda
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(200)
            ->quality(85)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        // Koleksi logo sertifikasi (hanya 1 gambar)
        $this->addMediaCollection('logos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);

        // Koleksi dokumen sertifikat (hanya 1 file PDF)
        $this->addMediaCollection('pdfs')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
