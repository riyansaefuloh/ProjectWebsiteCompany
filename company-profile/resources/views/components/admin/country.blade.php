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
        'md' => ['gap-2',   'px-2 py-0.5 text-[10px]', 'text-[13px] text-ink-muted'],
        'sm' => ['gap-1.5', 'px-1.5 py-0 text-[10px]', 'text-[12px] text-ink-faint'],
    ][$size] ?? null;

    [$jarak, $keping, $teks] = $rupa ?? [
        'gap-2', 'px-2 py-0.5 text-[10px]', 'text-[13px] text-ink-muted',
    ];
@endphp

<span {{ $attributes->class(['flex items-center', $jarak]) }}>
    <span class="inline-flex shrink-0 items-center rounded-full bg-mist-deep
                 font-bold tracking-[0.04em] text-ink-muted {{ $keping }}">{{ $kode ?: '??' }}</span>

    <span class="min-w-0 truncate {{ $teks }}"
          title="{{ $nama }}">{{ $nama ?? '—' }}</span>
</span>
