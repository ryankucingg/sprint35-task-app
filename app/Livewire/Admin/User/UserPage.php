<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserPage extends MrCatzComponent
{
    public $name;
    public $username;
    public $email;
    public $role;
    public $password;
    public $password_confirmation;
    public $oldUsername;
    public $usernameHint = '';

    public function setForm(): array
    {
        return [
            MrCatzFormField::text('name', label: 'Nama Lengkap',
                rules: 'required|max:255',
                messages: ['required' => 'Silahkan masukan nama (maks 255 karakter)'],
                icon: 'person',
            ),
            MrCatzFormField::text('username', label: 'Username',
                rules: 'required|min:3|max:50',
                messages: ['required' => 'Username harus 3-50 karakter'],
                icon: 'alternate_email',
            )->disabled(fn() => $this->isEdit)->span(11)
              ->hint($this->usernameHint ?: null, 'success'),
            MrCatzFormField::button('', onClick: 'checkUsernameAvailable', icon: 'search', style: 'info')
                ->withLoading()->span(1)
                ->hideWhen(fn() => $this->isEdit),
            MrCatzFormField::email('email', label: 'Email',
                rules: 'required|email|max:255',
                messages: ['required' => 'Silahkan masukan email yang valid'],
                icon: 'mail',
            ),
            MrCatzFormField::select('role', label: 'Role',
                data: [
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'super-admin', 'label' => 'Super Admin'],
                ],
                value: 'value',
                option: 'label',
                rules: 'required|in:admin,super-admin',
                messages: ['required' => 'Silahkan pilih role yang valid'],
            ),
            MrCatzFormField::password('password', label: $this->isEdit ? 'Password (kosongkan jika tidak diubah)' : 'Password',
                icon: 'lock',
            )->withConfirmation(label: 'Konfirmasi Password'),
        ];
    }

    public function mount()
    {
        $this->setTitle('User');
        session()->put('active', 'admin-users');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'User Management', 'url' => null]
        ];
    }

    public function render()
    {
        return view('livewire.admin.user.user-page')->layout('components.layouts.admin_layout');
    }

    public function prepareAddData()
    {
        $this->form_title = "Tambah User";
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = "";
        $this->username = "";
        $this->email = "";
        $this->role = "admin";
        $this->password = "";
        $this->password_confirmation = "";
        $this->oldUsername = null;
        $this->usernameHint = "";
    }

    public function prepareEditData($data)
    {
        $this->id = $data['id'];
        $this->form_title = "Edit User";
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = $data['name'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->role = $data['role'];
        $this->password = "";
        $this->password_confirmation = "";
        $this->oldUsername = $data['username'];
    }

    public function prepareDeleteData($data)
    {
        $this->id = $data['id'];
        $this->form_title = "Hapus User";
        $this->deleted_text = $data['username'] . ' (' . $data['name'] . ')';
    }

    public function saveData()
    {
        $this->validate(
            $this->getFormValidationRules(),
            $this->getFormValidationMessages()
        );

        if ($this->role === 'super-admin' && !Auth::user()->isSuperAdmin()) {
            $this->show_notif('error', 'Hanya Super Admin yang dapat menetapkan role Super Admin!');
            return;
        }

        if (!$this->isEdit && empty($this->password)) {
            $this->addError('password', 'Password wajib diisi untuk user baru');
            return;
        }

        if (!empty($this->password)) {
            if (strlen($this->password) < 8) {
                $this->addError('password', 'Password minimal 8 karakter');
                return;
            }
            if ($this->password !== $this->password_confirmation) {
                $this->addError('password_confirmation', 'Konfirmasi password tidak cocok');
                return;
            }
        }

        if (!$this->checkUsername()) return;
        if (!$this->checkEmail()) return;

        if ($this->isEdit) {
            $user = User::find($this->id);

            if ($user->isSuperAdmin() && !Auth::user()->isSuperAdmin()) {
                $this->show_notif('error', 'Anda tidak memiliki izin untuk mengedit Super Admin!');
                return;
            }

            $user->name = $this->name;
            $user->username = $this->username;
            $user->email = $this->email;
            $user->role = $this->role;
            if (!empty($this->password)) {
                $user->password = Hash::make($this->password);
            }
            $user->save();

            Log::info('User updated', [
                'updated_by' => Auth::id(),
                'target_user' => $user->id,
                'username' => $user->username,
            ]);

            $this->dispatch_to_view(true, 'update');
        } else {
            $user = new User();
            $user->name = $this->name;
            $user->username = $this->username;
            $user->email = $this->email;
            $user->role = $this->role;
            $user->password = Hash::make($this->password);
            $insert = $user->save();

            Log::info('User created', [
                'created_by' => Auth::id(),
                'new_user' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ]);

            $this->dispatch_to_view($insert, 'insert');
        }
    }

    public function onInlineUpdate($rowData, $columnKey, $newValue)
    {
        User::where('id', $rowData['id'])->update([$columnKey => $newValue]);
        $this->dispatch_to_view(true, 'update');
    }

    public function dropData()
    {
        $user = User::find($this->id);

        if (!$user) {
            $this->show_notif('error', 'User tidak ditemukan!');
            return;
        }

        if (Auth::id() === $user->id) {
            $this->show_notif('error', 'Tidak dapat menghapus akun yang sedang Anda pakai!');
            return;
        }

        if ($user->isSuperAdmin() && !Auth::user()->isSuperAdmin()) {
            $this->show_notif('error', 'Anda tidak memiliki izin untuk menghapus Super Admin!');
            return;
        }

        Log::info('User deleted', [
            'deleted_by' => Auth::id(),
            'target_user' => $user->id,
            'username' => $user->username,
        ]);

        $delete = $user->delete();
        $this->dispatch_to_view($delete, 'delete');
    }

    public function dropBulkData($selectedRows)
    {
        if (empty($selectedRows)) return;

        $currentUserId = Auth::id();
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        if (in_array((string) $currentUserId, $selectedRows)) {
            $this->show_notif('error', 'Tidak dapat menghapus akun yang sedang Anda pakai!');
            return;
        }

        if (!$isSuperAdmin) {
            $hasSuperAdmin = User::whereIn('id', $selectedRows)
                ->where('role', 'super-admin')
                ->exists();

            if ($hasSuperAdmin) {
                $this->show_notif('error', 'Anda tidak memiliki izin untuk menghapus Super Admin!');
                return;
            }
        }

        $count = User::whereIn('id', $selectedRows)->delete();

        Log::info('Bulk user deleted', [
            'deleted_by' => $currentUserId,
            'count' => $count,
            'user_ids' => $selectedRows,
        ]);

        $this->dispatch('refresh-data', [
            'status' => true,
            'text' => $count . ' user berhasil dihapus!'
        ]);
    }

    public function checkUsernameAvailable()
    {
        $this->usernameHint = '';
        $this->resetValidation('username');

        if (empty($this->username) || strlen($this->username) < 3) {
            $this->addError('username', 'Username minimal 3 karakter');
            return;
        }

        $exists = User::where('username', $this->username)->exists();
        if ($exists) {
            $this->addError('username', 'Username "' . $this->username . '" sudah digunakan!');
        } else {
            $this->usernameHint = '✓ Username "' . $this->username . '" tersedia!';
        }
    }

    private function checkUsername()
    {
        if ($this->oldUsername != null && $this->username == $this->oldUsername) {
            return true;
        }

        if (User::where('username', $this->username)->exists()) {
            $this->addError('username', 'Username sudah digunakan!');
            return false;
        }
        return true;
    }

    private function checkEmail()
    {
        $query = User::where('email', $this->email);
        if ($this->isEdit) {
            $query->where('id', '!=', $this->id);
        }
        if ($query->exists()) {
            $this->addError('email', 'Email sudah digunakan!');
            return false;
        }
        return true;
    }
}
