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
    
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif
    
    <script type="application/ld+json">
    {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ══════════════════════════════════════════════════════════════════
         GOOGLE ANALYTICS

         ID-nya sudah lama bisa diisi dari panel, tapi tidak pernah dipasang
         di mana pun — jadi isian itu tersimpan rapi sambil tidak melacak
         apa-apa. Hanya digambar kalau ID-nya benar-benar terisi.

         Hanya di tata letak publik: kunjungan staf ke panel admin bukan lalu
         lintas pengunjung, dan mencampurnya membuat angkanya menipu.
         ══════════════════════════════════════════════════════════════════ --}}
    {{-- Blok penuh, BUKAN bentuk sebaris berkurung. Bentuk sebarisnya gagal
         mencocokkan tanda kurung untuk ungkapan yang memuat ?? '' lalu
         terkompilasi jadi tag PHP yang tidak pernah ditutup — menelan seluruh
         sisa berkas ini dan mematikan setiap halaman publik.

         Jangan pula menulis nama arahan Blade apa pun di dalam komentar ini:
         Blade tetap mengompilasinya walau terletak di dalam tanda komentar. --}}
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

<body class="flex min-h-screen flex-col bg-canvas text-ink">
    <x-site.header :company-name="$companyName" :logo="$logo" />

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
