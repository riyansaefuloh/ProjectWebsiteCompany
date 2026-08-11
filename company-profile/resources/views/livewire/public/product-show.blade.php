<div>
    @push('seo')
    <script type="application/ld+json">
    {!! json_encode(\App\Services\JsonLdService::productSchema($product), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    <div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
        <div class="frontend-task" style="margin-bottom: 20px;">
            [FRONTEND TASK: Desain halaman detail produk. Buat layout Grid/Flexbox di mana galeri foto ada di sebelah kiri dan deskripsi + tombol CTA ada di sebelah kanan.]
        </div>

        <a href="{{ route('products.index') }}" style="color: #2563eb; text-decoration: none;">{{ __('site.back_to_products') }}</a>

        <div style="display: flex; gap: 40px; margin-top: 20px;">
            <!-- Kolom Kiri: Galeri WebP -->
            <div style="flex: 1;">
                <div class="frontend-task" style="margin-bottom: 10px;">
                    [FRONTEND TASK: Buat image slider/carousel interaktif di sini untuk galeri produk]
                </div>
                
                @php $media = $product->getMedia('gallery'); @endphp
                @if($media->count() > 0)
                    <!-- Gambar Utama -->
                    <img src="{{ $media[0]->getUrl('webp') }}" alt="{{ $product->translated_name }}" style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                    
                    <!-- Thumbnail -->
                    <div style="display: flex; gap: 10px; margin-top: 10px; overflow-x: auto;">
                        @foreach($media->skip(1) as $image)
                            <img src="{{ $image->getUrl('webp') }}" alt="thumbnail" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; cursor: pointer;">
                        @endforeach
                    </div>
                @else
                    <div style="width: 100%; height: 400px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 8px;">{{ __('site.no_image_available') }}</div>
                @endif
            </div>

            <!-- Kolom Kanan: Detail & Specs -->
            <div style="flex: 1;">
                <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                    {{ $product->category ? $product->category->translated_name : __('site.uncategorized') }}
                </span>
                
                <h1 style="margin-top: 10px; margin-bottom: 5px;">{{ $product->translated_name }}</h1>
                <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                    {{ __('site.hs_code') }}: {{ $product->hs_code ?? '-' }} | {{ __('site.origin') }}: {{ $product->origin ?? '-' }}
                </p>

                <!-- Tombol CTA -->
                <div style="margin-bottom: 30px;">
                    <a href="{{ route('inquiry.index', ['product_id' => $product->id]) }}" style="display: inline-block; padding: 12px 25px; background: #10b981; color: white; text-decoration: none; font-weight: bold; border-radius: 6px;">
                        {{ __('site.request_quotation') }}
                    </a>
                </div>

                <h3>{{ __('site.description') }}</h3>
                <p style="line-height: 1.6; color: #444;">{{ $product->translated_description }}</p>

                @if($product->specifications->count() > 0)
                    <h3 style="margin-top: 30px;">{{ __('site.specifications') }}</h3>
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                        @foreach($product->specifications as $spec)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; background: #f8fafc; width: 40%; font-weight: bold;">{{ $spec->translated_name }}</td>
                                <td style="padding: 10px;">{{ $spec->translated_value }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                @if($product->certifications->count() > 0)
                    <h3 style="margin-top: 30px;">{{ __('site.certifications') }}</h3>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach($product->certifications as $cert)
                            <span style="border: 1px solid #cbd5e1; padding: 5px 10px; border-radius: 4px; font-size: 12px; background: #f1f5f9;">
                                🏆 {{ $cert->translated_name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
