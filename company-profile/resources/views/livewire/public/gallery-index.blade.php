<div x-data="{
        images: [],
        index: 0,
        album: '',
        open(list, name) {
            this.images = list;
            this.album = name;
            this.index = 0;
        },
        close() {
            this.images = [];
        },
        // [PERUBAHAN: YouTube Support] — Fungsi deteksi apakah item adalah video YouTube
        isYoutube(src) {
            return typeof src === 'string' && src.startsWith('youtube:');
        },
        // [PERUBAHAN: YouTube Support] — Konversi URL YouTube biasa ke format embed
        // Mendukung: youtube.com/watch?v=xxx, youtu.be/xxx, youtube.com/shorts/xxx
        youtubeEmbed(src) {
            const url = src.replace('youtube:', '');
            const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/);
            return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1&rel=0` : url;
        },
        /* Perpindahan MEMUTAR: dari foto terakhir kembali ke pertama. */
        next() { this.index = (this.index + 1) % this.images.length; },
        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; }
     }"
     x-on:keydown.escape.window="close()"
     x-on:keydown.arrow-right.window="images.length && next()"
     x-on:keydown.arrow-left.window="images.length && prev()">

    {{-- ══════════════════════════════════════════════════════════════════
         HEADER HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            @php $galeriBody = $isi('body', 'site.page_gallery_sub'); @endphp

            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-6">
                    <p class="eyebrow">{{ $isi('eyebrow', 'site.nav_gallery') }}</p>

                    {{-- 42px cokelat, ukuran dan warna yang sama dengan kepala
                         halaman produk, sertifikat, pasar ekspor, dan berita. Yang
                         lama 48px hitam — ukuran hero, di halaman yang bukan
                         beranda, dan warna tinta yang tidak dipakai judul mana pun
                         lagi di situs ini. --}}
                    <h1 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($isi('title', 'site.page_gallery')) !!}
                    </h1>
                </div>

                <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                    {{-- Isian ini ditulis lewat penyunting teks kaya, jadi isinya
                         mengandung tag. Yang lama menggambarnya dengan {{ }} —
                         tag-nya ikut TERCETAK di layar sebagai "<p>…</p>", dan itu
                         memang yang terjadi di halaman ini. Yang mengandung tag
                         digambar sebagai .rich; yang polos tetap .lede, karena <p>
                         di dalam <p> bukan HTML yang sah. --}}
                    @if($galeriBody !== strip_tags($galeriBody))
                        <div class="rich max-w-[46ch]">{!! $galeriBody !!}</div>
                    @else
                        <p class="lede max-w-[46ch] text-site-body">{{ $galeriBody }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         VIDEO SOROTAN
         ══════════════════════════════════════════════════════════════════ --}}
    @if($videoSematan)
        <section class="pb-6">
            <div class="shell">
                {{-- Menempati tempat yang dulu diduduki album pertama.

                     Album itu digambar selebar halaman bernisbah 16:9 — tiga
                     kali luas album lain — padahal yang menjadikannya pertama
                     cuma urutan di panel, bukan isinya. Video memang dipilih
                     untuk disorot, dan hanya ada satu.

                     Disemat langsung, bukan sebagai gambar kecil yang membuka
                     lightbox. Lightbox itu memang ada dan sudah bisa memutar
                     YouTube, tapi ia untuk album berisi banyak benda: satu
                     video yang menuntut dua ketukan sebelum berputar cuma
                     menambah pintu yang tidak menuju ke mana-mana.

                     loading="lazy" menahannya sampai bidangnya benar-benar
                     mendekati layar — sematan YouTube memuat beberapa ratus
                     kilobita sebelum satu detik pun diputar. --}}
                <div class="aspect-video w-full overflow-hidden rounded-panel bg-site-paper">
                    <iframe src="{{ $videoSematan }}"
                            title="{{ $isi('title', 'site.page_gallery') }}"
                            class="h-full w-full"
                            loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                </div>
            </div>
        </section>
    @endif

    <section class="pb-20 pt-6 lg:pb-24">
        <div class="shell">
            @if($albums->isNotEmpty())
                {{-- Judul seksi 26px, bukan 42px: ia bawahan judul halaman, dan
                     dua judul seukuran di satu layar membuat keduanya sama-sama
                     tidak terbaca sebagai puncak.

                     Ia juga yang memisahkan video sorotan dari kisi di bawahnya.
                     Tanpa itu, keduanya cuma dua blok bergambar yang berurutan
                     dengan jarak di antaranya, dan tidak ada yang menyatakan
                     bahwa yang di bawah ini daftar albumnya — sementara yang di
                     atas satu video yang berdiri sendiri.

                     Jumlah albumnya ikut disebut di bawahnya, sebagai label
                     kecil: ia keterangan tentang daftar yang persis ada di
                     bawahnya, dan pembaca tahu sejak awal apakah yang tergambar
                     ini seluruhnya atau baru sebagian. --}}
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.all_albums')) !!}
                </h2>

                <p class="mt-2 font-site-accent text-site-micro font-medium uppercase
                          tracking-[0.14em] text-ink-muted">
                    {{ trans_choice('site.albums_count', $albums->count(), ['count' => $albums->count()]) }}
                </p>

                {{-- SEMUA album bernisbah 4:3 dan selebar satu lajur — tidak ada
                     lagi yang pertama digambar tiga kali lebih besar. Urutan di
                     panel menentukan letak, bukan ukuran. --}}
                <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($albums as $album)
                        <li>
                            <x-site.gallery-album :album="$album" ratio="aspect-[4/3]"
                                                  :priority="$loop->first" />
                        </li>
                    @endforeach
                </ul>
            @else
                {{-- Syarat !$featured dilepas: sejak album pertama tidak lagi
                     dipisah jadi sorotan, $albums memuat SELURUHNYA — kalau ia
                     kosong, memang tidak ada album sama sekali. --}}
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_gallery_items') }}
                </p>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         LIGHTBOX
         ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="images.length" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-on:click="close()"
         class="fixed inset-0 z-[60] flex flex-col items-center justify-center bg-ink/90 p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">

        <p class="absolute left-5 top-6 font-semibold text-white/80 text-site-small">
            <span x-text="album"></span>
            <span class="ml-2 text-white/45" x-text="`${index + 1} / ${images.length}`"></span>
        </p>

        <button type="button" x-on:click="close()"
                aria-label="{{ __('site.close') }}"
                class="absolute right-5 top-5 inline-flex h-11 w-11 items-center justify-center rounded-full
                       border border-white/30 text-white transition-colors hover:border-white hover:bg-white/10">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="flex w-full max-w-[1100px] items-center gap-3 sm:gap-5" x-on:click.stop>

            <button type="button" x-show="images.length > 1" x-on:click="prev()"
                    aria-label="{{ __('site.close') }}"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                           border border-white/30 text-white transition-colors hover:border-white hover:bg-white/10">
                <svg class="h-4 w-4 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            {{-- Item berawalan 'youtube:' digambar sebagai iframe; sisanya
                 sebagai <img>. --}}
            <template x-if="isYoutube(images[index])">
                <div class="mx-auto w-full max-w-[900px] aspect-video">
                    <iframe
                        :src="youtubeEmbed(images[index])"
                        class="w-full h-full rounded-corner"
                        frameborder="0"
                        allow="autoplay; encrypted-media; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </template>
            <template x-if="!isYoutube(images[index])">
                <img x-bind:src="images[index]" x-bind:alt="album"
                     class="mx-auto max-h-[80vh] w-auto max-w-full rounded-corner object-contain">
            </template>

            <button type="button" x-show="images.length > 1" x-on:click="next()"
                    aria-label="{{ __('site.close') }}"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                           border border-white/30 text-white transition-colors hover:border-white hover:bg-white/10">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
</div>
