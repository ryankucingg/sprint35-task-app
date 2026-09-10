<?php

namespace App\Livewire\Admin\Profile;

use App\Models\User;
use Livewire\WithFileUploads;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use function MrCatz\DataTable\margin;

class ProfilePage extends MrCatzComponent
{
    use WithFileUploads;

    public $name;
    public $username;
    public $email;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $avatar_file;
    public $avatarUrl;
    public $usernameHint = '';
    public $oldUsername;

    public string $formColumnGap = "0.5rem";

    public function setForm(): array
    {
        return [
            MrCatzFormField::section('Informasi Profil')->asCard(),
            MrCatzFormField::image('avatar_file', label: 'Foto Profil')
                ->span(4)->rowSpan(4)->mobileOrder(-1)
                ->preview($this->avatarUrl, width: 128, height: 128)
                ->margin('mb-4')
                ->previewClass('rounded-full ring ring-primary ring-offset-base-100 ring-offset-2')
                ->fallback($this->name)
                ->onUpload('updateAvatar')
                ->onDelete('deleteAvatar', 'Hapus foto profil?')
                ->hint('JPG, PNG, WEBP. Maks 2MB.'),
            MrCatzFormField::text('name', label: 'Nama Lengkap',
                rules: 'required|max:255',
                icon: 'person',
            )->span(8),
            MrCatzFormField::text('username', label: 'Username',
                rules: 'required|min:3|max:50',
                icon: 'alternate_email',
            )->span(7)->hint($this->usernameHint ?: null, 'success'),
            MrCatzFormField::button('', onClick: 'checkUsernameAvailable', icon: 'search', style: 'info')
                ->withLoading()->span(1),
            MrCatzFormField::email('email', label: 'Email',
                rules: 'required|email|max:255',
                icon: 'mail',
            )->span(8),

            MrCatzFormField::section('Ubah Password')->asCard(),
            MrCatzFormField::note('Kosongkan jika tidak ingin mengubah password.'),
            MrCatzFormField::password('current_password', label: 'Password Saat Ini',
                icon: 'lock',
            ),
            MrCatzFormField::password('password', label: 'Password Baru',
                icon: 'lock',
            )->withConfirmation(label: 'Konfirmasi Password Baru'),
        ];
    }

    public function mount()
    {
        $this->setTitle('Edit Profil');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Edit Profil', 'url' => null],
        ];

        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->oldUsername = $user->username;
        $this->email = $user->email;
        $this->avatarUrl = $user->avatar ? Storage::url($user->avatar) : null;

        session()->put('active', 'admin-profile');
    }

    public function render()
    {
        return view('livewire.admin.profile.profile-page')
            ->layout('components.layouts.admin_layout');
    }

    public function updateAvatar()
    {
        $this->validate([
            'avatar_file' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
        ], [
            'avatar_file.required' => 'Pilih file foto terlebih dahulu.',
            'avatar_file.image' => 'File harus berupa gambar.',
            'avatar_file.mimes' => 'Format: JPG, PNG, JPEG, WEBP.',
            'avatar_file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $user = Auth::user();

        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        $filename = 'user-' . $user->id . '-' . time() . '.' . $this->avatar_file->getClientOriginalExtension();
        $path = $this->avatar_file->storeAs('users', $filename, 'public');

        $user->avatar = $path;
        $user->save();

        $this->avatarUrl = Storage::url($path);
        $this->reset('avatar_file');
        $this->notice('success', 'Foto profil berhasil diperbarui!');
    }

    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        $this->avatarUrl = null;
        $this->notice('success', 'Foto profil berhasil dihapus!');
    }

    public function checkUsernameAvailable()
    {
        $this->usernameHint = '';
        $this->resetValidation('username');

        if (empty($this->username) || strlen($this->username) < 3) {
            $this->addError('username', 'Username minimal 3 karakter.');
            return;
        }

        if ($this->username === $this->oldUsername) {
            $this->usernameHint = '✓ Username tidak berubah.';
            return;
        }

        if (User::where('username', $this->username)->exists()) {
            $this->addError('username', 'Username "' . $this->username . '" sudah digunakan!');
        } else {
            $this->usernameHint = '✓ Username "' . $this->username . '" tersedia!';
        }
    }

    public function save()
    {
        $this->resetErrorBag();

        $this->validate(
            $this->getFormValidationRules(),
            $this->getFormValidationMessages()
        );

        $user = Auth::user();

        if ($this->username !== $this->oldUsername) {
            if (User::where('username', $this->username)->where('id', '!=', $user->id)->exists()) {
                $this->addError('username', 'Username sudah digunakan oleh user lain.');
                return;
            }
        }

        if (User::where('email', $this->email)->where('id', '!=', $user->id)->exists()) {
            $this->addError('email', 'Email sudah digunakan oleh user lain.');
            return;
        }

        if (!empty($this->current_password) || !empty($this->password)) {
            if (empty($this->current_password)) {
                $this->addError('current_password', 'Masukkan password saat ini.');
                return;
            }

            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Password saat ini tidak cocok.');
                return;
            }

            if (empty($this->password)) {
                $this->addError('password', 'Masukkan password baru.');
                return;
            }

            if (strlen($this->password) < 8) {
                $this->addError('password', 'Password baru minimal 8 karakter.');
                return;
            }

            if ($this->password !== $this->password_confirmation) {
                $this->addError('password_confirmation', 'Konfirmasi password tidak cocok.');
                return;
            }

            $user->password = Hash::make($this->password);
        }

        $user->name = $this->name;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->save();

        $this->oldUsername = $this->username;
        $this->usernameHint = '';
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->notice('success', 'Profil berhasil diperbarui!');
    }
}
