<div>
    @push('seo')
    <script type="application/ld+json">
    {!! json_encode(\App\Services\JsonLdService::articleSchema($news), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <div class="frontend-task" style="margin-bottom: 20px;">
            [FRONTEND TASK: Styling halaman baca artikel berita. Gunakan Tailwind Typography (prose) untuk me-render HTML dari TinyMCE agar tabel dan format paragraf rapi.]
        </div>

        <a href="{{ route('news.index') }}" style="color: #2563eb; text-decoration: none;">&larr; Back to News List</a>

        <h1 style="margin-top: 20px; font-size: 32px;">{{ $news->translated_title }}</h1>
        
        <div style="font-size: 14px; color: #666; margin-bottom: 20px; display: flex; gap: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
            <span>📅 {{ $news->published_at ? $news->published_at->format('F d, Y') : '' }}</span>
            <span>👤 {{ $news->author ? $news->author->name : 'Admin' }}</span>
        </div>

        @if($news->getFirstMediaUrl('covers'))
            <img src="{{ $news->getFirstMediaUrl('covers', 'webp') }}" alt="{{ $news->translated_title }}" style="width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 30px;">
        @endif

        <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #eee; line-height: 1.6;">
            <!-- Output dari TinyMCE -->
            {!! $news->translated_content !!}
        </div>
    </div>
</div>
