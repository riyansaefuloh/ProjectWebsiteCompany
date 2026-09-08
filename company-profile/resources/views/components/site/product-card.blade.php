@props([
    'product',

    /*
     * Label tautannya datang dari luar, dengan teks bawaan sebagai cadangan.
     *
     * Kartu ini dipakai di dua tempat — seksi produk unggulan di beranda dan
     * halaman Produk — dan keduanya punya kolomnya sendiri di panel. Kalau
     * labelnya dikunci di dalam komponen, satu-satunya cara mengubahnya adalah
     * menyunting berkas bahasa.
     */
    'label' => null,
])

@php
    $image = $product->getFirstMediaUrl('gallery', 'medium')
          ?: $product->getFirstMediaUrl('gallery');

    $caption = $product->category?->translated_name
            ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->translated_description), 90);

    // Warna teks mengikuti ada-tidaknya foto. Dengan foto, teks berdiri di atas
    // peredam gelap sehingga harus putih; tanpa foto, latarnya bidang terang dan
    // teks putih akan hilang sama sekali.
    $titleColor   = $image ? 'text-white' : 'text-ink';
    /* white/85, bukan white/75. Kategori berdiri di atas FOTO, dan yang
       menahannya cuma gradasi peredam kartu — jadi kontrasnya berubah
       mengikuti foto yang kebetulan diunggah. Terukur dari piksel pada
       salah satu foto produk: white/75 cuma 4,42:1, tepat di bawah ambang.
       white/85 memberi 5,85:1 pada latar yang sama, dan margin itu yang
       menampung foto berikutnya yang mungkin lebih terang. */
    $captionColor = $image ? 'text-white/85' : 'text-ink-muted';
@endphp

{{-- ── KARTU PRODUK BERLATAR FOTO ──────────────────────────────────────── --}}
<a href="{{ route('products.show', $product->slug) }}"
   {{ $attributes->merge(['class' => 'group relative isolate flex min-h-[440px] flex-col justify-end overflow-hidden rounded-corner bg-mist-deep p-6 sm:min-h-[480px]']) }}>

    @if($image)
        <img src="{{ $image }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]">

        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink/90 via-ink/55 to-transparent" aria-hidden="true"></div>
    @else
        {{-- Tanpa foto: peredam gelap TIDAK dipasang. Gunanya menahan teks
             putih agar terbaca di atas foto; di atas bidang terang tanpa foto
             ia justru mengaburkan. --}}
        <x-site.image-placeholder class="absolute inset-0 -z-10 h-full w-full" icon="h-12 w-12" />
    @endif

    {{-- Pil beraksen NADA TERANG — warna yang sama dengan bulatan panah pada
         tombol di bawahnya. Keduanya terlihat sekaligus di satu kartu, dan dua
         nada aksen yang berbeda di jarak sedekat itu terbaca seperti salah
         satunya gagal memuat warnanya.

         Hurufnya WAJIB gelap. Putih di atas nada terang cuma 2,10:1 — hilang;
         forest memberi 6,99:1. Aturan yang sama dengan pil ajakan di bilah
         kepala, dan alasannya pun sama.

         Tanpa titik: titik berwarna itu dulu ada justru karena pilnya sendiri
         putih — satu-satunya cara menaruh aksen pada bidang tak berwarna.
         Begitu pilnya sendiri beraksen, titiknya mengulang hal yang sama. --}}
    @if($product->is_featured)
        <span class="absolute left-6 top-6 inline-flex items-center rounded-full
                     bg-site-gilt px-3.5 py-1.5 font-site-accent text-site-micro
                     font-medium uppercase tracking-[0.14em] text-site-forest">
            {{ __('site.featured') }}
        </span>
    @endif

    {{-- Kategori berdiri DI ATAS nama, bukan di bawahnya.

         Di bawah nama, ia terbaca sebagai keterangan tambahan — sesuatu yang
         boleh dilewati. Di atas nama, ia jadi penggolong: pembaca tahu ia
         sedang melihat apa sebelum tahu namanya, persis seperti label kecil di
         atas judul seksi.

         Ukurannya text-site-lede, sama dengan keterangan hero maupun seksi
         produk — terukur 17px Inter di ketiganya. Baris ini memuat NAMA
         KATEGORI, hal yang dipakai pembeli untuk menyaring; pada 13px ia
         terbaca sebagai catatan kaki dan terlewat. --}}
    @if($caption)
        <p class="line-clamp-2 leading-relaxed {{ $captionColor }} text-site-lede">
            {{ $caption }}
        </p>
    @endif

    {{-- Nama produk memakai huruf JUDUL, bukan huruf badan. Di kartu, nama
         produk dibaca sebagai tanda — bukan sebagai kalimat — dan rupa yang
         sama dengan judul seksi di atasnya yang mengikatnya ke sana. --}}
    <h3 class="mt-1.5 font-site-display font-bold leading-tight tracking-[-0.015em] {{ $titleColor }} text-site-h3">
        {{ $product->translated_name }}
    </h3>

    {{-- Bentuknya SAMA dengan "Explore Products": pil setinggi 40px, huruf badan
         biasa, dan bulatan beraksen berpanah yang mengambil bantalan kanannya
         sendiri. Keduanya berdiri di seksi yang sama dan terlihat sekaligus;
         dua rupa tombol di sana terbaca sebagai dua sistem yang kebetulan
         bertemu.

         Melebar penuh DILEPAS, ikut acuannya: tombol selebar kartu terbaca
         sebagai bidang, bukan sebagai tombol — dan di kartu yang seluruhnya
         sudah bisa diklik, bidang selebar itu tidak menambah apa pun.

         Badannya KACA GELAP, sama seperti di hero — bukan pil pejal. Yang
         menjaga keterbacaannya di sini bukan pil itu sendiri melainkan peredam
         kartu: gradasi from-ink/90 di kaki kartu sudah menggelapkan tepat di
         tempat tombol ini berdiri, jadi kaca 45% di atasnya cukup. Cincin
         putih tipis yang menggambar tepinya, persis acuannya. --}}
    <span class="mt-6 inline-flex h-10 w-max items-center gap-3 self-end rounded-full pl-5 pr-1.5
                 bg-site-shade/45 text-white ring-1 ring-white/25
                 font-site-body text-site-small font-semibold whitespace-nowrap
                 transition-colors duration-300 group-hover:bg-site-shade/65">
        {{ $label ?: __('site.view_details') }}
        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                     bg-site-gilt text-site-forest transition-transform duration-200
                     group-hover:translate-x-0.5">
            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    </span>
</a>
