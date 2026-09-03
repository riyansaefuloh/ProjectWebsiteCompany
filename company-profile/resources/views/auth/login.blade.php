@php
    $pengaturan  = \App\Models\Setting::pluck('value', 'key')->toArray();
    $namaPerusahaan = $pengaturan['company_name'] ?? config('app.name');
    $logo    = $pengaturan['logo'] ?? '';
    $favicon = $pengaturan['favicon'] ?? '';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-gutter">
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
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-shell min-h-screen bg-canvas font-ui text-admin-body text-ink">

    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- ══════════════════════════════════════════════════════════════════
             KIRI — formulir
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col p-6 sm:p-10 lg:p-12">

            {{-- Merek: lambang berbingkai + nama perusahaan. --}}
            <div class="flex min-w-0 items-center gap-3">
                
                <span class="flex h-10 w-10 shrink-0 items-center justify-center
                             rounded-control border border-line bg-mist">
                    @if($logo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt=""
                             class="h-6 w-6 object-contain">
                    @else
                        <svg class="h-5 w-5 text-brand" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <path d="M16 3.5c6 0 10.5 5.6 10.5 12.5S22 28.5 16 28.5 5.5 22.9 5.5 16 10 3.5 16 3.5Z"
                                  stroke="currentColor" stroke-width="2"/>
                            <path d="M16 5.2c-3 3-3 6.9 0 10.8s3 7.8 0 10.8"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    @endif
                </span>
                
                <span class="min-w-0">
                    <span class="block truncate text-admin-title text-ink">
                        {{ $namaPerusahaan }}
                    </span>
                    <span class="mt-0.5 block text-admin-overline uppercase text-ink-faint">Panel Admin</span>
                </span>
            </div>
            
            <div class="flex flex-1 items-center justify-center py-12 sm:py-16">
                <div class="w-full max-w-[400px]">
                    
                    <div class="text-center">
                        <h1 class="text-admin-display text-ink">
                            Selamat datang kembali
                        </h1>
                        <p class="mt-3 text-admin-body text-ink-muted">
                            Silakan masuk menggunakan akun Anda untuk mengelola website.
                        </p>
                    </div>
                    
                    @if($errors->any())
                        <div class="mt-10 flex items-start gap-2.5 rounded-control border border-danger/25 bg-danger/5 px-4 py-3"
                             role="alert">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-danger" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6.2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <circle cx="8" cy="11" r="0.9" fill="currentColor"/>
                            </svg>
                            <span class="text-admin-body text-danger">
                                {{ $errors->first() }}
                            </span>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-admin-overline uppercase text-ink-muted">
                                Email <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            
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
                                       class="admin-control py-3 pl-11">
                            </div>
                        </div>

                        {{-- x-data di pembungkusnya, bukan di <input>: tombol
                             matanya perlu ikut membaca keadaan yang sama. --}}
                        <div x-data="{ terlihat: false }">
                            <label for="password" class="block text-admin-overline uppercase text-ink-muted">
                                Kata sandi <span class="text-danger" aria-hidden="true">*</span>
                            </label>

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
                                       class="admin-control py-3 pl-11 pr-12">

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
                                <svg class="h-3.5 w-3.5 text-white opacity-0 transition-opacity"
                                     viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="m3.2 8.4 3.2 3.2 6.4-7" stroke="currentColor"
                                          stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <span class="text-admin-body text-ink-muted">Ingat saya</span>
                        </label>

                        <button type="submit"
                                class="admin-btn admin-btn-brand mt-10 w-full py-3.5 text-admin-title">Masuk</button>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-admin-caption text-ink-faint">
                Copyright &copy; {{ date('Y') }} {{ $namaPerusahaan }}. All rights reserved.
            </p>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             KANAN — kartu peta
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="hidden p-6 lg:block">
            <div class="relative h-full overflow-hidden rounded-panel bg-brand-wash">
                
                <div aria-hidden="true"
                     class="mask-worldmap pointer-events-none absolute -right-12 -top-8
                            h-[74%] w-[130%] max-w-none bg-brand/40"></div>
                
                <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/50"></div>
                
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-[62%]
                            bg-gradient-to-t from-brand-wash via-brand-wash to-transparent"></div>
                
                <div class="relative flex h-full flex-col justify-end p-10 xl:p-12">
                    <h2 class="text-login-hero text-ink">
                        Semua dalam satu panel
                    </h2>

                    <p class="mt-4 max-w-[58ch] text-admin-body text-ink-muted">
                        Kelola kebutuhan website dan inquiry pelanggan dalam satu tempat.
                        Mulai dari informasi produk, sertifikasi, pasar ekspor, berita,
                        hingga permintaan penawaran dari pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
