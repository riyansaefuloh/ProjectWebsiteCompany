@props([
    'model',                 // nama properti Livewire, mis. 'content_en'
    'value' => '',           // isi HTML yang sedang tersimpan
    'label' => null,         // untuk pembaca layar
    'placeholder' => 'Tulis isi artikel…',
    'kunci' => null,         // pembeda contoh, biasanya id data yang dibuka
    'tinggi' => 'min-h-[320px]',
])

{{--
    Editor teks kaya.

    wire:ignore WAJIB. Quill membangun DOM-nya sendiri di dalam wadah ini;
    tanpa wire:ignore, tiap pembaruan Livewire akan menimpanya dan editornya
    hancur di tengah pengetikan.

    wire:key harus BERUBAH tiap kali data yang dibuka berganti. x-data cuma
    dinilai sekali seumur elemen — tanpa kunci yang berubah, membuka artikel
    kedua akan memakai kembali editor milik artikel pertama, lengkap dengan
    isinya, dan menyimpannya ke artikel yang salah.
--}}
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
