@props([
    'status',

    'konteks' => null,
])

@php
    
    $sebutan = [
        // Inquiry
        'new'        => 'Baru',
        'processing' => 'Diproses',
        'quoted'     => 'Ditawar',
        'closed'     => 'Selesai',
        'rejected'   => 'Ditolak',

        // Keadaan terbit — dipakai produk, berita, halaman, galeri.
        'published'  => 'Terbit',
        'draft'      => 'Draf',

        // Keadaan hidup-mati — dipakai kategori, sertifikasi, pasar ekspor.
        'active'     => 'Aktif',
        'inactive'   => 'Nonaktif',

        // Gerbang unduhan — dipakai halaman Unduhan.
        'gated'      => 'Perlu email',
        'open'       => 'Terbuka',
    ];

    $khusus = [
        'produk' => ['published' => 'Aktif'],
    ];

    $sebutan = array_merge($sebutan, $khusus[$konteks ?? ''] ?? []);

    $gaya = [
        'new'        => 'bg-status-new/10 text-status-new',
        'processing' => 'bg-status-processing/10 text-status-processing',
        'quoted'     => 'bg-status-quoted/10 text-status-quoted',
        'closed'     => 'bg-status-done/10 text-status-done',
        'rejected'   => 'bg-status-rejected/10 text-status-rejected',

        'published'  => 'bg-status-done/10 text-status-done',
        'draft'      => 'border-line-strong text-ink-muted',

        'active'     => 'bg-status-done/10 text-status-done',
        'inactive'   => 'bg-status-rejected/10 text-status-rejected',

        'gated'      => 'bg-status-done/10 text-status-done',
        'open'       => 'border-line-strong text-ink-muted',
    ];
@endphp

<span {{ $attributes->class([
        'admin-pill font-semibold',
        $gaya[$status] ?? 'bg-mist-deep text-ink-muted',
    ]) }}>{{ $sebutan[$status] ?? $status }}</span>
