@php
    $pengaturan  = \App\Models\Setting::pluck('value', 'key')->toArray();
    $namaPerusahaan = $pengaturan['company_name'] ?? config('app.name');
    $logo    = $pengaturan['logo'] ?? '';
    $favicon = $pengaturan['favicon'] ?? '';

    // Tautan kebijakan privasi hanya dipasang kalau halamannya memang ada dan
    // sudah terbit — kaki halaman yang menautkan ke 404 lebih buruk daripada
    // kaki halaman tanpa tautan.
    $halamanPrivasi = \App\Models\Page::where('slug', 'privacy-policy')
        ->where('status', 'published')
        ->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk · {{ $namaPerusahaan }}</title>

    @if($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Plus Jakarta Sans untuk seluruh antarmuka panel; Nunito hanya
         tersisa untuk nama perusahaan, yang merupakan lambang merek dan
         disusun sama dengan header situs publik. --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Nunito:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-mist p-4 font-ui text-ink sm:p-6 lg:p-8">

    {{-- Kartu luar dengan bantalan sendiri (p-3). Bantalan itulah yang membuat
         panel hijau di kanan tampak melayang dengan jarak sama di keempat
         sisinya, tanpa perlu satu pun ukuran yang ditulis manual. --}}
    <div class="mx-auto flex min-h-[calc(100vh-2rem)] max-w-[1240px] items-center sm:min-h-[calc(100vh-3rem)] lg:min-h-[calc(100vh-4rem)]">
        <div class="w-full overflow-hidden rounded-panel border border-line bg-canvas p-3
                    shadow-[0_24px_60px_-32px_rgba(26,29,27,0.28)]">

            <div class="grid lg:grid-cols-2">

                {{-- ══════════════════════════════════════════════════════════
                     KIRI — formulir
                     ══════════════════════════════════════════════════════════ --}}
                <div class="flex flex-col px-5 py-6 sm:px-10 sm:py-8 lg:px-12 lg:py-10">

                    {{-- Merek: logo + nama perusahaan, disusun sama persis
                         dengan header situs publik supaya panel admin terbaca
                         sebagai bagian dari situs yang sama. --}}
                    <div class="flex min-w-0 items-center gap-2.5">
                        @if($logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                                 class="h-9 w-auto shrink-0 object-contain">
                        @else
                            <svg class="h-8 w-8 shrink-0 text-brand" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                <path d="M16 3.5c6 0 10.5 5.6 10.5 12.5S22 28.5 16 28.5 5.5 22.9 5.5 16 10 3.5 16 3.5Z"
                                      stroke="currentColor" stroke-width="2"/>
                                <path d="M16 5.2c-3 3-3 6.9 0 10.8s3 7.8 0 10.8"
                                      stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        @endif

                        <span class="truncate font-display text-[18px] font-extrabold tracking-[-0.02em] text-ink">
                            {{ $namaPerusahaan }}
                        </span>
                    </div>

                    {{-- Formulir ditengahkan tegak di ruang yang tersisa antara
                         merek dan kaki halaman. --}}
                    <div class="flex flex-1 items-center justify-center py-10 sm:py-14">
                        <div class="w-full max-w-[400px]">

                            <div class="text-center">
                                {{-- font-ui ditulis langsung: aturan h1 di layer base
                                     memaksa --font-display, dan kelas utilitas inilah yang
                                     menimpanya. --}}
                                <h1 class="font-ui text-[28px] font-bold leading-[1.2] tracking-[-0.02em] text-ink sm:text-[32px]">
                                    Selamat Datang Kembali
                                </h1>
                                <p class="mt-3 text-[14px] leading-relaxed text-ink-muted">
                                    Masukkan email dan kata sandi untuk mengakses akun Anda.
                                </p>
                            </div>

                            {{-- Galat kredensial datang dari kontroler dengan kunci
                                 'email'. Ditampilkan sebagai satu pemberitahuan di
                                 atas formulir, bukan menempel di kolom email —
                                 penyebabnya bisa jadi kata sandinya, dan menuding
                                 kolom yang salah membuat orang mengetik ulang
                                 email yang sebenarnya sudah benar. --}}
                            @if($errors->any())
                                <div class="mt-7 flex items-start gap-2.5 rounded-control border border-danger/25 bg-danger/5 px-4 py-3"
                                     role="alert">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-danger" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                        <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        <circle cx="8" cy="11" r="0.9" fill="currentColor"/>
                                    </svg>
                                    <span class="text-[13px] leading-relaxed text-danger">
                                        {{ $errors->first() }}
                                    </span>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                                @csrf

                                <div>
                                    <label for="email" class="field-label">Email</label>

                                    {{-- Lebar rongga ikon (w-11) sengaja sama persis dengan
                                         bantalan kiri kolomnya (pl-11). Kalau keduanya beda,
                                         ikonnya terbaca melenceng dari tengah rongga. --}}
                                    <div class="relative mt-2">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11
                                                     items-center justify-center text-ink-faint">
                                            <svg class="h-[18px] w-[18px]" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <rect x="2.4" y="4.4" width="15.2" height="11.2" rx="2"
                                                      stroke="currentColor" stroke-width="1.4"/>
                                                <path d="m3.2 5.6 6.8 5 6.8-5" stroke="currentColor"
                                                      stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>

                                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                                               required autofocus autocomplete="email"
                                               placeholder="nama@perusahaan.com"
                                               class="field mt-0 pl-11">
                                    </div>
                                </div>

                                {{-- x-data di pembungkusnya, bukan di <input>: tombol
                                     matanya perlu ikut membaca keadaan yang sama. --}}
                                <div x-data="{ terlihat: false }">
                                    <label for="password" class="field-label">Kata Sandi</label>

                                    <div class="relative mt-2">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11
                                                     items-center justify-center text-ink-faint">
                                            <svg class="h-[18px] w-[18px]" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <rect x="3.6" y="8.6" width="12.8" height="8.4" rx="2"
                                                      stroke="currentColor" stroke-width="1.4"/>
                                                <path d="M6.8 8.6V6.8a3.2 3.2 0 0 1 6.4 0v1.8"
                                                      stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        {{-- type="password" tetap ditulis di markup, bukan
                                             hanya diikat Alpine: sebelum Alpine sempat hidup,
                                             input tanpa type akan jatuh ke text — dan kata
                                             sandinya sempat terbaca di layar. --}}
                                        <input id="password" type="password" name="password"
                                               x-bind:type="terlihat ? 'text' : 'password'"
                                               required autocomplete="current-password"
                                               placeholder="••••••••"
                                               class="field mt-0 pl-11 pr-12">

                                        <button type="button" x-on:click="terlihat = ! terlihat"
                                                x-bind:aria-label="terlihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                                aria-label="Tampilkan kata sandi"
                                                class="absolute inset-y-0 right-0 flex w-12 items-center justify-center
                                                       text-ink-faint transition-colors hover:text-ink">
                                            {{-- Dua ikon, yang tidak aktif disembunyikan —
                                                 bukan satu ikon yang jalurnya diubah, supaya
                                                 keadaan tertutupnya tetap terbaca tanpa JS. --}}
                                            <svg x-show="! terlihat" class="h-[18px] w-[18px]" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z"
                                                      stroke="currentColor" stroke-width="1.4"/>
                                                <circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.4"/>
                                            </svg>

                                            <svg x-show="terlihat" x-cloak class="h-[18px] w-[18px]" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z"
                                                      stroke="currentColor" stroke-width="1.4"/>
                                                <circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.4"/>
                                                <path d="m3.6 3.6 12.8 12.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Kotak centangnya asli, cuma disembunyikan secara
                                     visual: papan ketik dan pembaca layar tetap bekerja
                                     apa adanya. Kontroler membacanya lewat
                                     $request->boolean('remember'). --}}
                                <label for="remember" class="flex w-fit cursor-pointer items-center gap-2.5">
                                    <input id="remember" type="checkbox" name="remember" value="1"
                                           @checked(old('remember')) class="peer sr-only">

                                    <span class="flex h-[18px] w-[18px] shrink-0 items-center justify-center
                                                 rounded-[5px] border border-line-strong bg-canvas transition-colors
                                                 peer-checked:border-brand peer-checked:bg-brand
                                                 peer-checked:[&_svg]:opacity-100
                                                 peer-focus-visible:outline peer-focus-visible:outline-2
                                                 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand">
                                        <svg class="h-3 w-3 text-white opacity-0 transition-opacity"
                                             viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <path d="m3.2 8.4 3.2 3.2 6.4-7" stroke="currentColor"
                                                  stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>

                                    <span class="text-[13px] text-ink-muted">Ingat saya</span>
                                </label>

                                <button type="submit" class="btn btn-brand w-full">Masuk</button>
                            </form>
                        </div>
                    </div>

                    {{-- Kaki halaman --}}
                    <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2 text-[13px] text-ink-faint">
                        <p>&copy; {{ date('Y') }} {{ $namaPerusahaan }}</p>

                        @if($halamanPrivasi)
                            <a href="{{ route('page.show', $halamanPrivasi->slug) }}"
                               class="underline-offset-4 transition-colors hover:text-ink hover:underline">
                                Kebijakan Privasi
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     KANAN — panel merek

                     Terang, bukan bidang warna pekat. Hijau merek muncul di
                     petanya saja; latarnya memakai brand-wash — rona hijau
                     paling pucat di palet — sehingga peta hijau punya tempat
                     berpijak tanpa dua hijau saling bertabrakan.

                     Disembunyikan di bawah lg: di layar sempit ia hanya akan
                     mendorong formulirnya jauh ke bawah lipatan.
                     ══════════════════════════════════════════════════════════ --}}
                <div class="relative hidden overflow-hidden rounded-corner bg-brand-wash lg:block">

                    {{-- Peta dipasang sebagai masker (lihat .mask-worldmap di
                         app.css), bukan sebagai <img>: warnanya jadi datang
                         dari bg-brand/40 di bawah ini, sehingga hijaunya persis
                         hijau merek.

                         Digeser ke ATAS (bukan ditengahkan): bagian terpadat
                         petanya jadi menempati separuh atas panel, dan tidak
                         lagi berebut ruang dengan kalimat di bawahnya.

                         aria-hidden: ini tekstur, bukan data. Ia tidak mengaku
                         menunjukkan negara tujuan yang sebenarnya. --}}
                    <div aria-hidden="true"
                         class="mask-worldmap pointer-events-none absolute -right-12 -top-8
                                h-[74%] w-[130%] max-w-none bg-brand/40"></div>

                    {{-- Bulatan putih lembut di sudut kiri atas, memberi
                         kedalaman pada bidang terang yang lebar. --}}
                    <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/50"></div>

                    {{-- Selubung dari bening ke warna panel.
                         Peta berhenti memudar tepat sebelum kalimatnya dimulai,
                         sehingga teks berpijak pada bidang yang tenang — bukan
                         di atas ribuan titik yang membuat hurufnya terbaca kotor. --}}
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-[62%]
                                bg-gradient-to-t from-brand-wash via-brand-wash to-transparent"></div>

                    {{-- Kalimatnya berlabuh di BAWAH, bukan melayang di tengah.
                         Sudut yang punya tepi memberi teks tempat berdiri; di
                         tengah bidang kosong ia tidak berpangkal pada apa pun
                         dan terbaca seperti keterangan yang tercecer. --}}
                    <div class="relative flex h-full flex-col justify-end px-12 pb-14 xl:px-14">
                        <p class="eyebrow">Panel Admin</p>

                        <h2 class="mt-5 max-w-[15ch] font-ui text-[32px] font-semibold leading-[1.15] tracking-[-0.025em] text-ink xl:text-[38px]">
                            Satu tempat untuk seluruh isi situs.
                        </h2>

                        <p class="mt-5 max-w-[34ch] text-[15px] leading-relaxed text-ink-muted">
                            Kelola produk, sertifikasi, pasar ekspor, dan berita yang tampil
                            di situs {{ $namaPerusahaan }} — serta permintaan penawaran yang masuk.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
