<div style="font-family: sans-serif; padding: 20px;">
    <h2>Admin Users & Role Permissions Management</h2>

    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <input type="text" wire:model.live="search" placeholder="Search user name or email..." style="padding: 8px; width: 300px;">
        <button wire:click="create" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            + Add New Admin User
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background-color: #fef2f2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>User Name</th>
                <th>Email Address</th>
                <th>Assigned Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $usr)
                <tr>
                    <td><strong>{{ $usr->name }}</strong></td>
                    <td>{{ $usr->email }}</td>
                    <td>
                        <span style="background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                            {{ $usr->roles->pluck('name')->implode(', ') ?: 'No Role' }}
                        </span>
                    </td>
                    <td>
                        <button wire:click="edit('{{ $usr->id }}')" style="padding: 4px 8px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Edit</button>
                        @if($usr->id !== auth()->id())
                            <button wire:click="delete('{{ $usr->id }}')" wire:confirm="Are you sure?" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $users->links() }}
    </div>

    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; padding: 25px; border-radius: 8px; width: 450px;">
                <h3>{{ $editingId ? 'Edit Admin User' : 'Add New Admin User' }}</h3>
                <form wire:submit.prevent="save">
                    <div style="margin-bottom: 10px;">
                        <label>Full Name *</label>
                        <input type="text" wire:model="name" style="width: 100%; padding: 6px;" required>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>Email Address *</label>
                        <input type="email" wire:model="email" style="width: 100%; padding: 6px;" required>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label>Password {{ $editingId ? '(Leave blank to keep current)' : '*' }}</label>
                        <input type="password" wire:model="password" style="width: 100%; padding: 6px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label>Assign Role *</label>
                        <select wire:model="selectedRole" style="width: 100%; padding: 6px;" required>
                            @foreach($roles as $rl)
                                <option value="{{ $rl->name }}">{{ $rl->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                        <button type="button" wire:click="$set('showModal', false)" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
