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
    /*
     * Yang membedakan "sm" dari "md" tinggal NAMA negaranya — ukurannya, bukan
     * kepingnya.
     *
     * Dulu kepingnya ikut mengecil: 22px pada md, 18px pada sm. Keduanya lalu
     * berdiri di halaman yang sama — daftar inquiry terbaru memakai sm, tabel
     * distribusi negara memakai md — dan dua ukuran untuk keping yang sama
     * terbaca sebagai dua benda berbeda. Sekarang keduanya .admin-pill, dan
     * yang menyatakan hierarki cuma nada dan ukuran teks di sebelahnya.
     */
    $rupa = [
        'md' => ['gap-2',   'text-admin-body text-ink-muted'],
        'sm' => ['gap-1.5', 'text-admin-caption text-ink-faint'],
    ][$size] ?? null;

    [$jarak, $teks] = $rupa ?? ['gap-2', 'text-admin-body text-ink-muted'];
@endphp

{{-- Memakai .admin-code, bukan huruf antarmuka yang ditebalkan: kode ISO
     adalah dua huruf yang dicocokkan dengan berkas ekspor dan dokumen
     pengiriman, bukan kata yang dibaca. --}}
<span {{ $attributes->class(['flex items-center', $jarak]) }}>
    <span class="admin-code admin-pill border-line bg-mist font-medium text-brand">{{ $kode ?: '??' }}</span>

    <span class="min-w-0 truncate {{ $teks }}"
          title="{{ $nama }}">{{ $nama ?? '—' }}</span>
</span>
