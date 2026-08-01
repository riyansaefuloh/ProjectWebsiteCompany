<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form Fields
    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public string $selectedRole = 'Admin CMS';

    protected function rules(): array
    {
        $emailRule = $this->editingId 
            ? 'required|email|unique:users,email,' . $this->editingId
            : 'required|email|unique:users,email';

        return [
            'name'         => 'required|string|max:100',
            'email'        => $emailRule,
            'password'     => $this->editingId ? 'nullable|string|min:6' : 'required|string|min:6',
            'selectedRole' => 'required|exists:roles,name',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = null;
        $this->selectedRole = $user->roles->first() ? $user->roles->first()->name : 'Admin CMS';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $user = $this->editingId 
            ? User::findOrFail($this->editingId)
            : new User();

        $user->name = $this->name;
        $user->email = $this->email;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        // Assign Spatie Role
        $user->syncRoles([$this->selectedRole]);

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Admin User saved successfully!');
    }

    public function delete(string $id): void
    {
        // Proteksi agar tidak menghapus akun sendiri saat ini
        if ($id === auth()->id()) {
            session()->flash('error', 'Cannot delete your own active account!');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('message', 'User deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = null;
        $this->selectedRole = 'Admin CMS';
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%")
                  ->orWhere('email', 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }
}
