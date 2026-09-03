@props([
    'model',                 // nama properti Livewire, mis. 'content_en'
    'value' => '',           // isi HTML yang sedang tersimpan
    'label' => null,         // untuk pembaca layar
    'placeholder' => 'Tulis isi artikel…',
    'kunci' => null,         // pembeda contoh, biasanya id data yang dibuka
    'tinggi' => 'min-h-[320px]',
])

{{-- Editor teks kaya. wire:ignore WAJIB — Quill membangun DOM-nya sendiri di
     dalam wadah ini, dan tanpa itu tiap pembaruan Livewire menimpanya di
     tengah pengetikan. wire:key harus ikut BERUBAH; lihat
     resources/js/editor.js. --}}
<div wire:ignore
     wire:key="editor-{{ $model }}-{{ $kunci ?? 'baru' }}"
     x-data="editorKaya({
         prop: @js($model),
         isi: @js($value),
         placeholder: @js($placeholder)
     })"
     {{ $attributes->class(['admin-editor']) }}>

    <div x-ref="kanvas" class="{{ $tinggi }}" @if($label) aria-label="{{ $label }}" @endif></div>
</div>
