<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
    
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:SOFT,WONK,opsz,wght@100,1,9..144,500..700&family=Parisienne&family=Inter:wght@400;600;700&family=Jost:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

<body class="situs flex min-h-screen flex-col bg-canvas text-ink">
    @php
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
