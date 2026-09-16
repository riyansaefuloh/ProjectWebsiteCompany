import daftarkanEditor from './editor'

/* Alpine dibawa oleh bundel Livewire — jangan memasangnya sendiri, dua contoh
   akan berebut DOM yang sama. 'alpine:init' satu-satunya saat Alpine sudah ada
   tapi belum memindai halaman. */
document.addEventListener('alpine:init', () => {
    daftarkanEditor(window.Alpine)
})
