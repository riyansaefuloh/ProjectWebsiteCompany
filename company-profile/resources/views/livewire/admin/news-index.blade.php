<div style="font-family: sans-serif; padding: 20px;">
    <h2>News & Articles Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search news title or content..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Write New Article
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
                <th>Title (EN)</th>
                <th>Author</th>
                <th>Published At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($newsList as $news)
                <tr>
                    <td><strong>{{ $news->translated_title }}</strong></td>
                    <td>{{ $news->author ? $news->author->name : 'System' }}</td>
                    <td>{{ $news->published_at ? $news->published_at->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <span style="color: {{ $news->status === 'published' ? 'green' : 'gray' }}; font-weight: bold;">
                            {{ strtoupper($news->status) }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $news->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $news->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No articles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $newsList->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 600px; max-height: 90vh; overflow-y: auto;">
                <h3>{{ $editingId ? 'Edit Article' : 'Write New Article' }}</h3>
                <form wire:submit.prevent="save">
                    <div style="margin-bottom: 10px;">
                        <label>Article Title (EN) *</label>
                        <input type="text" wire:model="title_en" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Judul Artikel (ID) *</label>
                        <input type="text" wire:model="title_id" style="width: 100%; padding: 6px;" required>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>Content (EN) *</label>
                        <textarea wire:model="content_en" rows="5" style="width: 100%; padding: 6px;" required></textarea>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Isi Berita (ID) *</label>
                        <textarea wire:model="content_id" rows="5" style="width: 100%; padding: 6px;" required></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label>Status</label>
                            <select wire:model="status" style="width: 100%; padding: 6px;">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label>Published Schedule</label>
                            <input type="datetime-local" wire:model="published_at" style="width: 100%; padding: 6px;">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Article</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
