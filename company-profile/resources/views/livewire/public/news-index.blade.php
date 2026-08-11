<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>{{ __('site.page_news') }}</h1>
        <p>{{ __('site.page_news_sub') }}</p>
        <div class="frontend-task">
            [FRONTEND TASK: Buat header yang indah dengan background pattern atau image]
        </div>
    </div>

    <div style="margin-bottom: 20px; text-align: right;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('site.search_news') }}" style="padding: 8px; width: 300px; max-width: 100%;">
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 40px;">
        @forelse($news as $article)
            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; background: white;">
                @if($article->getFirstMediaUrl('covers', 'webp'))
                    <img src="{{ $article->getFirstMediaUrl('covers', 'webp') }}" alt="{{ $article->translated_title }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">
                @else
                    <div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">{{ __('site.no_image') }}</div>
                @endif
                
                <h3 style="margin: 15px 0 10px 0;">{{ $article->translated_title }}</h3>
                <div style="font-size: 12px; color: #888; margin-bottom: 10px; display: flex; gap: 15px;">
                    <span>📅 {{ $article->published_at ? $article->published_at->format('M d, Y') : '' }}</span>
                    <span>👤 {{ $article->author ? $article->author->name : __('site.by_author') }}</span>
                </div>
                
                <p style="font-size: 14px; color: #666; margin-bottom: 15px;">{{ Str::limit($article->translated_excerpt, 100) }}</p>
                <a href="{{ route('news.show', $article->slug) }}" style="display: inline-block; padding: 8px 15px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">{{ __('site.read_article') }}</a>
            </div>
        @empty
            <div style="grid-column: span 3; padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                {{ __('site.no_news_found') }}
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="frontend-task">
        [FRONTEND TASK: Styling Tailwind/Bootstrap pagination links]
    </div>
    <div>
        {{ $news->links() }}
    </div>
</div>
