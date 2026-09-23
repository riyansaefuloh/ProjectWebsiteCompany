@props([
    'model',                       // nama properti Livewire yang menerima berkasnya
    'id',                          // id kotak isian, untuk <label for> pembaca layar
    'accept'   => 'image
@endphp

<label title="{{ $judul ?? $label }}"
       {{ $attributes->class([
           'relative flex aspect-square cursor-pointer items-center justify-center
            rounded-control border-2 border-dashed border-line-strong
            bg-mist/40 text-ink-faint transition-colors
            hover:border-brand hover:bg-brand-wash hover:text-brand
            focus-within:border-brand focus-within:text-brand',
       ]) }}>

    <input type="file" wire:model="{{ $model }}" id="{{ $id }}"
           @if($multiple) multiple @endif
           accept="{{ $accept }}"
           aria-label="{{ $judul ?? $label }}"
           class="sr-only">

    <span class="flex flex-col items-center gap-3 px-2 text-center">

        <span class="flex h-10 w-10 items-center justify-center rounded-full
                     border border-dashed border-current">
            
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round"/>
            </svg>
        </span>

        <span class="text-admin-caption font-semibold">{{ $label }}</span>
    </span>

    <span wire:loading wire:target="{{ $model }}"
          class="absolute inset-0 rounded-control bg-mist/85">
        <span class="flex h-full w-full items-center justify-center text-brand">
            <svg class="h-7 w-7 animate-spin" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.3"/>
                <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                      stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </span>
    </span>
</label>
