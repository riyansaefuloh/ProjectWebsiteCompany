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
            <div class="max-w-[46rem]">
                <p class="eyebrow">{{ __('site.nav_gallery') }}</p>
                <h1 class="display mt-5 max-w-[16ch] text-[32px] sm:text-[38px] lg:text-[46px]">
                    {{ __('site.page_gallery') }}
                </h1>
                <p class="lede mt-6 max-w-[52ch]">{{ __('site.page_gallery_sub') }}</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         ALBUM SOROTAN
         ══════════════════════════════════════════════════════════════════ --}}
    @if($featured)
        <section class="pb-6">
            <div class="shell">
                <x-site.gallery-album :album="$featured" ratio="aspect-[16/9]" :priority="true" />
            </div>
        </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         ALBUM LAINNYA
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 pt-6 lg:pb-24">
        <div class="shell">
            @if($albums->isNotEmpty())
                <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($albums as $album)
                        <li>
                            <x-site.gallery-album :album="$album" ratio="aspect-[4/3]" />
                        </li>
                    @endforeach
                </ul>
            @elseif(!$featured)
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ __('site.no_gallery_items') }}
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

        <p class="absolute left-5 top-6 text-[13px] font-bold text-white/80">
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

            {{-- ══════════════════════════════════════════════════════════════════
                 [PERUBAHAN: YouTube Support]
                 Dulu: hanya <img> biasa untuk foto
                 Sekarang: cek dulu apakah item adalah YouTube (prefix 'youtube:')
                 - Jika ya  → render <iframe> YouTube embedded (autoplay)
                 - Jika tidak → render <img> seperti biasa
                 ══════════════════════════════════════════════════════════════════ --}}
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
