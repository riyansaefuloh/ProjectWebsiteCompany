@props([
    'label' => '',
    'isVideo' => false,
    'count' => 0,
])

{{-- PEREDAM memakai site-shade, peredam netral situs ini — bukan --color-ink,
     yang cokelat. Foto galeri isinya hijau daun, merah ceri, dan kayu; peredam
     bernada cokelat menariknya semua ke satu rona hangat dan warna aslinya
     hilang. Netral menggelapkan tanpa mewarnai.

     Berhenti di 55% tinggi kartu, bukan menutup seluruhnya: yang perlu
     digelapkan cuma tempat tulisannya berdiri. --}}
<span class="pointer-events-none absolute inset-x-0 bottom-0 h-[55%]
             bg-gradient-to-t from-site-shade/85 via-site-shade/45 to-transparent"
      aria-hidden="true"></span>

<span class="pointer-events-none absolute inset-x-0 bottom-0 p-5 text-left">
    {{-- Jumlah isi berdiri DI ATAS nama sebagai label kecil, bukan sebagai
         keping angka di sebelahnya.

         Sebagai keping, angka itu sederajat dengan nama album — dua benda
         berdampingan yang sama beratnya, padahal yang satu nama dan yang satu
         keterangan tentangnya. Sebagai label di atasnya, urutannya jadi jelas:
         golongan dulu, baru namanya — susunan yang sama dengan kartu produk.

         Emas nada terang, bukan nada dalam: di atas peredam gelap, gilt-deep
         terbaca 2,4:1. Nada terangnya 6,99:1 terhadap forest dan lebih tinggi
         lagi terhadap peredam ini. --}}
    @if($count > 0)
        <span class="flex items-center gap-2 font-site-accent text-site-micro font-medium
                     uppercase tracking-[0.16em] text-site-gilt">
            @if($isVideo)
                {{-- Bulatan emas berpanah putar, rupa yang sama dengan bulatan
                     panah di tombol ajakan situs ini. Yang lama bulatan putih
                     berpanah tinta — putih pejal di atas foto adalah benda
                     paling terang di kartu, dan ia merebut perhatian dari
                     nama albumnya sendiri. --}}
                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                             bg-site-gilt text-site-forest" aria-hidden="true">
                    <svg class="ml-px h-2.5 w-2.5" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M4.5 2.8 13 8l-8.5 5.2V2.8Z"/>
                    </svg>
                </span>
            @endif

            {{ trans_choice('site.album_count', $count, ['count' => $count]) }}
        </span>
    @endif

    {{-- Nama album memakai huruf JUDUL, bukan huruf badan — rupa yang sama
         dengan nama produk di kartu produk dan judul artikel di kartu berita.
         Di kartu, nama dibaca sebagai tanda, bukan sebagai kalimat. --}}
    <span class="mt-1.5 block font-site-display text-site-title font-bold leading-snug
                 tracking-[-0.01em] text-white">
        {{ $label }}
    </span>
</span>
