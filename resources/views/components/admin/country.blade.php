@props([
    'code',
    'size' => 'md',   // md = berdiri sendiri di kolomnya | sm = baris kedua di bawah datum lain
])

@php
    
    $kode  = strtoupper((string) $code);
    $nama  = config('countries', [])[$kode] ?? null;

    $rupa = [
        'md' => ['gap-2',   'text-admin-body text-ink-muted'],
        'sm' => ['gap-1.5', 'text-admin-caption text-ink-faint'],
    ][$size] ?? null;

    [$jarak, $teks] = $rupa ?? ['gap-2', 'text-admin-body text-ink-muted'];
@endphp

<span {{ $attributes->class(['flex items-center', $jarak]) }}>
    <span class="admin-code admin-pill border-line bg-mist font-medium text-brand">{{ $kode ?: '??' }}</span>

    <span class="min-w-0 truncate {{ $teks }}"
          title="{{ $nama }}">{{ $nama ?? '—' }}</span>
</span>
