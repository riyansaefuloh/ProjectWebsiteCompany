@php
    /* Bahasa dari ruas alamat — alasannya sama dengan halaman 404. */
    $ruas   = request()->segment(1);
    $bahasa = array_key_exists((string) $ruas, config('laravellocalization.supportedLocales', []))
        ? $ruas
        : config('app.locale', 'en');

    app()->setLocale($bahasa);
@endphp
<!DOCTYPE html>
<html lang="{{ $bahasa }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.error_500_eyebrow') }} - {{ config('app.name', 'Coffee Nusantara') }}</title>
    <meta name="robots" content="noindex">

    {{-- ══════════════════════════════════════════════════════════════════
         Halaman ini BERDIRI SENDIRI, dan itu keputusan, bukan kemalasan.

         Halaman 404 memakai tata letak publik lengkap. Halaman ini TIDAK boleh,
         karena tata letak itu memanggil basis data dua kali di dalam <head> —
         Setting::pluck() dan Page::where() — dan memuat CSS lewat manifes Vite.

         Ketiganya justru penyebab paling lazim dari galat 500. Menggambar
         halaman "maaf, ada yang salah" yang bergantung pada hal yang barusan
         rusak berarti melempar galat kedua di tengah penggambaran, dan Laravel
         lalu jatuh ke halaman bawaannya — persis yang mau kita hindari.

         Karena itu: tanpa basis data, tanpa Vite, tanpa huruf dari luar. Yang
         dipakai cuma berkas bahasa (dibaca dari cakram) dan CSS di bawah ini.
         ══════════════════════════════════════════════════════════════════ --}}
    <style>
        :root {
            --kanvas: #fdfaf5;
            --kertas: #f6efe3;
            --forest: #332619;
            --dalam:  #241a11;
            --emas:   #e2a862;
            --tinta:  #1f1810;
            --redup:  #5b4d3e;
            --garis:  #eae0d0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: var(--kanvas);
            color: var(--tinta);
            /* Huruf sistem, bukan Fraunces dan Inter lewat Google Fonts: huruf
               dari luar menuntut jaringan, dan halaman ini harus tetap utuh
               justru ketika ada yang tidak beres. Serif untuk judul supaya
               nadanya tetap sekeluarga dengan situsnya. */
            font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.625;
            -webkit-font-smoothing: antialiased;
        }

        .bingkai { max-width: 38rem; text-align: center; }

        .angka {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(88px, 17vw, 152px);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.04em;
            color: rgba(31, 24, 16, 0.07);
            user-select: none;
            margin: 0;
        }

        .label {
            margin: 16px 0 0;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #905c1b;
        }

        h1 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(29px, 5vw, 42px);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: var(--forest);
            margin: 20px 0 0;
        }

        .isi {
            margin: 20px auto 0;
            max-width: 46ch;
            color: var(--redup);
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 36px;
            height: 40px;
            padding: 0 6px 0 20px;
            border-radius: 999px;
            background: var(--forest);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color .3s;
        }
        .tombol:hover { background: var(--dalam); }

        .tombol span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: var(--emas);
            color: var(--forest);
        }

    </style>
</head>
<body>
    <main class="bingkai">
        <p class="angka" aria-hidden="true">500</p>

        <p class="label">{{ __('site.error_500_eyebrow') }}</p>

        <h1>{{ __('site.error_500_title') }}</h1>

        <p class="isi">{{ __('site.error_500_body') }}</p>

        {{-- url(), bukan route(): satu sambungan lebih sedikit ke bagian
             aplikasi yang mungkin sedang rusak. --}}
        <a class="tombol" href="{{ url('/' . $bahasa) }}">
            {{ __('site.error_back_home') }}
            <span aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </a>

    </main>
</body>
</html>
