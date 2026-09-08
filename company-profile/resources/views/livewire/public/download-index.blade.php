<div>
    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pt-20">
        <div class="shell">
            @php $unduhBody = $isi('body', 'site.page_downloads_sub'); @endphp

            {{-- Rata tengah dalam satu lajur, bukan dua kolom berhadapan —
                 sama dengan kepala halaman Sertifikat, Pasar Ekspor, dan News.

                 Lebarnya dipatok 44rem dan 56ch supaya barisnya tidak melar
                 selebar halaman: teks rata tengah yang panjang paling sulit
                 dibaca, karena mata kehilangan awal baris berikutnya. --}}
            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.nav_downloads') }}</p>

                {{-- 42px cokelat, ukuran dan warna yang sama dengan kepala
                     halaman produk, sertifikat, galeri, dan berita. Yang lama
                     48px hitam — ukuran hero, di halaman yang bukan beranda. --}}
                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_downloads')) !!}
                </h1>

                {{-- Isian ini ditulis lewat penyunting teks kaya, jadi isinya
                     mengandung tag. Yang lama menggambarnya dengan {{ }} —
                     tag-nya ikut TERCETAK di layar sebagai "<p>…</p>". Yang
                     mengandung tag digambar sebagai .rich; yang polos tetap
                     .lede, karena <p> di dalam <p> bukan HTML yang sah. --}}
                @if($unduhBody !== strip_tags($unduhBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $unduhBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $unduhBody }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         KISI BERKAS
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($downloads->isNotEmpty())
                {{-- Judul seksi 26px, bukan 42px: ia bawahan judul halaman, dan
                     dua judul seukuran di satu layar membuat keduanya sama-sama
                     tidak terbaca sebagai puncak.

                     Jumlah berkasnya disebut di bawahnya sebagai label kecil —
                     pembaca tahu sejak awal apakah yang tergambar ini seluruhnya
                     atau baru sebagian. --}}
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.all_files')) !!}
                </h2>

                <p class="mt-2 font-site-accent text-site-micro font-medium uppercase
                          tracking-[0.14em] text-ink-muted">
                    {{ trans_choice('site.files_count', $downloads->count(), ['count' => $downloads->count()]) }}
                </p>

                {{-- ── BARIS BERKAS, bukan kartu tegak ─────────────────────────
                     Yang didaftar di sini DOKUMEN, dan isi tiap dokumen cuma
                     nama, jenis, dan satu tombol. Kartu tegak setinggi 240px
                     untuk tiga baris keterangan menghasilkan bidang yang dua
                     pertiganya kosong — dan tombol yang didorong ke dasar kartu
                     oleh ruang kosong itu berdiri jauh dari nama berkas yang
                     diunduhnya.

                     Sebagai baris mendatar, ketiganya berdampingan: lambang di
                     kiri, nama dan keterangannya di tengah, tombol di kanan.
                     Tingginya jadi 88px — sepertiga dari sebelumnya — dan tidak
                     ada satu piksel pun yang tidak mengerjakan apa-apa.

                     Dua lajur, bukan tiga: nama berkas di sini panjang-panjang
                     ("Product Specifications & Quality Standards"), dan baris
                     mendatar berlajur tiga menyisakan lebar yang memaksa nama
                     itu pecah tiga baris. --}}
                <ul class="mt-8 grid items-start gap-4 lg:grid-cols-2">
                    @foreach($downloads as $download)
                        @php $terpilih = $selectedDownloadId === $download->id; @endphp

                        <li class="flex" wire:key="download-{{ $download->id }}">
                            <div @class([
                                'flex h-full w-full flex-col justify-center rounded-corner border bg-site-canvas',
                                'p-4 transition-colors duration-200 sm:p-5',
                                'border-site-forest' => $terpilih,
                                'border-line'        => ! $terpilih,
                            ])>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-3">
                                    {{-- Keping ikon forest berisi lambang terang,
                                         rupa yang sama dengan keping ikon di gerbang
                                         katalog. --}}
                                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center
                                                 rounded-corner bg-site-forest text-site-canvas"
                                          aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M13.4 2.8H6.8a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.6l-5.8-5.8Z"
                                                  stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                            <path d="M13.4 2.8v5.8h5.8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        {{-- Judul memesan ruang DUA BARIS, selalu.

                                             Nama berkas di sini panjangnya
                                             berbeda-beda: yang satu baris berakhir
                                             21px lebih pendek daripada tetangganya
                                             yang pecah dua, dan dua baris
                                             berdampingan yang tidak sama tinggi
                                             terbaca sebagai kisi yang salah pasang.

                                             Dipesan lewat min-h, BUKAN dengan
                                             meregangkan barisnya ke tinggi
                                             tetangganya: saat satu baris membuka
                                             kolom email, regangan itu ikut menyeret
                                             tetangganya jadi setinggi 241px berisi
                                             ruang kosong.

                                             2,75em = dua baris pada leading-snug
                                             (1,375), dalam satuan em supaya ikut
                                             menyusut bersama hurufnya di layar
                                             sempit. --}}
                                        <h3 class="min-h-[2.75em] font-site-display text-site-body font-bold
                                                   leading-snug text-site-forest">
                                            {{ $download->title }}
                                        </h3>

                                        {{-- Jenis, jumlah unduhan, dan penanda gerbang
                                             email BERBAGI satu baris.

                                             Penandanya dulu pil tersendiri di sudut
                                             kanan atas kartu — benda keempat di kartu
                                             yang isinya cuma tiga hal. Sebagai butir
                                             ketiga di baris keterangan, ia terbaca
                                             sebagai sifat berkasnya, yang memang itu
                                             adanya.

                                             ink-muted, bukan ink-faint: pada 11px yang
                                             terukur dari piksel cuma 4,33:1. --}}
                                        <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1
                                                  font-site-accent text-site-micro font-medium uppercase
                                                  tracking-[0.12em] text-ink-muted">
                                            <span>PDF</span>
                                            <span aria-hidden="true">·</span>
                                            <span class="tabular-nums">
                                                {{ __('site.downloads_count', ['count' => number_format($download->download_count)]) }}
                                            </span>

                                            @if($download->require_email)
                                                <span aria-hidden="true">·</span>
                                                <span class="inline-flex items-center gap-1.5 text-site-gilt-deep">
                                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                        <rect x="3.2" y="7" width="9.6" height="6.8" rx="1.6" stroke="currentColor" stroke-width="1.4"/>
                                                        <path d="M5.6 7V5.2a2.4 2.4 0 0 1 4.8 0V7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                                    </svg>
                                                    {{ __('site.enter_email') }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Pil forest berbulatan emas, keluarga tombol yang
                                         sama dengan "Request Quote" dan gerbang katalog.
                                         TIDAK melebar penuh: di baris mendatar ia berdiri
                                         di ujung kanan, sejajar dengan nama berkas yang
                                         diunduhnya.

                                         Bulatan panahnya menunjuk KE BAWAH — yang terjadi
                                         sesudah ditekan adalah berkas turun, bukan halaman
                                         berpindah. --}}
                                    <button type="button" wire:click="download('{{ $download->id }}')"
                                            wire:loading.attr="disabled" wire:target="download('{{ $download->id }}')"
                                            class="group ml-auto inline-flex h-10 shrink-0 items-center gap-3
                                                   rounded-full bg-site-forest pl-5 pr-1.5 text-white
                                                   font-site-body text-site-small font-semibold whitespace-nowrap
                                                   transition-colors duration-300 hover:bg-site-brand-deep
                                                   disabled:opacity-60">
                                        {{ __('site.download_pdf_btn') }}
                                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                     bg-site-gilt text-site-forest transition-transform duration-200
                                                     group-hover:translate-y-0.5">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M8 3v7m0 0L5 7m3 3 3-3M3.5 13h9" stroke="currentColor"
                                                      stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>

                                {{-- ── Kolom email, hanya untuk berkas yang sedang
                                     diminta ──────────────────────────────────────
                                     Terbuka DI BAWAH baris, dipisah garis rambut dan
                                     jarak 16px di kedua sisinya — bukan menempel pada
                                     baris di atasnya maupun pada tombolnya. --}}
                                @if($terpilih)
                                    <div class="mt-4 border-t border-line pt-4">
                                        <label for="dl-email-{{ $download->id }}" class="field-label">
                                            {{ __('site.field_email') }} *
                                        </label>

                                        <p class="mt-1.5 leading-relaxed text-ink-muted text-site-micro">
                                            {{ $isi('gated_note', 'site.download_gated_note') }}
                                        </p>

                                        <input id="dl-email-{{ $download->id }}" type="email"
                                               wire:model="email"
                                               wire:keydown.enter="download('{{ $download->id }}')"
                                               autocomplete="email"
                                               placeholder="{{ __('site.field_email_ph') }}"
                                               @error('email') aria-invalid="true" @enderror
                                               class="field @error('email') border-danger @enderror">

                                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center text-site-body">
                    {{ $isi('empty', 'site.no_downloads') }}
                </p>
            @endif
        </div>
    </section>
</div>
