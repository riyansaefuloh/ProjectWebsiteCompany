/* ==========================================================================
   PENYUNTING TEKS KAYA (Quill)

   Didaftarkan sebagai komponen Alpine, bukan dimuat lewat tag skrip: Livewire
   menyisipkan skrip lewat pembaruan DOM, dan skrip yang disisipkan begitu tidak
   pernah dijalankan peramban.

   Tiga syarat di bladenya supaya ia bertahan di dalam modal Livewire:
     1. wire:ignore pada wadahnya — tanpa itu pembaruan Livewire menimpa DOM
        buatan Quill dan editornya hancur di tengah pengetikan.
     2. Isi dikirim lewat $wire.$set(), bukan wire:model — Quill menulis ke
        <div contenteditable>, yang tidak bisa diikat wire:model.
     3. wire:key yang BERUBAH pada wadahnya — x-data cuma dinilai sekali, jadi
        tanpa itu artikel kedua memakai kembali contoh Quill milik yang pertama.

   Tema Quill diimpor dari resources/css/app.css, bukan dari sini.
   ========================================================================== */

const TOOLBAR = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
]

/* Daftar putih format. Tanpa ini Quill menerima seluruh format bawaannya, dan
   teks yang DITEMPEL dari tempat lain membawa masuk warna huruf serta latar
   yang terpaku — tidak ikut berubah saat tema situs diganti. */
const FORMATS = [
    'header',
    'bold', 'italic', 'underline',
    'list',
    'blockquote', 'link',
]

export default function daftarkanEditor(Alpine) {
    Alpine.data('editorKaya', (config = {}) => ({
        quill: null,

        async init() {
            /* import() dinamis, BUKAN impor di puncak berkas: sebagai impor
               biasa Quill memakan 81% bundel panel dan ikut diunduh di ketiga
               belas halaman, padahal hanya dua yang punya editor. */
            const { default: Quill } = await import('quill')

            const wadah = this.$refs.kanvas

            this.quill = new Quill(wadah, {
                theme: 'snow',
                placeholder: config.placeholder || 'Tulis isi artikel…',
                formats: FORMATS,
                modules: { toolbar: TOOLBAR },

                /* Dijepit ke wadahnya sendiri; bawaan Quill adalah
                   document.body, dan kotak alamat tautan bisa mendarat di luar
                   kolom modal yang bisa digulung. */
                bounds: this.$el,
            })

            /* URUTAN DI BAWAH TIDAK BOLEH DIBALIK: pendengar didaftarkan LEBIH
               DULU, isi awal dimuat paling akhir.

               Versi lama memuat isi duluan dengan dangerouslyPasteHTML(), yang
               melempar galat pada editor yang belum pernah mendapat fokus —
               misalnya di tab bahasa yang tersembunyi. Galat itu menghentikan
               init() sebelum pendengarnya terpasang: editor tampak hidup dan
               bisa diketik, tapi tidak satu huruf pun sampai ke server. */

            /* Argumen ketiga $set bernilai false — simpan tanpa menggambar
               ulang. Kalau true, tiap ketukan memicu pembaruan DOM dan kursor
               melompat ke awal. */
            let jeda = null

            this.quill.on('text-change', (delta, oldDelta, sumber) => {
                if (sumber !== 'user') {
                    return
                }

                clearTimeout(jeda)
                jeda = setTimeout(() => this.$wire.$set(config.prop, this.isiHtml(), false), 400)
            })

            /* Sekali lagi saat kehilangan fokus, tanpa tunda: menekan Simpan
               langsung setelah mengetik bisa mendahului tunda 400 ms di atas. */
            this.quill.root.addEventListener('blur', () => {
                clearTimeout(jeda)
                this.$wire.$set(config.prop, this.isiHtml(), false)
            })

            /* setContents(), bukan dangerouslyPasteHTML() — ia tidak menyentuh
               kursor, jadi aman untuk editor yang sedang tersembunyi. 'silent'
               supaya pemuatan ini tidak terbaca sebagai perubahan pemakai dan
               balik menimpa properti Livewire. */
            const awal = config.isi || ''

            if (awal.trim() !== '') {
                this.quill.setContents(this.quill.clipboard.convert({ html: awal }), 'silent')
            }

            /* Perubahan dari server (misal tombol Auto Translate) harus dipantau
               manual: wadahnya memakai wire:ignore, jadi Livewire tidak pernah
               menimpa isinya sendiri. */
            this.$watch('$wire.' + config.prop, (value) => {
                const html = value || ''
                if (html !== this.isiHtml()) {
                    this.quill.setContents(this.quill.clipboard.convert({ html: html }), 'silent')
                }
            })
        },

        /* Editor kosong tetap menghasilkan '<p><br></p>'. Tanpa dinormalkan,
           aturan 'required' menganggapnya terisi dan artikel kosong lolos. */
        isiHtml() {
            const html = this.quill.root.innerHTML

            return this.quill.getText().trim() === '' ? '' : html
        },

        destroy() {
            this.quill = null
        },
    }))
}
