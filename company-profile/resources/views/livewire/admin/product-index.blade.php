<div style="font-family: sans-serif; padding: 20px;">
    <h2>Export Products Management</h2>

    <!-- Top Action & Search -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <input type="text" wire:model.live="search" placeholder="Search by name, HS Code..." style="padding: 8px; width: 250px;">
            <select wire:model.live="selectedCategory" style="padding: 8px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Add New Export Product
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Table Products -->
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>Product Name (EN)</th>
                <th>Category</th>
                <th>HS Code</th>
                <th>MOQ</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <strong>{{ $product->translated_name }}</strong>
                        @if($product->is_featured)
                            <span style="background:#fef08a; color:#854d0e; font-size:10px; padding:2px 6px; border-radius:4px;">★ Featured</span>
                        @endif
                    </td>
                    <td>{{ $product->category ? $product->category->translated_name : '-' }}</td>
                    <td><code>{{ $product->hs_code }}</code></td>
                    <td>{{ $product->moq }}</td>
                    <td>{{ $product->supply_capacity }}</td>
                    <td>
                        <span style="color: {{ $product->status === 'published' ? 'green' : 'gray' }}; font-weight: bold;">
                            {{ strtoupper($product->status) }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $product->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $product->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $products->links() }}
    </div>

    <!-- Modal Form (Inline Simpel) -->
    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 650px; max-height: 90vh; overflow-y: auto;">
                <h3>{{ $editingId ? 'Edit Product' : 'Add New Export Product' }}</h3>
                <form wire:submit.prevent="save">
                    
                    <div style="margin-bottom: 10px;">
                        <label>Category *</label>
                        <select wire:model="category_id" style="width: 100%; padding: 6px;" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tabs for Language -->
                    <div style="margin-bottom: 15px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                        <div style="display: flex; background: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
                            <button type="button" wire:click="$set('activeTab', 'en')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'en' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'en' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'en' ? 'bold' : 'normal' }};">English (EN)</button>
                            <button type="button" wire:click="$set('activeTab', 'id')" style="flex: 1; padding: 10px; border: none; background: {{ $activeTab === 'id' ? 'white' : 'transparent' }}; border-bottom: {{ $activeTab === 'id' ? '2px solid #2563eb' : 'none' }}; cursor: pointer; font-weight: {{ $activeTab === 'id' ? 'bold' : 'normal' }};">Indonesia (ID)</button>
                        </div>
                        <div style="padding: 15px; background: white;">
                            <!-- English Tab -->
                            <div style="display: {{ $activeTab === 'en' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Product Name (EN) *</label>
                                    <input type="text" wire:model="name_en" style="width: 100%; padding: 6px;">
                                    @error('name_en') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label>Description (EN)</label>
                                    <textarea wire:model="description_en" rows="4" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                            <!-- Indonesia Tab -->
                            <div style="display: {{ $activeTab === 'id' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Nama Produk (ID) *</label>
                                    <input type="text" wire:model="name_id" style="width: 100%; padding: 6px;">
                                    @error('name_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label>Deskripsi (ID)</label>
                                    <textarea wire:model="description_id" rows="4" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label>HS Code *</label>
                            <input type="text" wire:model="hs_code" style="width: 100%; padding: 6px;" required>
                        </div>
                        <div style="flex: 1;">
                            <label>MOQ *</label>
                            <input type="text" wire:model="moq" placeholder="e.g. 1 x 20ft container" style="width: 100%; padding: 6px;" required>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label>Supply Capacity *</label>
                            <input type="text" wire:model="supply_capacity" placeholder="e.g. 50 tons / month" style="width: 100%; padding: 6px;" required>
                        </div>
                        <div style="flex: 1;">
                            <label>Packaging *</label>
                            <input type="text" wire:model="packaging" placeholder="e.g. Jute bag 60kg" style="width: 100%; padding: 6px;" required>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label>Origin *</label>
                            <input type="text" wire:model="origin" style="width: 100%; padding: 6px;" required>
                        </div>
                        <div style="flex: 1;">
                            <label>Incoterms *</label>
                            <input type="text" wire:model="incoterms" style="width: 100%; padding: 6px;" required>
                        </div>
                    </div>

                    <!-- Dynamic Key-Value Specifications Repeater -->
                    <div style="margin-bottom: 15px; background: #f9fafb; padding: 10px; border-radius: 6px;">
                        <label><strong>Technical Specifications (Dynamic Repeater)</strong></label>
                        @foreach($specifications as $index => $spec)
                            <div style="display: flex; gap: 10px; margin-top: 5px;">
                                <input type="text" wire:model="specifications.{{ $index }}.key" placeholder="Key (e.g. Moisture)" style="flex: 1; padding: 4px;">
                                <input type="text" wire:model="specifications.{{ $index }}.value" placeholder="Value (e.g. Max 12%)" style="flex: 1; padding: 4px;">
                                <button type="button" wire:click="removeSpecification({{ $index }})" style="background: red; color: white; border: none; padding: 4px 8px; cursor: pointer;">X</button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addSpecification" style="margin-top: 8px; padding: 4px 8px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            + Add Specification
                        </button>
                    </div>

                    <!-- Product Images -->
                    <div style="margin-bottom: 15px; background: #f9fafb; padding: 10px; border-radius: 6px;">
                        <label><strong>Product Images (Gallery)</strong></label>
                        
                        @if($editingId && count($existingMedia) > 0)
                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; margin-top: 10px;">
                                @foreach($existingMedia as $media)
                                    <div style="position: relative; border: 1px solid #ccc; padding: 4px; border-radius: 4px; width: 120px; text-align: center; background: white;">
                                        <img src="{{ $media->getUrl() }}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 2px;">
                                        @if($media->getCustomProperty('is_cover'))
                                            <div style="position: absolute; top: 0; left: 0; background: #eab308; color: white; font-size: 10px; padding: 2px 4px; font-weight: bold;">COVER</div>
                                        @endif
                                        <div style="margin-top: 5px; display: flex; flex-direction: column; gap: 4px;">
                                            @if(!$media->getCustomProperty('is_cover'))
                                                <button type="button" wire:click="setCoverMedia({{ $media->id }})" style="font-size: 10px; background: #2563eb; color: white; border: none; padding: 4px; cursor: pointer; border-radius: 2px;">Set Cover</button>
                                            @endif
                                            <button type="button" wire:click="deleteMedia({{ $media->id }})" wire:confirm="Delete this image?" style="font-size: 10px; background: #ef4444; color: white; border: none; padding: 4px; cursor: pointer; border-radius: 2px;">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div style="margin-top: 5px;">
                            <label style="font-size: 12px; color: #6b7280;">Upload New Images (Multiple allowed, max 3MB each, WebP auto-conversion)</label>
                            <input type="file" wire:model="imageFiles" multiple accept="image/*" style="width: 100%; padding: 6px; background: white; border: 1px solid #d1d5db; border-radius: 4px; margin-top: 4px;">
                            <div wire:loading wire:target="imageFiles" style="font-size: 12px; color: #2563eb; margin-top: 4px;">Uploading...</div>
                            @error('imageFiles.*') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Related Certifications Checkbox -->
                    <div style="margin-bottom: 15px;">
                        <label><strong>Linked Certifications:</strong></label><br>
                        @foreach($certifications as $cert)
                            <label style="margin-right: 15px;">
                                <input type="checkbox" wire:model="selectedCertifications" value="{{ $cert->id }}">
                                {{ $cert->translated_name }}
                            </label>
                        @endforeach
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label>Status</label>
                            <select wire:model="status" style="width: 100%; padding: 6px;">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div style="flex: 1; display: flex; align-items: center; margin-top: 15px;">
                            <label>
                                <input type="checkbox" wire:model="is_featured">
                                <strong>Featured Product</strong>
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
