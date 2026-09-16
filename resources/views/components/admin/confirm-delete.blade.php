@props([
    /*
     * Nama aksi Livewire dan kunci barisnya. Komponen ini yang merakit
     * pemanggilannya sendiri jadi $wire.<metode>('<id>').
     *
     * Dipisah dua, bukan satu cuplikan JavaScript utuh. Cuplikan utuh berarti
     * tiap tempat pemakaian harus menulis "$wire.delete('" . $x->id . "')" —
     * sebuah untai PHP yang di dalamnya ada tanda kutip tunggal yang harus
     * di-escape, di dalam atribut Blade yang dibatasi kutip ganda. Susunan
     * itu sudah gagal sekali saat berkas ini dibuat, dan gagalnya berupa
     * galat sintaks PHP yang baru terlihat saat halamannya dibuka.
     */
    'metode' => 'delete',

    /*
     * Boleh kosong. Sebagian aksi hapus tidak menyebut baris mana pun —
     * "hapus gambar kategori ini" di dalam modal misalnya, karena kategorinya
     * sudah jelas dari jendela yang sedang terbuka.
     */
    'id' => null,

    'judul'  => 'Hapus?',
    'tombol' => 'Ya, hapus',

    // Dibacakan pembaca layar dan muncul saat kursor berhenti di tombolnya.
    'label'  => 'Hapus',

    /*
     * Rupa tombol pemicunya. Dibiarkan kosong berarti memakai bentuk baku:
     * petak 32px berbingkai, seperti tombol Aksi di tabel.
     *
     * Diganti — bukan ditambahi — karena sebagian pemicu berdiri di tempat
     * yang aturannya berbeda: tombol hapus gambar duduk DI ATAS gambarnya,
     * jadi ia berlatar putih tembus pandang dan berukuran 26px. Kelas yang
     * ditambahkan ke bentuk baku akan bertabrakan dengan bingkai dan latar
     * bawaannya alih-alih menggantikannya.
     */
    'kelas' => null,
    'ikon'  => 'h-4 w-4',

    /*
     * Nama barang yang akan dihapus. Boleh kosong untuk aksi yang
     * sasarannya sudah jelas dari layar tempat ia dipanggil — 'hapus
     * gambar kategori ini' di dalam modal, misalnya.
     */
    'nama' => null,
])

@php
    /*
     * Kotak konfirmasi hapus.
     *
     * Menggantikan wire:confirm, yang memanggil window.confirm() bawaan
     * peramban. Kotak itu digambar sistem operasi: hurufnya bukan huruf
     * panel, tombolnya berbahasa Inggris atau bahasa sistem, letaknya di
     * puncak layar jauh dari tombol yang ditekan, dan tidak ada satu pun
     * bagiannya yang bisa ditata. Ia juga MENGHENTIKAN seluruh halaman
     * sampai dijawab.
     *
     * Yang paling merugikan: kotak itu tidak bisa membedakan mana yang
     * berbahaya. "Hapus gambar ini?" dan "Hapus produk beserta seluruh
     * terjemahannya?" tampil dengan rupa yang sama persis.
     *
     * Kotak ini memakai anatomi yang sama dengan jendela lain di panel —
     * latar gelap berkabut, panel bersudut lengkung, kaki bergaris — supaya
     * ia terbaca sebagai bagian dari panel, bukan sebagai peringatan sistem.
     */
@endphp

{{-- contents: pembungkus ini tidak boleh ikut jadi kotak flex. Sel Aksi di
     tabel menata anak-anaknya berderet; kalau pembungkus ini menghitung
     dirinya sendiri sebagai satu anak, jarak antar-tombolnya berubah. --}}
<div x-data="{ buka: false }" class="contents">

    <button type="button" x-on:click="buka = true"
            title="{{ $label }}" aria-label="{{ $label }}"
            {{ $attributes->class([$kelas ?: 'inline-flex h-8 w-8 shrink-0 items-center justify-center
                                              rounded-control border border-line bg-canvas text-ink-muted
                                              transition-colors hover:border-danger hover:bg-danger
                                              hover:text-white']) }}>
        <x-icon.admin name="trash" :size="$ikon" />
    </button>

    {{-- template x-if, BUKAN x-show: app.css mengunci gulungan lewat
         html:has(.modal-open), dan :has() memeriksa ada-tidaknya elemen di
         DOM — bukan tampak-tidaknya. --}}
    <template x-if="buka">
        <div class="modal-open fixed inset-0 z-[110] flex items-center justify-center
                    overflow-clip bg-ink/45 p-4 backdrop-blur-[2px]"
             x-on:keydown.escape.window="buka = false"
             role="alertdialog" aria-modal="true">

            <div class="absolute inset-0" aria-hidden="true" x-on:click="buka = false"></div>

            <div class="relative w-full max-w-[440px] overflow-hidden rounded-corner border border-line
                        bg-canvas shadow-[0_32px_80px_-24px_rgba(26,29,27,0.45)]">

                <div class="flex items-start gap-3 p-5">
                    {{-- SEGITIGA peringatan, bukan tong sampah: tong sampah
                         sudah dipakai tombol yang MEMBUKA kotak ini, dan
                         lambang yang sama untuk ajakan dan penegasannya
                         menghapus bedanya. --}}
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                 rounded-control bg-danger/10 text-danger">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M8 2.4 14.4 13.2H1.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            <path d="M8 6.6v2.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="8" cy="11.4" r="0.85" fill="currentColor"/>
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-admin-title text-heading">{{ $judul }}</h2>

                        <p class="mt-1 text-admin-body text-ink-muted">{{ $slot }}</p>

                        {{-- Nama barang yang akan hilang, berbingkai.
                             Judulnya menyebut JENISNYA ("Hapus kategori?"),
                             dan di tabel berisi belasan baris mirip, jenis
                             saja tidak cukup. --}}
                        @if(filled($nama))
                            <p class="mt-3 truncate rounded-control border border-line bg-mist
                                      px-3.5 py-2.5 text-admin-strong text-ink"
                               title="{{ $nama }}">{{ $nama }}</p>
                        @endif
                    </div>
                </div>

                {{-- "Batal" lebih dulu dan bergaris: yang paling mungkin
                     dituju orang yang membuka kotak ini karena salah tekan
                     adalah jalan keluarnya. --}}
                <div class="flex items-center justify-end gap-2 border-t border-line px-5 py-3.5">
                    <button type="button" x-on:click="buka = false"
                            class="admin-btn admin-btn-quiet">
                        Batal
                    </button>

                    <button type="button" x-on:click="buka = false; $wire.{{ $metode }}({{ collect(is_array($id) ? $id : array_filter([$id], 'filled'))->map(fn ($x) => "'" . $x . "'")->implode(', ') }})"
                            class="admin-btn admin-btn-danger">
                        {{ $tombol }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
