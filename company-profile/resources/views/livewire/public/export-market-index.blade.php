<div>
    {{-- ══════════════════════════════════════════════════════════════════
         KEPALA HALAMAN
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-10 pt-14 md:pt-16 lg:pb-12 lg:pt-20">
        <div class="shell">
            @php $pasarBody = $isi('body', 'site.page_export_markets_sub'); @endphp

            {{-- Rata tengah dalam satu lajur, bukan dua kolom berhadapan.

                 Yang di bawahnya peta dunia — benda selebar halaman yang
                 pusatnya di tengah. Kepala yang menempel ke tepi kiri
                 meninggalkan sumbu yang berbeda dari isinya sendiri; satu lajur
                 di tengah membuat judul, keterangan, dan peta berbagi satu garis
                 tengah yang sama.

                 Lebarnya dipatok 44rem dan 56ch supaya barisnya tidak melar
                 sampai selebar halaman: teks rata tengah yang panjang paling
                 sulit dibaca, karena mata kehilangan awal baris berikutnya. --}}
            <div class="mx-auto max-w-[44rem] text-center">
                <p class="eyebrow">{{ $isi('eyebrow', 'site.home_section_export_markets') }}</p>

                {{-- 42px cokelat, ukuran dan warna yang sama dengan kepala
                     halaman sertifikat dan produk. Yang lama 48px hitam —
                     ukuran hero, di halaman yang bukan beranda. --}}
                <h1 class="display mx-auto mt-5 max-w-[20ch] text-site-h2 text-site-forest">
                    {!! \App\Support\Judul::sorot($isi('title', 'site.page_export_markets')) !!}
                </h1>

                {{-- Isian ini ditulis lewat penyunting teks kaya, jadi isinya
                     mengandung tag. Yang lama menggambarnya dengan {{ }} —
                     tag-nya ikut TERCETAK di layar sebagai "<p>…</p>". Yang
                     mengandung tag digambar sebagai .rich; yang polos tetap
                     .lede, karena <p> di dalam <p> bukan HTML yang sah. --}}
                @if($pasarBody !== strip_tags($pasarBody))
                    <div class="rich mx-auto mt-5 max-w-[56ch]">{!! $pasarBody !!}</div>
                @else
                    <p class="lede mx-auto mt-5 max-w-[56ch] text-site-body">{{ $pasarBody }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         PETA & DAFTAR NEGARA
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="pb-20 lg:pb-24">
        <div class="shell">
            @if($exportMarkets->isNotEmpty())
                <x-site.export-map :markets="$exportMarkets" :detailed="true" />
            @else
                <p class="lede rounded-corner border border-dashed border-line px-6 py-20 text-center">
                    {{ $isi('empty', 'site.no_export_markets') }}
                </p>
            @endif
        </div>
    </section>
</div>
