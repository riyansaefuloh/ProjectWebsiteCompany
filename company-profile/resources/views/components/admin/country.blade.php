@props([
    'code',
    'size' => 'md',   // md = berdiri sendiri di kolomnya | sm = baris kedua di bawah datum lain
])

@php
    /*
     * Kode ISO + nama negaranya.
     *
     * Kodenya tetap ditampilkan meski namanya sudah ada di sebelahnya: kode
     * itulah yang dipakai di berkas ekspor dan di dokumen pengiriman, jadi
     * menyembunyikannya berarti memaksa sales membuka detail hanya untuk
     * menyalin dua huruf.
     *
     * Bendera sengaja tidak dipakai — proyek ini tidak menyimpan berkasnya,
     * dan emoji bendera tidak tergambar sama sekali di Windows.
     */
    $kode  = strtoupper((string) $code);
    $nama  = config('countries', [])[$kode] ?? null;

    /*
     * Ukuran "sm" dipakai saat negaranya jadi baris kedua di bawah datum
     * lain — nama perusahaan, misalnya. Di sana ia keterangan, bukan judul,
     * jadi ia harus lebih kecil dan lebih pudar dari baris di atasnya;
     * kalau bobotnya sama, sel itu terbaca sebagai dua hal setara dan mata
     * kehilangan urutan bacanya.
     */
    $rupa = [
        'md' => ['gap-2',   'px-2 py-0.5',  'text-admin-body text-ink-muted'],
        'sm' => ['gap-1.5', 'px-1.5 py-0',  'text-admin-caption text-ink-faint'],
    ][$size] ?? null;

    [$jarak, $keping, $teks] = $rupa ?? [
        'gap-2', 'px-2 py-0.5', 'text-admin-body text-ink-muted',
    ];
@endphp

{{-- Memakai .admin-code, bukan huruf antarmuka yang ditebalkan: kode ISO
     adalah dua huruf yang dicocokkan dengan berkas ekspor dan dokumen
     pengiriman, bukan kata yang dibaca. --}}
<span {{ $attributes->class(['flex items-center', $jarak]) }}>
    <span class="admin-code inline-flex shrink-0 items-center rounded-full border border-line
                 bg-mist text-admin-caption font-medium text-brand {{ $keping }}">{{ $kode ?: '??' }}</span>

    <span class="min-w-0 truncate {{ $teks }}"
          title="{{ $nama }}">{{ $nama ?? '—' }}</span>
</span>
