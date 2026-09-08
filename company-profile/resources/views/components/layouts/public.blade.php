<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- SEO Meta Tags: Title, Description, Canonical, OG, Twitter Card --}}
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
    @livewireStyles
    @php
        $globalSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $companyName = $globalSettings['company_name'] ?? config('app.name', 'Export Company');
        $whatsapp = $globalSettings['whatsapp_number'] ?? '';
        $email = $globalSettings['contact_email'] ?? $globalSettings['company_email'] ?? '';
        $address = $globalSettings['company_address'] ?? '';
        $logo = $globalSettings['logo'] ?? '';
        $favicon = $globalSettings['favicon'] ?? '';
        
        $staticPages = \App\Models\Page::whereNotIn('slug', ['hero', 'about-us'])
            ->where('status', 'published')
            ->get();
            
        // Get global Organization JSON-LD Schema
        $organizationSchema = \App\Services\JsonLdService::organizationSchema();
    @endphp
    
    {{-- Ikon tab SELALU dinyatakan, termasuk ketika tidak ada yang diunggah.

         Halaman yang tidak menyebut ikon sama sekali membuat peramban jatuh ke
         /favicon.ico, lalu mempertahankan ikon yang terakhir dikenalnya untuk
         asal ini. Akibatnya favicon yang sudah dihapus tetap tampak di tab —
         bukan karena aplikasinya masih menyimpannya, melainkan karena tidak ada
         yang menggantikannya, dan itu membaca seperti penghapusan yang gagal.

         Pernyataan yang eksplisit memutus itu: peramban memakai apa yang
         disebutkan, bukan apa yang diingatnya. --}}
    @if($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @else
        <link rel="icon" type="image/svg+xml"
              href="{{ \App\Support\Monogram::favicon($companyName) }}">
    @endif
    
    <script type="application/ld+json">
    {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Empat rupa, empat tugas — keterangannya di app.css. Bobot yang
         diminta sengaja sedikit; bobot yang DIPAKAI tapi tidak dimuat akan
         ditebalkan sendiri oleh peramban, dan hasilnya huruf yang melar.
         Parisienne karena itu tidak boleh dipanggil dengan bobot apa pun.
         SOFT dan WONK dipatok di URL, bukan lewat font-variation-settings:
         rupa yang dikirim Google sudah membawa sumbunya tertanam. --}}
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:SOFT,WONK,opsz,wght@100,1,9..144,500..700&family=Parisienne&family=Inter:wght@400;600;700&family=Jost:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ══ GOOGLE ANALYTICS — hanya digambar kalau ID-nya benar-benar terisi,
         dan hanya di tata letak publik: kunjungan staf ke panel bukan lalu
         lintas pengunjung. ══ --}}
    {{-- Blok @php penuh, BUKAN bentuk sebaris berkurung: bentuk sebarisnya
         gagal mencocokkan kurung untuk ungkapan ber-?? lalu terkompilasi jadi
         tag PHP yang tidak pernah ditutup — menelan sisa berkas dan mematikan
         SETIAP halaman publik. --}}
    @php
        $gaId = trim($globalSettings['google_analytics_id'] ?? '');
    @endphp

    @if($gaId !== '')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($gaId));
        </script>
    @endif

    @stack('seo')
</head>

{{-- .situs menimpa nilai token warna & huruf untuk seluruh keturunannya.
     Satu kelas, dan hanya di sini — panel admin berdiri di layout lain dan
     tidak ikut berubah. Hapus kelasnya, situs kembali ke rupa lamanya. --}}
<body class="situs flex min-h-screen flex-col bg-canvas text-ink">
    @php
        /* Bilah kepala hanya boleh mengambang kalau ada bidang GELAP di
           bawahnya. Yang menyediakannya cuma hero beranda — dan hero itu bisa
           dimatikan atau dipindah urutannya dari panel. Kalau ia bukan bagian
           teratas, huruf putih akan jatuh di atas krem dan lenyap.

           Dibaca dari $globalSettings yang sudah dimuat di atas, bukan lewat
           kueri baru. Larik kosong berarti susunan bawaan, dan di sana hero
           memang teratas. */
        $heroDiPuncak = false;

        if (request()->routeIs('home')) {
            $bagian = array_filter(
                json_decode($globalSettings['home_sections'] ?? '[]', true) ?: [],
                fn ($s) => ($s['active'] ?? false) === true && isset($s['id'])
            );

            if (empty($bagian)) {
                $heroDiPuncak = true;
            } else {
                usort($bagian, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
                $heroDiPuncak = str_replace('-', '_', reset($bagian)['id']) === 'hero';
            }
        }
    @endphp

    <x-site.header :company-name="$companyName" :logo="$logo" :over-hero="$heroDiPuncak" />

    @php
        $designedRoutes = [
            'home', 'products.index', 'products.show',
            'about', 'certifications.index', 'export-markets.index',
            'news.index', 'news.show', 'inquiry.index',
            'gallery.index', 'downloads.index', 'page.show',
            'download.catalog.form',
        ];
    @endphp

    <main id="main" class="flex-1">
        @if(request()->routeIs($designedRoutes))
            {{ $slot }}
        @else
            <div class="shell py-12">{{ $slot }}</div>
        @endif
    </main>

    <x-site.footer :company-name="$companyName" :settings="$globalSettings" :static-pages="$staticPages" />

    @livewireScripts
</body>
</html>
