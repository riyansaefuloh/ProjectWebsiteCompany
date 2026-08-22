import Quill from 'quill'

// Tema Quill diimpor dari resources/css/app.css, bukan dari sini — lihat
// keterangannya di sana.

/*
 * Editor teks kaya untuk konten berita dan halaman.
 *
 * Sebelumnya konten diketik sebagai HTML mentah di dalam <textarea> — penulis
 * artikel harus mengetik sendiri tag paragraf dan daftarnya. Pernah dicoba
 * memuat TinyMCE lewat tag skrip di dalam modal, tapi tidak pernah jalan:
 * Livewire menyisipkan skrip lewat pembaruan DOM, dan skrip yang disisipkan
 * begitu TIDAK PERNAH dijalankan peramban.
 *
 * Karena itu editornya dibundel lewat Vite dan didaftarkan sebagai komponen
 * Alpine di sini — satu berkas yang sudah termuat sejak awal halaman, jadi
 * tidak ada skrip yang perlu disisipkan belakangan.
 *
 * Tiga hal yang membuatnya bertahan di dalam modal Livewire:
 *
 * 1. wire:ignore pada wadahnya. Tanpa itu, tiap pembaruan Livewire akan
 *    menimpa DOM buatan Quill dan editornya hancur di tengah pengetikan.
 *
 * 2. Isinya dikirim balik lewat $wire.$set(), bukan wire:model. Quill menulis
 *    ke <div contenteditable>, bukan ke kolom borang, jadi tidak ada yang bisa
 *    diikat wire:model.
 *
 * 3. wire:key yang berubah pada wadahnya (lihat bladenya). x-data cuma dinilai
 *    sekali; tanpa kunci yang berubah, membuka artikel kedua akan memakai
 *    kembali contoh Quill milik artikel pertama beserta isinya.
 */

const TOOLBAR = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
]

/*
 * Format yang BOLEH ada di dalam isi — persis sebanyak yang ditawarkan bilah
 * alat di atas, tidak lebih.
 *
 * Tanpa daftar ini Quill menerima seluruh format bawaannya, termasuk warna
 * huruf dan warna latar. Bilah alatnya memang tidak menawarkan keduanya, tapi
 * MENEMPEL teks dari tempat lain tetap membawanya masuk — dan isi artikel jadi
 * memuat baris seperti
 *
 *     <span style="color: rgb(84, 91, 88); background-color: rgb(255,255,255)">
 *
 * Warna yang terpaku begitu tidak ikut berubah kalau tema situs diganti, dan
 * latar putihnya menimpa latar bagian yang berwarna. Yang ditempel sekarang
 * dilucuti sampai tinggal tebal, miring, garis bawah, tautan, judul, dan
 * daftar — sisanya jadi teks biasa.
 */
const FORMATS = [
    'header',
    'bold', 'italic', 'underline',
    'list',
    'blockquote', 'link',
]

export default function daftarkanEditor(Alpine) {
    Alpine.data('editorKaya', (config = {}) => ({
        quill: null,

        init() {
            const wadah = this.$refs.kanvas

            this.quill = new Quill(wadah, {
                theme: 'snow',
                placeholder: config.placeholder || 'Tulis isi artikel…',
                formats: FORMATS,
                modules: { toolbar: TOOLBAR },
            })

            /*
             * PENDENGARNYA DIDAFTARKAN LEBIH DULU, sebelum isi awal dimuat.
             *
             * Urutan ini bukan gaya penulisan, tapi perbaikan atas cacat nyata:
             * versi sebelumnya memuat isi lebih dulu dengan
             * clipboard.dangerouslyPasteHTML(), dan panggilan itu MELEMPAR
             * galat ("Cannot read properties of null (reading 'offset')")
             * karena ia menyetel posisi kursor pada editor yang belum pernah
             * mendapat fokus — misalnya editor di tab bahasa yang tersembunyi.
             *
             * Galat itu menghentikan init() di tengah jalan, jadi baris
             * pendaftaran pendengar di bawah tidak pernah tercapai. Hasilnya
             * editor tampak hidup dan bisa diketik, tapi tidak satu pun huruf
             * pernah sampai ke server — isi artikel tersimpan seperti semula
             * seolah tidak ada yang diketik.
             *
             * Sekarang pendengarnya dipasang duluan, dan pemuatan isinya
             * memakai setContents() yang tidak menyentuh kursor sama sekali.
             */

            /*
             * Perubahan dikirim ke server dengan tunda.
             *
             * Argumen ketiga $set bernilai false: menyimpan tanpa menggambar
             * ulang. Kalau true, tiap ketukan tombol akan memicu pembaruan DOM
             * dan kursor melompat ke awal.
             */
            let jeda = null

            this.quill.on('text-change', (delta, oldDelta, sumber) => {
                if (sumber !== 'user') {
                    return
                }

                clearTimeout(jeda)
                jeda = setTimeout(() => this.$wire.$set(config.prop, this.isiHtml(), false), 400)
            })

            /*
             * Sekali lagi saat editornya kehilangan fokus, tanpa tunda.
             *
             * Menekan Simpan langsung setelah mengetik bisa mendahului tunda
             * 400 ms di atas — dan yang tersimpan jadi isi sebelum kalimat
             * terakhir diketik.
             */
            this.quill.root.addEventListener('blur', () => {
                clearTimeout(jeda)
                this.$wire.$set(config.prop, this.isiHtml(), false)
            })

            /*
             * Isi awal dimuat PALING AKHIR, dan dengan 'silent'.
             *
             * setContents() memasang Delta apa adanya tanpa menyentuh kursor,
             * jadi ia aman dipanggil untuk editor yang sedang tersembunyi.
             * clipboard.convert() tetap dipakai untuk mengubah HTML jadi Delta,
             * supaya tag yang tidak dikenal Quill dibuang di sini — bukan
             * lenyap diam-diam pada suntingan pertama.
             *
             * 'silent' supaya pemuatan ini tidak terbaca sebagai perubahan
             * buatan pemakai dan tidak balik menimpa properti Livewire.
             */
            const awal = config.isi || ''

            if (awal.trim() !== '') {
                this.quill.setContents(this.quill.clipboard.convert({ html: awal }), 'silent')
            }

            /*
             * Memantau perubahan dari server (misal: tombol Auto Translate).
             *
             * Karena wadah ini memakai wire:ignore, pembaruan Livewire tidak
             * akan pernah menimpa isinya. Kita harus memantaunya secara manual
             * lewat proxy $wire milik Alpine.
             */
            this.$watch('$wire.' + config.prop, (value) => {
                const html = value || ''
                if (html !== this.isiHtml()) {
                    this.quill.setContents(this.quill.clipboard.convert({ html: html }), 'silent')
                }
            })
        },

        /*
         * Editor kosong tetap menghasilkan '<p><br></p>' — bukan untai kosong.
         * Tanpa dinormalkan, aturan validasi 'required' menganggapnya terisi
         * dan artikel tanpa isi lolos tersimpan.
         */
        isiHtml() {
            const html = this.quill.root.innerHTML

            return this.quill.getText().trim() === '' ? '' : html
        },

        destroy() {
            this.quill = null
        },
    }))
}
