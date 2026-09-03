@php
    /*
     * Kotak centang panel admin.
     *
     * Kotak centang bawaan tidak bisa ditata isinya. Yang bisa diubah cuma
     * ronanya lewat accent-color; bentuk kotaknya, tebal centangnya, dan
     * lengkung sudutnya digambar sistem operasi — jadi ia tampak berbeda di
     * Windows, macOS, dan Android, dan di ketiganya tampak berbeda dari sisa
     * panel ini. Alasan yang sama melahirkan x-admin.select.
     *
     * Maka kotaknya digambar sendiri, tapi kotak centang SUNGGUHAN tetap ada
     * di baliknya — cuma disembunyikan lewat sr-only, bukan display:none.
     * Dengan begitu ia tetap bisa dijangkau papan tik, tetap diumumkan pembaca
     * layar sebagai kotak centang, tetap ikut terkirim saat formulirnya
     * disimpan, dan wire:model tetap bekerja apa adanya.
     *
     * Seluruh rupanya digerakkan peer-checked di CSS, bukan oleh nilai di sisi
     * PHP: wire:model bisa bersifat tunda, dan nilai di server baru menyusul
     * pada permintaan berikutnya.
     *
     * rounded (4px), bukan rounded-control (8px): pada kotak 18px, lengkung
     * delapan piksel hampir membuatnya bundar — dan kotak centang yang bundar
     * terbaca sebagai tombol radio, yang artinya "pilih SATU saja".
     */
@endphp

<span class="relative inline-flex shrink-0 items-center">
    <input type="checkbox" {{ $attributes->class(['peer sr-only']) }}>

    <span class="flex h-[18px] w-[18px] items-center justify-center rounded border
                 border-line-strong bg-canvas text-transparent transition-colors
                 peer-checked:border-brand peer-checked:bg-brand peer-checked:text-white
                 peer-focus-visible:ring-2 peer-focus-visible:ring-brand/30
                 peer-focus-visible:ring-offset-1"
          aria-hidden="true">
        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none">
            <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</span>
