<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AuthenticationSettings extends Component
{
    public bool $loginEnabled = false;

    // User management
    public bool $showUserModal = false;
    public ?int $editingUserId = null;
    public string $newUserName = '';
    public string $newUserEmail = '';
    public string $newUserPassword = '';
    public string $newUserRole = 'editor';

    public function mount(SettingsService $settingsService): void
    {
        $this->loginEnabled = $settingsService->isLoginEnabled();
    }

    public function updatedLoginEnabled(SettingsService $settingsService): void
    {
        // Check if at least one administrator exists when enabling login
        if ($this->loginEnabled) {
            $adminCount = User::where('role', 'administrator')->count();
            if ($adminCount === 0) {
                $this->loginEnabled = false;
                session()->flash('error', __('At least one administrator account must exist before enabling login.'));
                return;
            }
        }

        $settingsService->setLoginEnabled($this->loginEnabled);
        session()->flash('success', __('Login setting updated successfully!'));
    }

    public function openUserModal(): void
    {
        $this->editingUserId = null;
        $this->showUserModal = true;
        $this->newUserName = '';
        $this->newUserEmail = '';
        $this->newUserPassword = '';
        $this->newUserRole = 'editor';
    }

    public function closeUserModal(): void
    {
        $this->showUserModal = false;
        $this->editingUserId = null;
        $this->newUserName = '';
        $this->newUserEmail = '';
        $this->newUserPassword = '';
        $this->newUserRole = 'editor';
    }

    public function editUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->newUserName = $user->name;
        $this->newUserEmail = $user->email;
        $this->newUserPassword = '';
        $this->newUserRole = $user->role;
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        $rules = [
            'newUserName' => 'required|string|max:255',
            'newUserEmail' => 'required|email|max:255|unique:users,email,' . ($this->editingUserId ?? 'NULL'),
            'newUserRole' => 'required|in:editor,administrator',
        ];

        if ($this->editingUserId) {
            // Editing existing user - password is optional
            if (!empty($this->newUserPassword)) {
                $rules['newUserPassword'] = 'min:8';
            }
        } else {
            // Creating new user - password is required
            $rules['newUserPassword'] = 'required|min:8';
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            // Update existing user
            $user = User::findOrFail($this->editingUserId);
            $user->name = $this->newUserName;
            $user->email = $this->newUserEmail;
            $user->role = $this->newUserRole;

            if (!empty($this->newUserPassword)) {
                $user->password = Hash::make($this->newUserPassword);
            }

            $user->save();

            session()->flash('success', __('User updated successfully!'));
        } else {
            // Create new user
            User::create([
                'name' => $this->newUserName,
                'email' => $this->newUserEmail,
                'password' => Hash::make($this->newUserPassword),
                'role' => $this->newUserRole,
            ]);

            session()->flash('success', __('User created successfully!'));
        }

        $this->closeUserModal();
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Prevent deleting the last administrator
        if ($user->isAdministrator()) {
            $adminCount = User::where('role', 'administrator')->count();
            if ($adminCount <= 1) {
                session()->flash('error', __('Cannot delete the last administrator account.'));
                return;
            }
        }

        // Prevent deleting yourself
        if (auth()->check() && auth()->id() === $userId) {
            session()->flash('error', __('You cannot delete your own account.'));
            return;
        }

        $user->delete();
        session()->flash('success', __('User deleted successfully!'));
    }

    public function render()
    {
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('livewire.settings.authentication-settings', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}

