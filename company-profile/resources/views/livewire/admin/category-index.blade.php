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
                    <td>{{ $cat->icon ?? '-' }}</td>
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
                    <div style="margin-bottom: 10px;">
                        <label>Category Name (EN) *</label>
                        <input type="text" wire:model="name_en" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Nama Kategori (ID) *</label>
                        <input type="text" wire:model="name_id" style="width: 100%; padding: 6px;" required>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Icon Class / SVG Name</label>
                        <input type="text" wire:model="icon" placeholder="e.g. coffee, leaf" style="width: 100%; padding: 6px;">
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
