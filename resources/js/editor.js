/* Editor teks kaya */

const TOOLBAR = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
]

/* Format editor */
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
            /* Muat editor */
            const { default: Quill } = await import('quill')

            const wadah = this.$refs.kanvas

            this.quill = new Quill(wadah, {
                theme: 'snow',
                placeholder: config.placeholder || 'Tulis isi artikel…',
                formats: FORMATS,
                modules: { toolbar: TOOLBAR },

                /* Wadah editor */
                bounds: this.$el,
            })

            /* Sinkronisasi konten */
            let jeda = null

            this.quill.on('text-change', (delta, oldDelta, sumber) => {
                if (sumber !== 'user') {
                    return
                }

                clearTimeout(jeda)
                jeda = setTimeout(() => this.$wire.$set(config.prop, this.isiHtml(), false), 400)
            })

            /* Simpan saat kehilangan fokus */
            this.quill.root.addEventListener('blur', () => {
                clearTimeout(jeda)
                this.$wire.$set(config.prop, this.isiHtml(), false)
            })

            /* Isi awal */
            const awal = config.isi || ''

            if (awal.trim() !== '') {
                this.quill.setContents(this.quill.clipboard.convert({ html: awal }), 'silent')
            }

            /* Perubahan dari server */
            this.$watch('$wire.' + config.prop, (value) => {
                const html = value || ''
                if (html !== this.isiHtml()) {
                    this.quill.setContents(this.quill.clipboard.convert({ html: html }), 'silent')
                }
            })
        },

        /* Normalisasi konten */
        isiHtml() {
            const html = this.quill.root.innerHTML

            return this.quill.getText().trim() === '' ? '' : html
        },

        destroy() {
            this.quill = null
        },
    }))
}
