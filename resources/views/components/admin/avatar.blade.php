@props([
    'name' => null,
    'size' => 'md',    // sm | md | lg | xl
    'tone' => 'frame', // frame | quiet | brand
])

@php
    // Dua huruf pertama dari dua kata pertama. Nama asing seperti
    // "Fatima Al-Rashid" jadi "FA"; nama satu kata jadi satu huruf; nama
    // kosong jadi "?" — bukan lingkaran hampa yang tampak rusak.
    $inisial = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->implode('') ?: '?';

    // Ukurannya dipetakan ke kelas utuh, bukan dirangkai seperti h-{{ $size }}:
    // Tailwind memindai berkas sumber apa adanya, jadi kelas yang baru terbentuk
    // saat penyajian tidak pernah ikut dibuatkan gayanya.
    $ukuran = [
        'sm' => 'h-9 w-9 text-admin-label',
        'md' => 'h-9 w-9 text-admin-body',

        'lg' => 'h-10 w-10 text-admin-body',

        'xl' => 'h-13 w-13 text-admin-title',
    ];

    $nada = [
        'frame' => 'border border-line bg-mist text-brand',
        'quiet' => 'bg-mist-deep text-ink-muted',
        
        'brand' => 'bg-brand-deep text-white',
    ];
@endphp

<span aria-hidden="true" {{ $attributes->class([
        'inline-flex shrink-0 select-none items-center justify-center rounded-full font-bold',
        $ukuran[$size] ?? $ukuran['md'],
        $nada[$tone] ?? $nada['frame'],
    ]) }}>{{ $inisial }}</span>
