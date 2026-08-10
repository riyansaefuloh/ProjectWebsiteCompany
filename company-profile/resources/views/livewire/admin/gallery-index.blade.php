<div style="font-family: sans-serif; padding: 20px;">
    <h2>Manage Galleries</h2>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <button wire:click="create" style="padding: 10px 15px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Create New Gallery Album
        </button>
        <input type="text" wire:model.live="search" placeholder="Search galleries..." style="padding: 8px; margin-left: 10px;">
    </div>

    @if($isOpen)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; padding: 20px;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 800px; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">{{ $gallery_id ? 'Edit Gallery Album & Media' : 'Create Gallery Album' }}</h3>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                </div>
                
                <!-- If Editing, show existing media grid -->
                @if($editingGallery && $editingGallery->items->count() > 0)
                    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">Existing Media ({{ $editingGallery->items->count() }} items)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px;">
                            @foreach($editingGallery->items as $item)
                                <div style="border: 1px solid #d1d5db; border-radius: 6px; overflow: hidden; background: white; text-align: center; position: relative;">
                                    @if($item->type === 'video')
                                        <div style="height: 90px; display: flex; align-items: center; justify-content: center; background: #1f2937; color: white;">
                                            🎥 Video
                                        </div>
                                        <a href="{{ $item->video_url }}" target="_blank" style="font-size: 10px; display: block; padding: 5px; color: #2563eb; text-decoration: none; word-break: break-all;">View Link</a>
                                    @else
                                        @if($item->getFirstMediaUrl('gallery', 'thumb'))
                                            <img src="{{ $item->getFirstMediaUrl('gallery', 'thumb') }}" style="width: 100%; height: 90px; object-fit: cover;">
                                        @endif
                                    @endif
                                    <button wire:click="deleteItem('{{ $item->id }}')" wire:confirm="Delete this media item?" style="width: 100%; padding: 6px; background: #ef4444; color: white; border: none; font-size: 11px; cursor: pointer;">
                                        Delete
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="store">
                    
                    <div style="margin-bottom: 15px;">
                        <label><strong>Album Name *</strong></label>
                        <input type="text" wire:model="name" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                        @error('name') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <div style="flex: 1; border: 1px solid #d1d5db; padding: 15px; border-radius: 6px;">
                            <label><strong>Add Video Embed</strong> (Optional)</label><br>
                            <span style="font-size: 12px; color: #6b7280;">Insert a YouTube or Vimeo URL.</span>
                            <input type="url" wire:model="videoUrl" placeholder="https://youtube.com/..." style="width: 100%; padding: 8px; margin-top: 10px;">
                            @error('videoUrl') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div style="flex: 1; border: 1px solid #d1d5db; padding: 15px; border-radius: 6px;">
                            <label><strong>Upload Photos</strong> (Multiple)</label><br>
                            <span style="font-size: 12px; color: #6b7280;">Auto-converted to WebP.</span>
                            <input type="file" wire:model="photos" multiple accept="image/*" style="width: 100%; padding: 8px; margin-top: 10px;">
                            @error('photos.*') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="photos" style="font-size: 12px; color: #2563eb; margin-top: 5px;">Uploading...</div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" wire:click="closeModal" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            {{ $gallery_id ? 'Done' : 'Cancel' }}
                        </button>
                        <button type="submit" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            {{ $gallery_id ? 'Save & Add Media' : 'Create Gallery' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background: #e5e7eb; text-align: left;">
                <th style="padding: 10px; border: 1px solid #d1d5db;">Album Name</th>
                <th style="padding: 10px; border: 1px solid #d1d5db;">Photos Count</th>
                <th style="padding: 10px; border: 1px solid #d1d5db;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($galleries as $gallery)
                <tr>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">{{ $gallery->name }}</td>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">{{ $gallery->items->count() }} photos</td>
                    <td style="padding: 10px; border: 1px solid #d1d5db;">
                        <button wire:click="edit('{{ $gallery->id }}')" style="padding: 5px 10px; background: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit / Add Photos</button>
                        <button wire:click="delete('{{ $gallery->id }}')" wire:confirm="Are you sure you want to delete this entire album?" style="padding: 5px 10px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="padding: 10px; border: 1px solid #d1d5db; text-align: center;">No galleries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $galleries->links() }}
    </div>
</div>
