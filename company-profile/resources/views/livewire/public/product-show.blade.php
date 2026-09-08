<div>
    <section class="pb-16 pt-6 md:pt-8 lg:pb-20 lg:pt-10">
        <div class="shell">

            {{-- Tautan kembali berupa pil pucat bercincin, rupa yang sama dengan
                 "Read Article" di kartu berita. Yang lama .link-arrow — tautan
                 telanjang berpanah kecil; di puncak halaman yang isinya foto besar
                 dan judul 42px, ia terlalu tipis untuk terbaca sebagai jalan
                 kembali. --}}
            <a href="{{ route('products.index') }}"
               class="inline-flex h-10 w-max items-center gap-2.5 rounded-full pl-3 pr-5
                      bg-site-paper text-site-forest ring-1 ring-line-strong
                      font-site-body text-site-small font-semibold whitespace-nowrap
                      transition-colors duration-300 hover:bg-site-line">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                             bg-site-canvas text-site-forest">
                    <svg class="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('site.page_products') }}
            </a>

            <div class="mt-5 grid items-start gap-8 lg:mt-6 lg:grid-cols-12 lg:gap-12">

                {{-- ── KIRI: foto ──────────────────────────────────────────────
                     Kartunya SELURUHNYA foto — tanpa bantalan, tanpa bidang krem di
                     sisinya, tanpa latar kabur menambal sisa ruang. Nisbahnya
                     mengikuti foto produknya masing-masing, jadi "mengisi penuh" dan
                     "tidak terpotong" tidak lagi saling meniadakan.

                     Lebarnya 5 dari 12, bukan separuh. Kartu ini tingginya ditentukan
                     nisbah fotonya, jadi menyempitkan kolomnya adalah satu-satunya cara
                     memendekkannya tanpa mulai memotong gambar lagi — dan tiga petak
                     yang berpindah ke kanan dipakai lembar spesifikasi, yang sejak jadi
                     satu kolom memang butuh lebar antara label dan nilainya. --}}
                <div class="lg:sticky lg:top-[92px] lg:col-span-5" x-data="{ aktif: 0 }">
                    <div class="overflow-hidden rounded-panel border border-line bg-site-paper">
                        @forelse($gallery as $i => $foto)
                            {{-- Fotonya mengisi kartu SEPENUHNYA, tepi ke tepi.

                                 Nisbah kartunya datang dari foto itu sendiri (lihat
                                 nisbahKartu di komponen), jadi object-cover di sini
                                 tidak memotong apa pun kecuali pada foto yang
                                 melewati batas 4:5 sampai 3:2.

                                 Nisbahnya ditulis sebagai gaya sebaris, bukan kelas
                                 aspect-[...]: Tailwind memindai berkas sumber untuk
                                 menemukan kelas yang perlu dibuat, dan kelas yang
                                 nilainya baru diketahui saat halaman digambar tidak
                                 pernah ada di berkas mana pun untuk ditemukan. --}}
                            <div x-show="aktif === {{ $i }}"
                                 @if($i > 0) x-cloak @endif
                                 class="w-full" style="aspect-ratio: {{ $nisbah }}">
                                <img src="{{ $foto['besar'] }}"
                                     alt="{{ $product->translated_name }}@if($i > 0) — {{ $i + 1 }}@endif"
                                     @if($i === 0) fetchpriority="high" @else loading="lazy" @endif
                                     class="h-full w-full object-cover">
                            </div>
                        @empty
                            <x-site.image-placeholder icon="h-16 w-16"
                                class="w-full bg-transparent" style="aspect-ratio: {{ $nisbah }}" />
                        @endforelse
                    </div>

                    {{-- Deretan gambar kecil digambar HANYA kalau fotonya lebih dari
                         satu. Satu gambar kecil di bawah satu foto besar tidak
                         menawarkan pilihan apa pun; ia cuma mengulang gambar yang
                         sudah terlihat. --}}
                    @if($gallery->count() > 1)
                        <ul class="mt-3 flex flex-wrap gap-3">
                            @foreach($gallery as $i => $foto)
                                <li>
                                    <button type="button" x-on:click="aktif = {{ $i }}"
                                            x-bind:aria-current="aktif === {{ $i }} ? 'true' : 'false'"
                                            x-bind:class="aktif === {{ $i }}
                                                ? 'border-site-forest'
                                                : 'border-line hover:border-line-strong'"
                                            class="block h-20 w-20 overflow-hidden rounded-control border-2
                                                   bg-site-paper transition-colors">
                                        <span class="sr-only">
                                            {{ $product->translated_name }} — {{ $i + 1 }}
                                        </span>

                                        <img src="{{ $foto['kecil'] }}" alt="" aria-hidden="true" loading="lazy"
                                             class="h-full w-full object-cover">
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- ── KANAN: nama, keterangan, spesifikasi, ajakan ───────────── --}}
                <div class="lg:col-span-7">

                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                           class="eyebrow transition-colors hover:text-site-forest">
                            {{ $product->category->translated_name }}
                        </a>
                    @endif

                    {{-- 42px cokelat, ukuran dan warna yang sama dengan judul seksi
                         produk di beranda. Tetap <h1> — ia judul halaman ini — tapi
                         tidak lagi 48px: ukuran itu membuatnya lebih besar daripada
                         judul mana pun di situs, padahal perannya sama. --}}
                    <h1 class="display mt-4 max-w-[18ch] text-site-h2 text-site-forest">
                        {!! \App\Support\Judul::sorot($product->translated_name) !!}
                    </h1>

                    @if($product->translated_description)
                        <div class="rich mt-4 max-w-[54ch]">{!! $product->translated_description !!}</div>
                    @endif

                    {{-- ── Lembar spesifikasi ─────────────────────────────────
                         Satu kolom: sembilan butir yang dibaca berurutan dari atas ke
                         bawah, bukan dipecah jadi dua tumpukan yang harus disusuri
                         bolak-balik.

                         Angkanya tabular-nums. Yang dilakukan pembeli dengan kolom ini
                         adalah membandingkannya antar-produk, dan angka berlebar tetap
                         membuat digit yang sama berdiri di kolom yang sama saat dua
                         halaman dibuka bergantian. --}}
                    @if($facts->isNotEmpty())
                        <div class="mt-7">
                            <p class="eyebrow">{{ __('site.specifications') }}</p>

                            {{-- Satu BIDANG lembar data, bukan baris-baris melayang.

                                 Yang lama sederet baris bergaris bawah di atas kanvas
                                 halaman — tiap baris berdiri sendiri, dan tidak ada
                                 yang menyatakan bahwa kesembilannya satu himpunan.
                                 Dibungkus satu bidang bergaris dengan sekat rambut di
                                 antaranya, ia terbaca sebagai lembar data: satu benda
                                 yang bisa dipindai dari atas ke bawah.

                                 Jumlah pesanan minimum dan harga indikatif ikut di
                                 dalamnya, sederajat dengan asal dan kadar air. Keduanya
                                 pernah diangkat keluar jadi dua angka besar; tanpa itu,
                                 seluruh keterangan produk kembali terkumpul di satu
                                 tempat dan pembacanya tidak perlu memindai dua bidang
                                 berbeda untuk menghitung satu penawaran.

                                 Label di kiri, nilai di kanan — bukan bertumpuk.
                                 Bertumpuk memakan dua baris untuk tiap butir; di
                                 halaman yang sengaja dijaga muat satu layar, sembilan
                                 butir berarti delapan belas baris. Berhadapan, tiap
                                 butir cukup satu — dan di satu kolom yang selebar
                                 setengah halaman, jarak antara label dan nilainya masih
                                 cukup lebar untuk terbaca sebagai pasangan. --}}
                            <dl class="mt-3 grid gap-px overflow-hidden rounded-corner border border-line
                                       bg-line">
                                @foreach($facts as $fact)
                                    <div class="flex items-baseline justify-between gap-4 bg-site-canvas px-4 py-2.5">
                                        <dt class="shrink-0 font-site-accent text-site-micro font-medium uppercase
                                                   tracking-[0.1em] text-ink-muted">
                                            {{ $fact['label'] }}
                                        </dt>
                                        <dd class="min-w-0 text-right font-semibold tabular-nums
                                                   text-site-forest text-site-small">
                                            {{ $fact['value'] }}
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    {{-- Sertifikat pindah KELUAR dari kartu foto.

                         Di sana ia duduk di atas gambar sebagai deretan keping tanpa
                         judul — tidak jelas apakah ia menerangkan fotonya atau
                         produknya. Ia fakta tentang produk, sederajat dengan lembar
                         spesifikasi, jadi tempatnya di sebelahnya dan berlabel. --}}
                    @if($product->certifications->isNotEmpty())
                        <div class="mt-8">
                            <p class="eyebrow">{{ __('site.certifications') }}</p>

                            <ul class="mt-4 flex flex-wrap gap-2">
                                @foreach($product->certifications as $cert)
                                    <li>
                                        <a href="{{ route('certifications.index') }}"
                                           class="inline-flex items-center rounded-full border border-line bg-site-paper
                                                  px-3.5 py-1.5 font-site-body text-site-small font-semibold text-ink-muted
                                                  transition-colors hover:border-site-forest hover:text-site-forest">
                                            {{ $cert->translated_name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Sepasang tombol dengan rupa yang sama seperti di banner
                         penutup: pil forest pejal berbulatan emas untuk ajakan
                         utamanya, pil pucat bercincin untuk WhatsApp.

                         Yang lama .btn-pill .btn-pill-brand dan .btn .btn-outline —
                         dua keluarga tombol lama yang tidak dipakai di halaman mana
                         pun lagi. --}}
                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <a href="{{ route('inquiry.index', ['product' => $product->id]) }}"
                           class="group inline-flex h-10 items-center gap-3 rounded-full pl-5 pr-1.5
                                  bg-site-forest text-white
                                  font-site-body text-site-small font-semibold whitespace-nowrap
                                  transition-colors duration-300 hover:bg-site-brand-deep">
                            {{ __('site.cta_request_quote') }}
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                         bg-site-gilt text-site-forest transition-transform duration-200
                                         group-hover:translate-x-0.5">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>

                        @if($waLink)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex h-10 items-center gap-2.5 rounded-full px-5
                                      bg-site-paper text-site-forest ring-1 ring-line-strong
                                      font-site-body text-site-small font-semibold whitespace-nowrap
                                      transition-colors duration-300 hover:bg-site-line">
                                <x-icon.whatsapp size="h-4 w-4" class="shrink-0" />
                                {{ __('site.cta_whatsapp') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
