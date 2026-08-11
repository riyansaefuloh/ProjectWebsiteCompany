<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>{{ __('site.page_gallery') }}</h1>
        <p>{{ __('site.page_gallery_sub') }}</p>
        <div class="frontend-task">
            [FRONTEND TASK: Berikan styling Hero Banner khusus halaman Gallery]
        </div>
    </div>

    <div style="max-width: 1200px; margin: 0 auto;">
        <div class="frontend-task" style="margin-bottom: 30px;">
            [FRONTEND TASK: Buat Photo Grid masonry/lightbox. Looping album dan fotonya dari data backend di bawah ini.]
        </div>

        @forelse($galleries as $gallery)
            <div style="margin-bottom: 40px; padding: 20px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                <h2 style="margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px;">{{ $gallery->name }}</h2>
                
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 20px;">
                    @forelse($gallery->items as $item)
                        @if($item->type === 'video')
                            @php
                                $embedUrl = $item->video_url;
                                // Simple logic to convert youtube watch link to embed link with autoplay & mute & NO CONTROLS
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $item->video_url, $matches)) {
                                    $videoId = $matches[1];
                                    $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?autoplay=1&mute=1&loop=1&playlist=' . $videoId . '&controls=0&modestbranding=1&rel=0&playsinline=1';
                                }
                            @endphp
                            <div style="border: 1px solid #ccc; border-radius: 4px; overflow: hidden; height: 160px;">
                                <iframe src="{{ $embedUrl }}" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
                            </div>
                        @else
                            @if($item->getFirstMediaUrl('gallery', 'webp'))
                                <div style="border: 1px solid #ccc; padding: 5px; border-radius: 4px;">
                                    <img src="{{ $item->getFirstMediaUrl('gallery', 'webp') }}" alt="Gallery Image" style="width: 100%; height: 150px; object-fit: cover;">
                                </div>
                            @else
                                <div style="border: 1px solid #ccc; padding: 5px; border-radius: 4px; display: flex; align-items: center; justify-content: center; height: 150px; background: #eee; font-size: 12px;">{{ __('site.no_image') }}</div>
                            @endif
                        @endif
                    @empty
                        <p style="grid-column: span 4; color: #888;">{{ __('site.no_photos_in_album') }}</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                {{ __('site.no_galleries') }}
            </div>
        @endforelse
    </div>
</div>
