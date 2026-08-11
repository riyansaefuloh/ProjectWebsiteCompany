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
        $email = $globalSettings['contact_email'] ?? '';
        $address = $globalSettings['company_address'] ?? '';
        $logo = $globalSettings['logo'] ?? '';
        $favicon = $globalSettings['favicon'] ?? '';
        
        // Fetch static pages for footer (excluding hero and about-us which have special handling)
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
    
    @stack('seo')

    <style>
        body { font-family: monospace; padding: 0; margin: 0; background: #fafafa; color: #333; }
        .backend-warning { background: #fee2e2; color: #991b1b; padding: 15px; text-align: center; font-weight: bold; border-bottom: 2px solid #ef4444; }
        .frontend-task { background: #fef08a; color: #854d0e; padding: 10px; border: 1px dashed #ca8a04; margin-bottom: 15px; font-size: 14px; }
        .nav-bar { background: {{ $brandColor }}; padding: 15px; display: flex; gap: 15px; justify-content: center; align-items: center; }
        .nav-bar a { color: white; text-decoration: none; font-weight: bold; }
        .nav-bar a:hover { text-decoration: underline; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .footer { background: #333; color: white; padding: 40px 20px; margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="backend-warning">
        ⚠️ TAMPILAN INI ADALAH KERANGKA (WIREFRAME) BACKEND UNTUK TESTING DATA ⚠️<br>
        <span style="font-weight: normal; font-size: 14px;">[FRONTEND DEVELOPER: Harap ganti struktur HTML ini dan implementasikan desain UI/UX sesuai Figma]</span>
    </div>

    <div class="nav-bar">
        @if($logo)
            <a href="{{ route('home') }}" style="margin-right: 20px;">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="Company Logo" style="height: 40px; object-fit: contain;">
            </a>
        @endif
        @php
            $sectionsSetting = \App\Models\Setting::where('key', 'home_sections')->value('value');
            $homeSectionsArr = $sectionsSetting ? json_decode($sectionsSetting, true) : [];
            
            // Auto-inject downloads if missing from DB
            $hasDownloads = false;
            foreach ($homeSectionsArr as $sec) {
                if ($sec['id'] === 'downloads') $hasDownloads = true;
            }
            if (!$hasDownloads) {
                $homeSectionsArr[] = ["id" => "downloads", "name" => "Catalogs & Downloads", "active" => true, "order" => count($homeSectionsArr) + 1];
            }

            $activeSections = array_filter($homeSectionsArr, fn($sec) => $sec['active'] === true);
            usort($activeSections, fn($a, $b) => $a['order'] <=> $b['order']);
            
            $navLinks = [];
            foreach($activeSections as $sec) {
                switch($sec['id']) {
                    case 'hero': $navLinks[__('site.nav_home')] = route('home'); break;
                    case 'about': $navLinks[__('site.nav_about')] = route('about'); break;
                    case 'products': $navLinks[__('site.nav_products')] = route('products.index'); break;
                    case 'export-markets': $navLinks[__('site.nav_export_markets')] = route('export-markets.index'); break;
                    case 'certifications': $navLinks[__('site.nav_certifications')] = route('certifications.index'); break;
                    case 'gallery': $navLinks[__('site.nav_gallery')] = route('gallery.index'); break;
                    case 'downloads': $navLinks[__('site.nav_downloads')] = route('downloads.index'); break;
                    case 'news': $navLinks[__('site.nav_news')] = route('news.index'); break;
                    case 'contact': $navLinks[__('site.nav_contact')] = route('inquiry.index'); break;
                }
            }
        @endphp

        @foreach($navLinks as $label => $url)
            <a href="{{ $url }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="container">
        {{ $slot }}
    </div>

    <div class="footer">
        <div class="frontend-task">
            [FRONTEND TASK: Rapihkan UI footer ini. Variabel data ($globalSettings) sudah di-*supply* oleh Backend dari Global Settings CMS!]
        </div>
        <div style="margin-bottom: 20px;">
            <strong>{{ __('site.footer_contact') }}:</strong><br>
            {{ __('site.footer_email') }}: {{ $email }} <br>
            {{ __('site.footer_whatsapp') }}: {{ $whatsapp }} <br>
            {{ __('site.footer_address') }}: {{ $address }}
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>{{ __('site.footer_other_info') }}:</strong><br>
            @foreach($staticPages as $p)
                <a href="{{ route('page.show', $p->slug) }}" style="color:#aaa; text-decoration:none; margin:0 10px;">{{ $p->translated_title }}</a>
            @endforeach
        </div>

        <div>
            @if(isset($globalSettings['facebook_url'])) <a href="{{ $globalSettings['facebook_url'] }}" style="color:white; margin:0 5px;">Facebook</a> @endif
            @if(isset($globalSettings['instagram_url'])) <a href="{{ $globalSettings['instagram_url'] }}" style="color:white; margin:0 5px;">Instagram</a> @endif
            @if(isset($globalSettings['linkedin_url'])) <a href="{{ $globalSettings['linkedin_url'] }}" style="color:white; margin:0 5px;">LinkedIn</a> @endif
        </div>
        <p style="margin-top: 30px;">&copy; {{ date('Y') }} {{ $companyName }}. {{ __('site.footer_rights') }}</p>
    </div>

    @livewireScripts
</body>
</html>
