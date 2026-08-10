<div>
    @if(empty($homeSections))
        <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; margin: 30px;">
            <p>No Home Sections are active. Please configure them in Global Settings.</p>
        </div>
    @endif

    @foreach($homeSections as $section)
        @switch($section['id'])
            @case('hero')
                <!-- HERO SECTION -->
                <div style="background: #e2e8f0; padding: 60px 40px; text-align: center; margin-bottom: 30px; border-bottom: 5px solid #3b82f6;">
                    @if(isset($heroPage))
                        <h1 style="font-size: 2.5rem; margin-bottom: 10px; color: #1e293b;">{{ $heroPage->translated_title }}</h1>
                        <div style="font-size: 1.2rem; color: #475569; max-width: 800px; margin: 0 auto;">
                            {!! $heroPage->translated_content !!}
                        </div>
                    @else
                        <h2>Welcome to Our Company</h2>
                        <p>Please create a Static Page with the title "Hero" in the Admin CMS to populate this banner.</p>
                    @endif
                    <div class="frontend-task" style="margin-top:20px;">
                        [FRONTEND TASK: Hias struktur teks dinamis di atas menjadi Slider/Carousel dengan gambar latar (background image).]
                    </div>
                </div>
                @break

            @case('products')
                <!-- FEATURED PRODUCTS SECTION -->
                <div style="margin-bottom: 50px; border-left: 5px solid #10b981; padding-left: 20px;">
                    <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px;">{{ $section['name'] }}</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Buat Product Grid yang estetis (Responsive). Data sudah siap dari Backend (6 limit).]
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        @forelse($featuredProducts as $product)
                            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; background: white;">
                                @if($product->getFirstMediaUrl('gallery', 'webp'))
                                    <img src="{{ $product->getFirstMediaUrl('gallery', 'webp') }}" alt="{{ $product->translated_name }}" style="width: 100%; height: 200px; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center;">No Image</div>
                                @endif
                                <h3 style="margin: 10px 0 5px 0;">{{ $product->translated_name }}</h3>
                                <p style="font-size: 14px; color: #666; margin: 0;">{{ Str::limit($product->translated_description, 60) }}</p>
                                <a href="{{ route('products.show', $product->slug) }}" style="display: inline-block; margin-top: 10px; color: #2563eb;">View Details &rarr;</a>
                            </div>
                        @empty
                            <p>No featured products found.</p>
                        @endforelse
                    </div>
                </div>
                @break

            @case('export_markets')
                <!-- EXPORT MARKETS SECTION -->
                <div style="margin-bottom: 50px; background: #f8fafc; padding: 30px; border-radius: 8px; border-left: 5px solid #f59e0b;">
                    <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px;">{{ $section['name'] }}</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Ganti list statis ini menjadi Interactive Vector Map (misal jVectorMap). Highlight negara berdasarkan country_code di bawah ini.]
                    </div>
                    
                    <ul style="display: flex; gap: 15px; flex-wrap: wrap; padding: 0; list-style: none;">
                        @foreach($exportMarkets as $market)
                            <li style="background: white; border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 20px;">
                                <strong>{{ $market->country_code }}</strong> - {{ $market->translated_name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @break

            @case('news')
                <!-- LATEST NEWS SECTION -->
                <div style="margin-bottom: 50px; border-left: 5px solid #8b5cf6; padding-left: 20px;">
                    <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px;">{{ $section['name'] }}</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Buat News Cards yang elegan. Data sudah siap dengan limit 3.]
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        @forelse($latestNews as $news)
                            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; background: white;">
                                @if($news->getFirstMediaUrl('covers', 'webp'))
                                    <img src="{{ $news->getFirstMediaUrl('covers', 'webp') }}" alt="{{ $news->translated_title }}" style="width: 100%; height: 150px; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 150px; background: #eee; display: flex; align-items: center; justify-content: center;">No Image</div>
                                @endif
                                <h4 style="margin: 10px 0 5px 0;">{{ $news->translated_title }}</h4>
                                <p style="font-size: 12px; color: #888; margin-bottom: 5px;">{{ $news->published_at ? $news->published_at->format('M d, Y') : '' }}</p>
                                <a href="{{ route('news.show', $news->slug) }}" style="font-size: 14px; color: #2563eb;">Read Article &rarr;</a>
                            </div>
                        @empty
                            <p>No news found.</p>
                        @endforelse
                    </div>
                </div>
                @break

            @default
                <!-- OTHER SECTIONS -->
                <div style="margin-bottom: 50px; padding: 20px; border: 1px dashed #94a3b8; background: #f1f5f9;">
                    <h2>[{{ $section['name'] }}]</h2>
                    <div class="frontend-task">
                        [FRONTEND TASK: Buat UI untuk seksi {{ $section['name'] }} di sini.]
                    </div>
                </div>
                @break
        @endswitch
    @endforeach
</div>
