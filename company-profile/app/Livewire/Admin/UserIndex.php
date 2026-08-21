<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    /*
     * Penyaring peran. Namanya filterRole, bukan selectedRole, karena
     * $selectedRole di bawah sudah dipakai sebagai isian modalnya — satu
     * properti tidak bisa merangkap dua peran: menyunting pengguna bakal ikut
     * menyaring tabelnya.
     */
    public string $filterRole = '';

    public bool $showModal = false;
    public ?string $editingId = null;

    /**
     * Kembali ke halaman satu tiap kali penyaringnya diubah.
     *
     * Tanpa ini, menyaring saat sedang berada di halaman jauh meninggalkan
     * nomor halamannya apa adanya — dan halaman 20 dari hasil yang cuma 3
     * halaman menggambar tabel kosong beserta kalimat "tidak ada yang cocok",
     * padahal hasilnya ada, cuma tidak di halaman itu.
     */
    public function updating($property, $value): void
    {
        if (in_array($property, ['search', 'filterRole'], true)) {
            $this->resetPage();
        }
    }

    // Form Fields
    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public string $selectedRole = 'admin-cms';

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

    /*
     * Kantong galatnya ikut dikosongkan tiap kali modalnya dibuka.
     *
     * Kantong itu bertahan lintas permintaan: sekali percobaan simpan gagal,
     * pesan merahnya masih menempel saat modalnya dibuka lagi untuk pengguna
     * yang lain, padahal isiannya sudah benar.
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $this->resetValidation();

        $user = User::with('roles')->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = null;
        $this->selectedRole = $user->roles->first() ? $user->roles->first()->name : 'admin-cms';
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
        $this->selectedRole = 'admin-cms';
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        $users = User::with('roles')
            /*
             * Kedua syarat pencariannya dikurung sendiri.
             *
             * when() tidak membungkus isinya dalam tanda kurung, jadi tanpa $b
             * ini SQL-nya jadi "(nama cocok) OR email cocok AND peran = ?" —
             * dan AND mengikat lebih erat daripada OR, sehingga penyaring
             * perannya cuma berlaku untuk cabang email.
             */
            ->when($this->search, function ($q) {
                $q->where(function ($b) {
                    $b->where('name', 'LIKE', "%{$this->search}%")
                      ->orWhere('email', 'LIKE', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('name', $this->filterRole));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-index', [
            'users' => $users,
            /*
             * 'permissions' ikut dimuat karena modalnya menampilkan apa saja
             * yang bisa dilakukan peran yang sedang dipilih. Memilih peran
             * tanpa tahu akibatnya adalah cara paling gampang memberi akses
             * yang tidak dimaksudkan.
             */
            'roles' => Role::with('permissions')->withCount('users')->orderBy('name')->get(),
        ]);
    }
}
