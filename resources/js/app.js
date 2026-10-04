import daftarkanEditor from './editor'

/* Registrasi Alpine */
document.addEventListener('alpine:init', () => {
    daftarkanEditor(window.Alpine)
})
