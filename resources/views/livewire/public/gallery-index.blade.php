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
        isYoutube(src) {
            return typeof src === 'string' && src.startsWith('youtube:');
        },
        youtubeEmbed(src) {
            const url = src.replace('youtube:', '');
            const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/);
            return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1&rel=0` : url;
        },
        next() { this.index = (this.index + 1) % this.images.length; },
        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; }
     }"
     x-on:keydown.escape.window="close()"
     x-on:keydown.arrow-right.window="images.length && next()"
     x-on:keydown.arrow-left.window="images.length && prev()">

    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            @php $galeriBody = $isi('body', 'site.page_gallery_sub'); @endphp

            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-6">
                    <p class="eyebrow">{{ $isi('eyebrow', 'site.nav_gallery') }}</p>

                    <h1 class="display mt-5 max-w-[18ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($isi('title', 'site.page_gallery')) !!}
                    </h1>
                </div>

                <div class="lg:col-span-5 lg:col-start-8 lg:self-end">
                    @if($galeriBody !== strip_tags($galeriBody))
                        <div class="rich max-w-[46ch]">{!! $galeriBody !!}</div>
                    @else
                        <p class="lede max-w-[46ch] text-site-body">{{ $galeriBody }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($videoSematan)
        <section class="pb-6">
            <div class="shell">
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
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.all_albums')) !!}
                </h2>

                <p class="mt-2 font-site-accent text-site-micro font-medium uppercase
                          tracking-[0.14em] text-ink-muted">
                    {{ trans_choice('site.albums_count', $albums->count(), ['count' => $albums->count()]) }}
                </p>

                <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($albums as $album)
                        <li>
                            <x-site.gallery-album :album="$album" ratio="aspect-[4/3]"
                                                  :priority="$loop->first" />
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_gallery_items') }}
                </p>
            @endif
        </div>
    </section>

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

            {{-- Sematan Iframe YouTube atau Gambar --}}
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
