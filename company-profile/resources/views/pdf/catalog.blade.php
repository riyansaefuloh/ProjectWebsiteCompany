<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Product Catalogue - {{ $companyName }}</title>
    <style>
        /* ══════════════════════════════════════════════════════════════════
           Palet Karamel Sangrai, sama dengan situsnya. Yang lama memakai biru
           Tailwind bawaan — #2563eb, #1e3a8a, #1d4ed8 — yang tidak muncul di
           satu pun halaman situs ini.

           Huruf DejaVu, bukan Helvetica: DejaVu satu-satunya keluarga bawaan
           DomPDF yang Unicode penuh, dan nilai spesifikasi di sini memakai
           tanda pisah en (1.200 – 1.600 mdpl). Serif untuk judul supaya
           nadanya sekeluarga dengan Fraunces di situs.
           ══════════════════════════════════════════════════════════════════ */
        @page { margin: 26mm 16mm 22mm; }

        body {
            margin: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.5pt;
            line-height: 1.55;
            color: #5b4d3e;
        }

        /* ── Kepala, hanya di halaman pertama ─────────────────────────── */
        .masthead {
            background-color: #332619;
            color: #ffffff;
            padding: 18px 20px 16px;
            margin-bottom: 22px;
        }
        .masthead .nama {
            font-family: "DejaVu Serif", serif;
            font-size: 19pt;
            font-weight: bold;
            letter-spacing: -0.4pt;
            margin: 0;
            color: #ffffff;
        }
        .masthead .kelopak {
            font-size: 7.5pt;
            letter-spacing: 1.6pt;
            text-transform: uppercase;
            color: #e2a862;
            margin: 7px 0 0;
        }
        .masthead .kontak {
            font-size: 7.5pt;
            color: #d8c9b2;
            margin: 12px 0 0;
            padding-top: 10px;
            border-top: 1px solid #5b4d3e;
        }

        /* ── Kartu produk ─────────────────────────────────────────────── */
        .produk {
            border: 1px solid #eae0d0;
            background-color: #fdfaf5;
            padding: 13px 15px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        /* Tabel, bukan float: DomPDF tidak menghitung tinggi float dengan
           benar, dan kartu berfoto akan saling tumpang tindih. */
        .baris { width: 100%; border-collapse: collapse; }
        .baris td { vertical-align: top; padding: 0; border: none; }
        .kolom-foto { width: 108px; padding-right: 14px !important; }
        .kolom-foto img { width: 108px; height: 108px; object-fit: cover; }

        .judul {
            font-family: "DejaVu Serif", serif;
            font-size: 12.5pt;
            font-weight: bold;
            color: #332619;
            margin: 0 0 2px;
            line-height: 1.25;
        }
        .kategori {
            font-size: 7pt;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #905c1b;
            margin: 0 0 8px;
        }
        .ringkas { margin: 0 0 10px; font-size: 9pt; }

        /* ── Tabel spesifikasi ────────────────────────────────────────── */
        .spek { width: 100%; border-collapse: collapse; margin-top: 2px; }
        .spek td {
            border-bottom: 1px solid #eae0d0;
            padding: 3px 8px 3px 0;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .spek td.kunci {
            width: 27%;
            color: #332619;
            font-weight: bold;
        }
        .spek td.pemisah { width: 4%; border-bottom: none; }
        .spek tr:last-child td { border-bottom: none; }

        .harga {
            margin-top: 9px;
            padding: 6px 10px;
            background-color: #f6efe3;
            font-size: 9pt;
            color: #332619;
        }
        .harga strong { color: #905c1b; }

        .sertifikat {
            margin-top: 9px;
            font-size: 7.5pt;
            letter-spacing: 0.8pt;
            text-transform: uppercase;
            color: #905c1b;
        }

        /* ── Kaki, terulang di tiap halaman ───────────────────────────── */
        .kaki {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            border-top: 1px solid #eae0d0;
            padding-top: 7px;
            font-size: 7.5pt;
            color: #5b4d3e;
        }
        .kaki table { width: 100%; border-collapse: collapse; }
        .kaki td { border: none; padding: 0; }
        .kaki .kanan { text-align: right; }
        .kaki .nomor:after { content: counter(page); }
    </style>
</head>
<body>

    {{-- Kaki dinyatakan lebih dulu: DomPDF menggambar unsur position:fixed
         pada tiap halaman terhitung dari tempat ia DIDEKLARASIKAN, jadi yang
         ditaruh di akhir badan hanya muncul di halaman terakhir. --}}
    <div class="kaki">
        <table>
            <tr>
                <td>{{ $companyName }}@if($kontak['email']) &nbsp;·&nbsp; {{ $kontak['email'] }}@endif</td>
                <td class="kanan"><span class="nomor">Page </span></td>
            </tr>
        </table>
    </div>

    <div class="masthead">
        <p class="nama">{{ $companyName }}</p>
        <p class="kelopak">Export Product Catalogue &nbsp;&middot;&nbsp; {{ $date }}</p>

        @if($kontak['email'] || $kontak['whatsapp'] || $kontak['alamat'])
            <p class="kontak">
                @if($kontak['alamat']){{ $kontak['alamat'] }}@endif
                @if($kontak['email']) &nbsp;&middot;&nbsp; {{ $kontak['email'] }}@endif
                @if($kontak['whatsapp']) &nbsp;&middot;&nbsp; {{ $kontak['whatsapp'] }}@endif
            </p>
        @endif
    </div>

    @foreach($products as $product)
        @php
            $fotoIni = $foto[$product->id] ?? null;

            /* Spesifikasi bebas dari panel — Processing, Altitude, Moisture
               Content, dan seterusnya. Relasinya sudah lama ikut dimuat tapi
               tidak pernah digambar; pembeli justru menilai kopi dari angka
               inilah. Disaring per bahasa supaya baris ID tidak ikut muncul di
               katalog berbahasa Inggris. */
            $spekBebas = $product->specifications
                ->where('locale', app()->getLocale())
                ->take(4);

            /* Baris tetap, hanya yang benar-benar terisi. Yang lama menggambar
               keenam barisnya apa pun isinya, jadi katalog penuh sel kosong. */
            $spekTetap = array_filter([
                'HS Code'         => $product->hs_code,
                'MOQ'             => $product->moq,
                'Supply Capacity' => $product->supply_capacity,
                'Packaging'       => $product->packaging,
                'Origin'          => $product->origin,
                'Incoterms'       => $product->incoterms,
            ], fn ($nilai) => filled($nilai));

            $sertifikat = $product->certifications->pluck('translated_name')->filter();
        @endphp

        <div class="produk">
            <table class="baris">
                <tr>
                    @if($fotoIni)
                        <td class="kolom-foto"><img src="{{ $fotoIni }}" alt=""></td>
                    @endif

                    <td>
                        <p class="judul">{{ $product->translated_name }}</p>

                        @if($product->category)
                            <p class="kategori">{{ $product->category->translated_name }}</p>
                        @endif

                        @if($product->translated_description)
                            <p class="ringkas">{{ \Illuminate\Support\Str::limit(strip_tags($product->translated_description), 190, '…', preserveWords: true) }}</p>
                        @endif
                    </td>
                </tr>
            </table>

            @if($spekTetap || $spekBebas->isNotEmpty())
                @php
                    /* Dua lajur pasangan kunci–nilai berdampingan, bukan satu
                       lajur panjang: kartunya jadi separuh tinggi, dan katalog
                       enam produk muat di dua halaman alih-alih empat. */
                    $semua = $spekTetap;
                    foreach ($spekBebas as $s) { $semua[$s->spec_key] = $s->spec_value; }
                    $pasangan = array_chunk($semua, 2, true);
                @endphp

                <table class="spek">
                    @foreach($pasangan as $pasang)
                        @php $isi = array_values($pasang); $kunci = array_keys($pasang); @endphp
                        <tr>
                            <td class="kunci">{{ $kunci[0] }}</td>
                            <td>{{ $isi[0] }}</td>
                            <td class="pemisah"></td>
                            <td class="kunci">{{ $kunci[1] ?? '' }}</td>
                            <td>{{ $isi[1] ?? '' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if($product->indicative_price)
                <p class="harga">
                    Indicative price &nbsp; <strong>{{ $product->currency }} {{ number_format($product->indicative_price, 2) }}</strong>
                </p>
            @endif

            @if($sertifikat->isNotEmpty())
                <p class="sertifikat">Certified &nbsp;&middot;&nbsp; {{ $sertifikat->implode(' · ') }}</p>
            @endif
        </div>
    @endforeach

</body>
</html>
