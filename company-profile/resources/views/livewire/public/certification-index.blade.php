<div>
    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            @php $sertBody = $isi('body', 'site.page_certifications_sub'); @endphp

            {{-- Rata tengah dalam satu lajur, bukan dua kolom berhadapan.

                 Yang di bawahnya kisi empat lambang selebar halaman — benda yang
                 sumbunya sendiri di tengah, dan tiap kartunya pun rata tengah.
                 Kepala yang menempel ke tepi kiri meninggalkan sumbu yang berbeda
                 dari isinya sendiri.

                 Lebarnya dipatok 44rem dan 56ch supaya barisnya tidak melar
                 selebar halaman: teks rata tengah yang panjang paling sulit
                 dibaca, karena mata kehilangan awal baris berikutnya. --}}
            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.certifications') }}</p>

                {{-- 42px cokelat, ukuran dan warna yang sama dengan kepala halaman
                     produk, pasar ekspor, dan berita. Tetap <h1> — ia judul halaman
                     ini — tapi tidak 48px: ukuran itu membuatnya lebih besar
                     daripada judul mana pun di situs, padahal perannya sama. --}}
                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_certifications')) !!}
                </h1>

                {{-- Isian ini ditulis lewat penyunting teks kaya, jadi isinya
                     mengandung tag. Yang lama menggambarnya dengan {{ }} — tag-nya
                     ikut TERCETAK di layar sebagai "<p>…</p>". Yang mengandung tag
                     digambar sebagai .rich; yang polos tetap .lede, karena <p> di
                     dalam <p> bukan HTML yang sah. --}}
                @if($sertBody !== strip_tags($sertBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $sertBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $sertBody }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         KISI SERTIFIKAT
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($certifications->isNotEmpty())
                {{-- Judul seksi 26px, bukan 42px: ia bawahan judul halaman, dan dua
                     judul seukuran di satu layar membuat keduanya sama-sama tidak
                     terbaca sebagai puncak.

                     Kepala halaman di atasnya menerangkan APA arti sertifikat bagi
                     pembeli; judul ini menandai di mana daftarnya dimulai. Tanpa
                     itu, kisi lambang muncul begitu saja sesudah satu paragraf dan
                     tidak ada yang menyatakan bahwa yang di bawah ini daftar
                     lengkapnya. --}}
                <h2 class="display text-site-h3 text-site-forest">
                    {!! \App\Support\Judul::sorot(__('site.all_certifications')) !!}
                </h2>

                {{-- EMPAT lajur di layar lebar, bukan tiga.

                     Isi tiap kartu cuma lambang selebar 132px beserta nama dan
                     penerbitnya; di lajur selebar 379px, lambang itu mengambang di
                     tengah bidang yang dua setengah kali lebarnya sendiri. Empat
                     lajur memberi 279px — cukup untuk lambangnya bernapas, tanpa
                     ruang kosong yang tidak mengerjakan apa pun. --}}
                <ul class="mt-8 grid auto-rows-fr gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($certifications as $cert)
                        @php
                            $logo = $cert->getFirstMediaUrl('logos', 'thumb')
                                 ?: $cert->getFirstMediaUrl('logos');
                        @endphp

                        {{-- Saat DIAM kartu ini cuma lambang kelabu; saat DISOROT
                             lambangnya berwarna dan nama beserta penerbitnya terbuka.

                             Kelabu saat diam bukan sekadar gaya: lima lambang
                             berwarna sekaligus di satu kisi saling berebut, dan yang
                             paling mencolok belum tentu yang paling penting. Kelabu
                             menyamakan bobotnya, warna mengembalikannya pada yang
                             sedang ditunjuk.

                             Keduanya HANYA di lg ke atas. Di bawah itu tidak ada
                             kursor yang bisa menyorot: lambang yang selamanya kelabu
                             dan keterangan yang tidak pernah terbuka bukan rancangan,
                             melainkan isi yang hilang. Di layar sempit lambangnya
                             berwarna dan keterangannya selalu tampak.

                             Namanya tidak digambar saat diam: lambang-lambang ini sudah
                             memuat namanya sendiri — "FAIRTRADE", "Rainforest Alliance",
                             "TÜV Rheinland". Yang belum tentu terbaca dari lambangnya
                             justru LEMBAGA PENERBITNYA, dan itu yang ikut muncul
                             bersama namanya saat disorot. --}}
                        <li class="card group flex h-full min-h-[148px] flex-col items-center justify-center
                                   p-5 text-center lg:min-h-[160px]">

                            <span class="flex h-10 w-[116px] shrink-0 items-center justify-center">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="{{ $cert->translated_name }}" loading="lazy"
                                         class="max-h-full max-w-full object-contain transition duration-300
                                                lg:grayscale lg:opacity-70
                                                lg:group-hover:opacity-100 lg:group-hover:grayscale-0">
                                @else
                                    {{-- Belum ada lambang: NAMANYA yang jadi lambang, bukan
                                         dua huruf pertamanya. Monogram cuma bisa dibaca oleh
                                         yang sudah tahu jawabannya. --}}
                                    <span class="font-site-display text-site-small font-bold leading-snug text-site-forest">
                                        {{ $cert->translated_name }}
                                    </span>
                                @endif
                            </span>

                            {{-- Nama dan penerbitnya, terbuka saat kartunya disorot.

                                 Yang menutupnya grid-rows 0fr→1fr, bukan max-height.
                                 max-height menuntut angka yang ditebak: kekecilan
                                 memotong nama yang panjang, kebesaran membuat separuh
                                 animasinya berjalan di ruang kosong. 1fr artinya
                                 "setinggi isinya", dan nama sertifikat di sini datang
                                 dari panel.

                                 Yang disembunyikan cuma tinggi dan alfa — BUKAN
                                 display:none — jadi namanya tetap ada di pohon
                                 aksesibilitas bagi yang tidak memakai tetikus, dan
                                 tetap terbaca mesin pencari.

                                 Hanya di lg ke atas, sama seperti kelabunya: di layar
                                 sempit tidak ada kursor yang bisa menyorot, dan nama
                                 yang tidak pernah terbuka bukan rancangan melainkan
                                 isi yang hilang. --}}
                            <div class="grid grid-rows-[1fr] transition-all duration-300 ease-out
                                        lg:grid-rows-[0fr] lg:opacity-0
                                        lg:group-hover:grid-rows-[1fr] lg:group-hover:opacity-100
                                        lg:group-focus-within:grid-rows-[1fr] lg:group-focus-within:opacity-100">
                                <div class="overflow-hidden">
                                    <p class="mt-3 font-site-display font-bold leading-snug tracking-[-0.01em]
                                              text-site-forest text-site-body">
                                        {{ $cert->translated_name }}
                                    </p>

                                    @if($cert->issuer)
                                        <p class="mt-1 text-ink-muted text-site-small">
                                            {{ __('site.issued_by') }} {{ $cert->issuer }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center text-site-body">
                    {{ $isi('empty', 'site.no_certifications') }}
                </p>
            @endif
        </div>
    </section>
</div>
