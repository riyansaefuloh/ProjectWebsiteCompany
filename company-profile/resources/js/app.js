import './bootstrap'
import daftarkanEditor from './editor'

/*
 * Alpine dibawa oleh bundel Livewire, bukan dipasang sendiri di sini —
 * memasang Alpine kedua membuat dua contoh berebut DOM yang sama.
 *
 * Komponen tambahan didaftarkan lewat peristiwa 'alpine:init', satu-satunya
 * saat Alpine sudah ada tapi belum mulai memindai halaman.
 */
document.addEventListener('alpine:init', () => {
    daftarkanEditor(window.Alpine)
})
