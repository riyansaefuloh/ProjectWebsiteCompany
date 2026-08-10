<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>Our Products</h1>
        <p>Explore our premium export-quality products.</p>
        <div class="frontend-task">
            [FRONTEND TASK: Buat header yang indah dengan background pattern atau image]
        </div>
    </div>

    <div style="display: flex; gap: 30px;">
        <!-- Sidebar Filter -->
        <div style="width: 250px; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 6px; height: fit-content;">
            <div class="frontend-task">
                [FRONTEND TASK: Styling form pencarian dan filter dropdown ini agar responsif di mobile]
            </div>
            
            <h3 style="margin-top: 0;">Filters</h3>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Search Products</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, origin, HS code..." style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Category</label>
                <select wire:model.live="category_id" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->translated_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <h3 style="margin-top: 0;">Offline Catalog</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Get our complete product catalog in PDF format.</p>
                <a href="{{ route('download.catalog.form') }}" style="display: block; width: 100%; text-align: center; background: #dc2626; color: white; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold; box-sizing: border-box;">
                    📥 Download PDF Catalog
                </a>
            </div>
        </div>

        <!-- Product Grid -->
        <div style="flex: 1;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
                @forelse($products as $product)
                    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; background: white;">
                        @if($product->getFirstMediaUrl('gallery', 'webp'))
                            <img src="{{ $product->getFirstMediaUrl('gallery', 'webp') }}" alt="{{ $product->translated_name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center;">No Image</div>
                        @endif
                        
                        <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="background: #2563eb; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                {{ $product->category ? $product->category->translated_name : 'Uncategorized' }}
                            </span>
                            @if($product->is_featured)
                                <span style="background: #f59e0b; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">Featured</span>
                            @endif
                        </div>

                        <h3 style="margin: 10px 0 5px 0;">{{ $product->translated_name }}</h3>
                        <p style="font-size: 14px; color: #666; margin: 0;">{{ Str::limit($product->translated_description, 60) }}</p>
                        <a href="{{ route('products.show', $product->slug) }}" style="display: inline-block; margin-top: 10px; color: #2563eb; font-weight: bold;">View Specs &rarr;</a>
                    </div>
                @empty
                    <div style="grid-column: span 3; padding: 40px; text-align: center; border: 1px dashed #ccc; color: #666;">
                        No products found matching your filters.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="frontend-task">
                [FRONTEND TASK: Styling Tailwind/Bootstrap pagination links]
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
