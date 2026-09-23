@props([
    'label',
    'value'  => null,
    'kosong' => 'Tidak diisi',
    'blok'   => false,   // isian panjang yang boleh membungkus ke banyak baris
])

@php
    
@endphp

<div {{ $attributes->class(['flex min-w-0 flex-col']) }}>
    
    <dt class="text-admin-label text-ink-faint">{{ $label }}</dt>

    <dd class="mt-1.5 flex-1">
        <div @class([
            'h-full cursor-default rounded-control border border-line bg-canvas px-3.5 py-2.5',
            'flex min-h-[42px] items-center' => ! $blok,
            'min-h-[112px]'                  => $blok,
        ])>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @elseif(filled($value))
                <span @class([
                    'min-w-0 text-admin-body leading-5 text-ink',
                    'break-words'                                             => ! $blok,
                    'block whitespace-pre-line leading-relaxed text-ink-muted' => $blok,
                ])>{{ $value }}</span>
            @else
                <span class="text-admin-body leading-5 text-ink-faint">{{ $kosong }}</span>
            @endif
        </div>
    </dd>
</div>
