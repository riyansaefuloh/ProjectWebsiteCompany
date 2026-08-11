<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>{{ __('site.page_downloads') }}</h1>
        <p>{{ __('site.page_downloads_sub') }}</p>
        <div class="frontend-task">
            [FRONTEND TASK: Berikan styling Hero Banner khusus halaman Downloads]
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        
        @forelse($downloads as $download)
            <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px;">
                <div>
                    <h3 style="margin: 0 0 10px 0;">📄 {{ $download->title }}</h3>
                    <p style="margin: 0; font-size: 12px; color: #666;">{{ __('site.downloaded_times', ['count' => $download->download_count]) }}</p>
                </div>
                
                <div>
                    @if($download->require_email)
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="email" wire:model="email" placeholder="{{ __('site.enter_email') }}" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <button wire:click="download('{{ $download->id }}')" style="padding: 8px 15px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                {{ __('site.download_pdf_btn') }}
                            </button>
                        </div>
                        @if($selectedDownloadId === $download->id)
                            @error('email') <span style="color:red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                        @endif
                    @else
                        <button wire:click="download('{{ $download->id }}')" style="padding: 8px 15px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            {{ __('site.download_pdf_btn') }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                {{ __('site.no_downloads') }}
            </div>
        @endforelse

    </div>
</div>
