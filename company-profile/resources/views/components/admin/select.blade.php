@props([
    'model',                  // nama properti Livewire yang disetel
    'value'       => '',      // nilainya sekarang, untuk gambar pertama
    'options'     => [],      // [['nilai' => ..., 'label' => ...], ...]
    'placeholder' => 'Semua',
    'label'       => null,    // nama yang dibacakan pembaca layar
    'nullable'    => true,    // sertakan pilihan kosong di puncak daftar?

    /*
     * Baris "tambah baru" di kaki daftarnya — pilihan, tidak menyala kecuali
     * diminta. Kalau diisi nama metode Livewire, kaki daftarnya menampilkan
     * satu baris yang menukar isi menunya jadi kotak ketik; namanya dikirim
     * ke metode itu, dan metodenya yang memutuskan apa yang dibuat serta
     * langsung memilihkannya.
     */
    'aksiTambah'     => null,
    'labelTambah'    => 'Tambah baru',
    'petunjukTambah' => 'Nama baru…',
])

@php
    /*
     * Menu pilih untuk panel admin.
     *
     * <select> bawaan tidak bisa ditata isinya. Daftar yang terbuka digambar
     * oleh sistem operasi, bukan oleh halaman: hurufnya bukan huruf panel,
     * barisnya bukan tinggi baris panel, dan yang terpilih ditandai biru
     * bawaan Windows — satu-satunya benda di seluruh panel ini yang tidak
     * mengikuti paletnya.
     *
     * Maka daftarnya dibangun sendiri. Yang ditukar cuma tampilannya;
     * nilainya tetap mengalir ke properti Livewire yang sama.
     *
     * Nilainya dikirim lewat $wire.$set, BUKAN lewat $wire.prop = nilai.
     * Penugasan biasa hanya menyentuh salinan yang ada di peramban — ia
     * mengubah tulisan di tombolnya, tapi tidak pernah sampai ke server,
     * sehingga tabelnya tidak ikut menyaring. Kesalahan itu pernah terjadi
     * di proyek ini dan tidak terlihat sama sekali dari luar.
     */
    /*
     * Pilihan kosong di puncak daftar hanya masuk akal kalau kekosongan itu
     * memang sebuah jawaban — "Semua status" di bilah penyaring, "Belum
     * ditugaskan" di kolom sales. Untuk status inquiry ia justru merusak:
     * tiap inquiry pasti punya status, jadi baris kosong itu akan tampil
     * sebagai "Baru" yang kedua di daftar yang sama.
     */
    $daftar = collect($options)
        ->map(fn ($o) => ['nilai' => (string) $o['nilai'], 'label' => (string) $o['label']]);

    if ($nullable) {
        $daftar = $daftar->prepend(['nilai' => '', 'label' => $placeholder]);
    }

    $daftar = $daftar->values()->all();

    $sekarang = (string) $value;

    // Label yang tergambar sebelum Alpine hidup. Tanpa ini tombolnya kosong
    // sekejap di tiap perpindahan halaman.
    $labelAwal = collect($daftar)->firstWhere('nilai', $sekarang)['label'] ?? $placeholder;
@endphp

{{-- $attributes->class([...]), BUKAN class="relative" ditambah {{ $attributes }}.
     Yang kedua menggambar DUA atribut class di satu elemen, dan peramban hanya
     membaca yang pertama — jadi setiap kelas yang dikirim pemakainya (mis.
     "mt-2" untuk jarak ke judulnya) diam-diam dibuang. Itu yang membuat jarak
     di atas menu pilih tidak pernah sama dengan kolom isian lain. --}}
{{-- wire:key yang ikut berubah saat daftar pilihannya berubah.

     x-data cuma dibaca sekali, saat elemennya lahir; morph DOM Livewire tidak
     melahirkannya ulang. Jadi tanpa kunci ini, kategori yang baru saja dibuat
     lewat baris "tambah baru" tersimpan di server tapi TIDAK pernah muncul di
     daftarnya — menunya tetap memegang salinan lama. Kuncinya berubah hanya
     kalau pilihannya berubah, jadi biasanya ia diam saja. --}}
<div wire:key="pilih-{{ $model }}-{{ substr(md5(json_encode($daftar)), 0, 8) }}"
     {{ $attributes->class(['relative']) }}
     x-data="{
         buka: false,
         sorot: 0,
         daftar: @js($daftar),

         @if($aksiTambah)
             /* Keadaan baris 'tambah baru'. */
             tambah: false,
             teksBaru: '',
             menyimpan: false,
         @else
             /* Menu ini tidak punya baris tambah, jadi keadaannya pun tidak
                dibawa: bukaMenu() di bawah tetap menyetel `tambah` supaya
                cabangnya satu, dan properti yang tidak dideklarasikan di sini
                cukup diabaikan Alpine. */
             tambah: false,
         @endif

         get nilai() { return String($wire.{{ $model }} ?? '') },

         get terpilih() {
             return this.daftar.find(p => p.nilai === this.nilai) ?? this.daftar[0]
         },

         pilih(p) {
             $wire.$set('{{ $model }}', p.nilai)
             this.tutup()
         },

         bukaMenu() {
             this.buka   = true
             this.tambah = false
             this.sorot  = Math.max(0, this.daftar.findIndex(p => p.nilai === this.nilai))
             this.hitungLetak()
             this.$nextTick(() => this.keBaris())
         },

         @if($aksiTambah)
             mulaiTambah() {
                 this.tambah   = true
                 this.teksBaru = ''
                 this.hitungLetak()
                 this.$nextTick(() => this.$refs.isian?.focus())
             },

             batalTambah() {
                 this.tambah = false
                 this.hitungLetak()
                 this.$nextTick(() => this.$refs.tombol.focus())
             },

             /*
              * Namanya dikirim ke metode Livewire, lalu menunya ditutup.
              *
              * Daftarnya TIDAK disusun sendiri di sini: server yang membuat
              * barisnya sekaligus memilihkannya, dan wire:key di atas yang
              * melahirkan ulang menu ini dengan daftar yang sudah berisi.
              * Menambah salinan di sisi peramban cuma membuat dua sumber
              * kebenaran yang gampang berselisih.
              */
             async simpanBaru() {
                 const nama = this.teksBaru.trim()
                 if (! nama || this.menyimpan) return

                 this.menyimpan = true
                 try {
                     await $wire.call('{{ $aksiTambah }}', nama)
                 } finally {
                     this.menyimpan = false
                     this.tambah    = false
                     this.buka      = false
                 }
             },
         @endif

         /*
          * Letak daftarnya dihitung sendiri, dan daftarnya dipasang dengan
          * position: fixed.
          *
          * Alasannya bukan kerapian. Daftar yang ditempatkan secara absolut
          * tetap tunduk pada induk mana pun yang punya overflow — dan begitu
          * menu pilih ini dipakai di dalam kolom yang bisa digulung (kolom
          * kanan modal, misalnya), daftarnya terpotong di tepi kolom itu.
          * fixed melepaskannya dari semua induk sekaligus.
          *
          * Sekalian: kalau ruang di bawah tombolnya tidak cukup, daftarnya
          * dibalik ke atas. Tanpa itu, menu di dekat kaki layar cuma
          * menampilkan satu-dua baris pertamanya.
          */
         letak: { kiri: 0, lebar: 0, atas: null, bawah: null },

         hitungLetak() {
             const t = this.$refs.tombol.getBoundingClientRect()

             /* 34px per baris + bantalan; dibatasi setinggi kotaknya (264px).
                Baris tambah-baru di kaki daftarnya ikut dihitung — kalau tidak,
                menu di dekat kaki layar membalik ke atas terlambat dan baris
                itu justru yang terpotong.

                Catatan: JANGAN pakai tanda kutip ganda di mana pun dalam blok
                ini, komentar sekalipun. Seluruh x-data ini satu atribut HTML
                yang dibatasi kutip ganda; satu saja di dalamnya menutup
                atributnya di tengah jalan, sisanya terbaca sebagai atribut
                sampah, dan Alpine melempar SyntaxError di tiap menu pilih. */
             const kaki   = {{ $aksiTambah ? 42 : 0 }}
             const tinggi = Math.min(264 + kaki, this.daftar.length * 34 + 12 + kaki)
             const bawah  = window.innerHeight - t.bottom - 12
             const keAtas = bawah < tinggi && t.top > bawah

             this.letak = {
                 kiri:  Math.round(t.left),
                 lebar: Math.round(t.width),
                 atas:  keAtas ? null : Math.round(t.bottom + 6),
                 bawah: keAtas ? Math.round(window.innerHeight - t.top + 6) : null,
             }
         },

         get gaya() {
             const l = this.letak

             return `left:${l.kiri}px; width:${l.lebar}px; `
                  + (l.atas === null ? `bottom:${l.bawah}px;` : `top:${l.atas}px;`)
         },

         tutup() {
             this.buka = false
             this.$refs.tombol.focus()
         },

         geser(n) {
             this.sorot = (this.sorot + n + this.daftar.length) % this.daftar.length
             this.keBaris()
         },

         /* Baris yang sedang disorot digulung ke dalam pandangan — daftar
            produk lebih panjang dari kotaknya, dan tanpa ini panah bawah
            menyorot baris yang tidak terlihat. */
         keBaris() {
             this.$refs.menu?.children[this.sorot]?.scrollIntoView({ block: 'nearest' })
         },
     }"
     x-on:keydown.escape.stop="buka && tutup()"
     x-on:click.outside="buka = false"
     {{-- Daftarnya melayang di titik yang dihitung saat dibuka, jadi begitu
          apa pun bergulir ia tidak lagi berada di tempat yang benar. Ditutup
          saja — kecuali gulungan yang datang dari dalam daftarnya sendiri,
          yang justru kita picu sendiri lewat keBaris(). --}}
     x-on:scroll.window.capture="buka && ! $refs.menu?.contains($event.target) && (buka = false)"
     x-on:resize.window="buka = false">

    <button type="button" x-ref="tombol"
            x-on:click="buka ? buka = false : bukaMenu()"
            x-on:keydown.down.prevent="buka ? geser(1) : bukaMenu()"
            x-on:keydown.up.prevent="buka ? geser(-1) : bukaMenu()"
            x-on:keydown.home.prevent="buka && (sorot = 0, keBaris())"
            x-on:keydown.end.prevent="buka && (sorot = daftar.length - 1, keBaris())"
            x-on:keydown.enter.prevent="buka ? pilih(daftar[sorot]) : bukaMenu()"
            x-bind:aria-expanded="buka ? 'true' : 'false'"
            aria-haspopup="listbox"
            @if($label) aria-label="{{ $label }}" @endif
            class="admin-control admin-control-button"
            x-bind:class="buka && '!border-brand'">

        <span class="min-w-0 truncate"
              x-text="terpilih.label"
              x-bind:class="nilai ? 'text-ink' : 'text-ink-muted'"
              @class(['text-ink' => $sekarang !== '', 'text-ink-muted' => $sekarang === ''])>{{ $labelAwal }}</span>

        <svg class="h-3 w-3 shrink-0 text-ink-faint transition-transform duration-150"
             x-bind:class="buka && 'rotate-180'" viewBox="0 0 12 12" fill="none" aria-hidden="true">
            <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div x-show="buka" x-cloak x-ref="menu" role="listbox"
         x-bind:style="gaya"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         @class([
             'admin-scroll fixed z-[120] max-h-[264px] overflow-y-auto overscroll-contain
              rounded-corner border border-line bg-canvas py-1.5
              shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]',
             /* Kaki daftarnya menempel, jadi barisnya tetap terjangkau di
                daftar yang panjang; py-1.5 dipindah ke isinya supaya kakinya
                tidak ikut menggantung di atas bantalan bawah. */
             '!py-0 pt-1.5' => (bool) $aksiTambah,
         ])>

        <template x-for="(p, i) in daftar" x-bind:key="p.nilai">
            <button type="button" role="option"
                    x-on:click="pilih(p)"
                    x-on:mousemove="sorot = i"
                    x-bind:aria-selected="p.nilai === nilai ? 'true' : 'false'"
                    x-bind:class="{
                        'bg-mist': sorot === i,
                        'font-semibold text-ink': p.nilai === nilai,
                        'text-ink-muted': p.nilai !== nilai,
                    }"
                    class="flex w-full items-center justify-between gap-2 px-3.5 py-2
                           text-left text-[13px] transition-colors">

                <span class="min-w-0 truncate" x-text="p.label"></span>

                {{-- Centang, bukan sekadar huruf tebal: di daftar panjang
                     yang isinya mirip-mirip, tebal huruf saja tidak cukup
                     untuk menjawab "yang mana yang sedang aktif". --}}
                <svg x-show="p.nilai === nilai" class="h-3.5 w-3.5 shrink-0 text-brand"
                     viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </template>

        @if($aksiTambah)
            <div class="sticky bottom-0 border-t border-line bg-canvas">

                {{-- Keadaan 1: barisnya masih tombol. --}}
                <button type="button" x-show="! tambah" x-on:click="mulaiTambah()"
                        class="flex w-full items-center gap-2 px-3.5 py-2.5 text-left
                               text-[13px] font-semibold text-brand transition-colors hover:bg-brand-wash">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M8 3.4v9.2M3.4 8h9.2" stroke="currentColor"
                              stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    {{ $labelTambah }}
                </button>

                {{-- Keadaan 2: barisnya jadi kotak ketik.

                     .prevent.stop di Enter — tanpa keduanya, tombol Enter di
                     sini ikut mengirim <form> modal yang membungkusnya, dan
                     yang terjadi bukan "kategori tersimpan" melainkan
                     "artikelnya tersimpan setengah jadi". --}}
                <div x-show="tambah" x-cloak class="flex items-center gap-1.5 p-1.5">
                    <input type="text" x-ref="isian" x-model="teksBaru"
                           placeholder="{{ $petunjukTambah }}"
                           aria-label="{{ $labelTambah }}"
                           maxlength="100"
                           x-on:keydown.enter.prevent.stop="simpanBaru()"
                           x-on:keydown.escape.prevent.stop="batalTambah()"
                           class="admin-control min-w-0 flex-1 !py-1.5 text-[13px]">

                    <button type="button" x-on:click="simpanBaru()"
                            x-bind:disabled="! teksBaru.trim() || menyimpan"
                            aria-label="Simpan {{ mb_strtolower($labelTambah) }}"
                            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-control
                                   bg-brand text-white transition-colors hover:bg-brand-deep
                                   disabled:cursor-not-allowed disabled:opacity-40">
                        <svg x-show="! menyimpan" class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="1.8"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg x-show="menyimpan" x-cloak class="h-3.5 w-3.5 animate-spin"
                             viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6" opacity="0.35"/>
                            <path d="M14 8a6 6 0 0 0-6-6" stroke="currentColor"
                                  stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </button>

                    <button type="button" x-on:click="batalTambah()"
                            aria-label="Batal menambah"
                            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-control
                                   border border-line text-ink-faint transition-colors
                                   hover:border-line-strong hover:text-ink">
                        <x-icon.admin name="close" size="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
