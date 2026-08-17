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
        $brandColor = $globalSettings['brand_color'] ?? '#333';
        $whatsapp = $globalSettings['whatsapp_number'] ?? '';
        $email = $globalSettings['company_email'] ?? $globalSettings['contact_email'] ?? '';
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
