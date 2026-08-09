<div style="font-family: sans-serif; padding: 20px;">
    <h2>Product Categories Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search category..." style="padding: 8px; width: 250px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Add New Category
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>Category Name (EN)</th>
                <th>Slug</th>
                <th>Icon</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td><strong>{{ $cat->translated_name }}</strong></td>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td>
                        @if($cat->getFirstMediaUrl('icon'))
                            <img src="{{ $cat->getFirstMediaUrl('icon') }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        @else
                            {{ $cat->icon ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $cat->sort_order }}</td>
                    <td>
                        <span style="color: {{ $cat->status === 'active' ? 'green' : 'red' }}; font-weight: bold;">
                            {{ strtoupper($cat->status) }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $cat->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $cat->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $categories->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 450px;">
                <h3>{{ $editingId ? 'Edit Category' : 'Add New Category' }}</h3>
                <form wire:submit.prevent="save">
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
                                    <label>Category Name (EN) *</label>
                                    <input type="text" wire:model="name_en" style="width: 100%; padding: 6px;">
                                    @error('name_en') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label>Description (EN)</label>
                                    <textarea wire:model="description_en" rows="3" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                            <!-- Indonesia Tab -->
                            <div style="display: {{ $activeTab === 'id' ? 'block' : 'none' }};">
                                <div style="margin-bottom: 10px;">
                                    <label>Nama Kategori (ID) *</label>
                                    <input type="text" wire:model="name_id" style="width: 100%; padding: 6px;">
                                    @error('name_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label>Deskripsi (ID)</label>
                                    <textarea wire:model="description_id" rows="3" style="width: 100%; padding: 6px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px; background: #f9fafb; padding: 10px; border-radius: 6px;">
                        <label><strong>Category Image/Icon</strong></label>
                        
                        @if($editingId && $existingImage)
                            <div style="margin-top: 10px; margin-bottom: 10px;">
                                <img src="{{ $existingImage }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                                <br>
                                <button type="button" wire:click="deleteImage" wire:confirm="Delete this image?" style="font-size: 10px; background: #ef4444; color: white; border: none; padding: 4px 8px; margin-top: 5px; cursor: pointer; border-radius: 2px;">Delete Image</button>
                            </div>
                        @endif

                        <div style="margin-top: 5px;">
                            <label style="font-size: 12px; color: #6b7280;">Upload New Image (Max 3MB, Auto WebP)</label>
                            <input type="file" wire:model="imageFile" accept="image/*" style="width: 100%; padding: 6px; background: white; border: 1px solid #d1d5db; border-radius: 4px; margin-top: 4px;">
                            <div wire:loading wire:target="imageFile" style="font-size: 12px; color: #2563eb; margin-top: 4px;">Uploading...</div>
                            @error('imageFile') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                        
                        <div style="margin-top: 10px;">
                            <label style="font-size: 12px; color: #6b7280;">Atau Icon Class (opsional, jika tidak upload gambar)</label>
                            <input type="text" wire:model="icon" placeholder="e.g. coffee, leaf" style="width: 100%; padding: 6px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Sort Order</label>
                        <input type="number" wire:model="sort_order" style="width: 100%; padding: 6px;">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
