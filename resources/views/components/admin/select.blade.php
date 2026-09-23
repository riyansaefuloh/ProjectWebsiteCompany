@props([
    'model',                  // nama properti Livewire yang disetel
    'value'       => '',      // nilainya sekarang, untuk gambar pertama
    'options'     => [],      // [['nilai' => ..., 'label' => ...], ...]
    'placeholder' => 'Semua',
    'label'       => null,    // nama yang dibacakan pembaca layar
    'nullable'    => true,    // sertakan pilihan kosong di puncak daftar?

    'aksiTambah'     => null,
    'labelTambah'    => 'Tambah baru',
    'petunjukTambah' => 'Nama baru…',

    'aksiHapus'  => null,
    'labelHapus' => 'Hapus',
])

@php

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
             
             tambah: false,
         @endif

         @if($aksiHapus)
             
             hapusId: null,
             menghapus: false,
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
             
             @if($aksiHapus) this.hapusId = null; @endif
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

         @if($aksiHapus)
             
             async hapusPilihan(p) {
                 if (this.menghapus) return

                 this.menghapus = true
                 try {
                     await $wire.call('{{ $aksiHapus }}', p.nilai)
                 } finally {
                     this.menghapus = false
                     this.hapusId   = null
                     this.buka      = false
                 }
             },
         @endif

         letak: { kiri: 0, lebar: 0, atas: null, bawah: null },

         hitungLetak() {
             const t = this.$refs.tombol.getBoundingClientRect()

             const kaki   = {{ $aksiTambah ? 42 : 0 }}
             const tinggi = Math.min(264 + kaki, this.daftar.length * 40 + 12 + kaki)
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

         keBaris() {
             this.$refs.baris?.children[this.sorot]?.scrollIntoView({ block: 'nearest' })
         },
     }"
     x-on:keydown.escape.stop="buka && tutup()"
     x-on:click.outside="buka = false"
     
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

        <span class="min-w-0 truncate" x-text="terpilih.label">{{ $labelAwal }}</span>

        <svg class="h-3 w-3 shrink-0 text-ink-faint transition-transform duration-150"
             x-bind:class="buka && 'rotate-180'" viewBox="0 0 12 12" fill="none" aria-hidden="true">
            <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div x-show="buka" x-cloak
         x-bind:style="gaya"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed z-[120] flex flex-col overflow-hidden rounded-corner border border-line
                bg-canvas shadow-[0_18px_44px_-18px_rgba(26,29,27,0.32)]">

    <div x-ref="baris" role="listbox"
         class="admin-scroll min-h-0 max-h-[264px] flex-1 overflow-y-auto overscroll-contain px-1.5 py-1.5">
        <template x-for="(p, i) in daftar" x-bind:key="p.nilai">
            
            <div class="group/baris relative">

                <button type="button" role="option"
                        @if($aksiHapus) x-show="hapusId !== p.nilai" @endif
                        x-on:click="pilih(p)"
                        x-on:mousemove="sorot = i"
                        x-bind:aria-selected="p.nilai === nilai ? 'true' : 'false'"
                        x-bind:class="{
                            'bg-mist text-brand-deep': sorot === i,
                            'font-semibold text-ink': p.nilai === nilai,
                        }"
                        @class([
                            'admin-menu-row w-full justify-between rounded-control hover:text-brand-deep',
                            
                            'pr-9' => (bool) $aksiHapus,
                        ])>

                    <span class="min-w-0 truncate" x-text="p.label"></span>

                    <svg x-show="p.nilai === nilai" class="h-3.5 w-3.5 shrink-0 text-brand"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="m3.6 8.4 2.8 2.8 6-6" stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                @if($aksiHapus)
                    
                    <button type="button" x-show="p.nilai !== '' && hapusId !== p.nilai"
                            x-on:click.stop="hapusId = p.nilai"
                            x-bind:aria-label="'{{ $labelHapus }} ' + p.label"
                            class="absolute right-1.5 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center
                                   justify-center rounded-control text-ink-faint opacity-0 transition
                                   hover:bg-danger/10 hover:text-danger focus-visible:opacity-100
                                   group-hover/baris:opacity-100">
                        <x-icon.admin name="trash" size="h-3.5 w-3.5" />
                    </button>

                    <div x-show="hapusId === p.nilai" x-cloak
                         class="flex items-center gap-1.5 rounded-control bg-danger/5 px-2.5 py-1.5">
                        <span class="min-w-0 flex-1 truncate text-admin-caption text-ink-muted">
                            Hapus <span class="font-semibold text-ink" x-text="p.label"></span>?
                        </span>

                        <button type="button" x-on:click.stop="hapusPilihan(p)"
                                x-bind:disabled="menghapus"
                                x-bind:aria-label="'Ya, hapus ' + p.label"
                                class="inline-flex h-6 shrink-0 items-center rounded-control bg-danger px-2
                                       text-admin-caption font-semibold text-white transition-colors
                                       hover:bg-danger/90 disabled:opacity-50">
                            Ya
                        </button>

                        <button type="button" x-on:click.stop="hapusId = null"
                                aria-label="Batal menghapus"
                                class="inline-flex h-6 shrink-0 items-center rounded-control border border-line
                                       px-2 text-admin-caption font-semibold text-ink-muted
                                       transition-colors hover:text-ink">
                            Batal
                        </button>
                    </div>
                @endif
            </div>
        </template>
    </div>

        @if($aksiTambah)
            <div class="shrink-0 border-t border-line bg-canvas">

                {{-- Keadaan 1: barisnya masih tombol. --}}
                <button type="button" x-show="! tambah" x-on:click="mulaiTambah()"
                        class="flex w-full items-center gap-2 px-3.5 py-2.5 text-left
                               text-admin-body font-semibold text-brand transition-colors hover:bg-brand-wash">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M8 3.4v9.2M3.4 8h9.2" stroke="currentColor"
                              stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    {{ $labelTambah }}
                </button>

                <div x-show="tambah" x-cloak class="flex items-center gap-1.5 p-1.5">
                    <input type="text" x-ref="isian" x-model="teksBaru"
                           placeholder="{{ $petunjukTambah }}"
                           aria-label="{{ $labelTambah }}"
                           maxlength="100"
                           x-on:keydown.enter.prevent.stop="simpanBaru()"
                           x-on:keydown.escape.prevent.stop="batalTambah()"
                           class="admin-control min-w-0 flex-1 !py-1.5 text-admin-body">

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
