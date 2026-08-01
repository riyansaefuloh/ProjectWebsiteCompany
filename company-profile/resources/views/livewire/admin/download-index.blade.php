<div style="font-family: sans-serif; padding: 20px;">
    <h2>Downloadable Brochures & Catalogs Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search brochure title..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Upload New Brochure PDF
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
                <th>Brochure Title</th>
                <th>Require Email Gate</th>
                <th>Download Count</th>
                <th>Sort Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($downloads as $dl)
                <tr>
                    <td><strong>{{ $dl->title }}</strong></td>
                    <td>
                        <span style="color: {{ $dl->require_email ? 'orange' : 'gray' }}; font-weight: bold;">
                            {{ $dl->require_email ? 'YES (Lead Gate)' : 'NO (Public Direct)' }}
                        </span>
                    </td>
                    <td><strong>{{ $dl->download_count }}</strong> times</td>
                    <td>{{ $dl->sort_order }}</td>
                    <td>
                        <button wire:click="edit('{{ $dl->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        <button wire:click="delete('{{ $dl->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No brochures found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $downloads->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 450px;">
                <h3>{{ $editingId ? 'Edit Brochure' : 'Upload New Brochure PDF' }}</h3>
                <form wire:submit.prevent="save">
                    <div style="margin-bottom: 10px;">
                        <label>Brochure Title *</label>
                        <input type="text" wire:model="title" placeholder="e.g. Coffee Beans Catalog 2026" style="width: 100%; padding: 6px;" required>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>Upload PDF File {{ $editingId ? '(Optional)' : '*' }}</label>
                        <input type="file" wire:model="pdfFile" accept="application/pdf" style="width: 100%; padding: 6px;">
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>Sort Order</label>
                        <input type="number" wire:model="sort_order" style="width: 100%; padding: 6px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" wire:model="require_email">
                            <strong>Require Buyer Email (Lead Capture Gate)</strong>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Brochure</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
