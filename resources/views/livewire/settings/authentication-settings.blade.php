<div>
    <!-- Page Header -->
    <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
            <div class="w-full flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('settings') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800/50 hover:bg-gray-700/50 text-gray-300 hover:text-white rounded-lg transition-all duration-200 border border-gray-700/50 hover:border-gray-600/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-medium">{{ __('Back') }}</span>
                    </a>
                    <h2 class="font-bold text-2xl text-white leading-tight">
                        {{ __('Authentication') }}
                    </h2>
                </div>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-2xl p-4 backdrop-blur-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-green-400 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if (session()->has('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-2xl p-4 backdrop-blur-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <p class="text-red-400 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Authentication Settings -->
            <div class="bg-gray-900/50 backdrop-blur-xl border border-gray-800/50 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-800/50">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-white">{{ __('Login Settings') }}</h3>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Enable Login Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
                            <div class="flex-1">
                                <label for="loginEnabled" class="block text-sm font-medium text-gray-300">
                                    {{ __('Enable Login') }}
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Require users to log in to access the application') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="loginEnabled" id="loginEnabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- User Management (always visible) -->
                        <div class="border-t border-gray-800/50 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-medium text-white">{{ __('Users') }}</h4>
                                <button
                                    type="button"
                                    wire:click="openUserModal"
                                    class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('Add User') }}
                                </button>
                            </div>

                            <!-- Info message when no admin exists -->
                            @if(!$loginEnabled && \App\Models\User::where('role', 'administrator')->count() === 0)
                                <div class="mb-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-yellow-400 text-sm">
                                            {{ __('At least one administrator account must exist before enabling login.') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Users List -->
                            <div class="space-y-2">
                                @forelse($users as $user)
                                    <div class="flex items-center justify-between p-3 bg-gray-800/30 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $user->isAdministrator() ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-gray-700/50 text-gray-300 border border-gray-600/30' }}">
                                                {{ $user->isAdministrator() ? __('Administrator') : __('Editor') }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="editUser({{ $user->id }})"
                                                class="p-2 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 rounded-lg transition"
                                                title="{{ __('Edit') }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="{{ __('Are you sure you want to delete this user?') }}"
                                                class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition"
                                                title="{{ __('Delete') }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        {{ __('No users found') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    @if($showUserModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" wire:click="closeUserModal"></div>

            <!-- Modal Content -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-lg w-full">
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-gray-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                {{ $editingUserId ? __('Edit User') : __('Add User') }}
                            </h3>
                            <button type="button" wire:click="closeUserModal" class="text-gray-400 hover:text-gray-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <form wire:submit.prevent="saveUser">
                        <div class="px-6 py-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="newUserName" class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ __('Name') }}
                                </label>
                                <input
                                    type="text"
                                    id="newUserName"
                                    wire:model="newUserName"
                                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required
                                >
                                @error('newUserName')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="newUserEmail" class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ __('Email') }}
                                </label>
                                <input
                                    type="email"
                                    id="newUserEmail"
                                    wire:model="newUserEmail"
                                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required
                                >
                                @error('newUserEmail')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="newUserPassword" class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ __('Password') }}
                                    @if($editingUserId)
                                        <span class="text-xs text-gray-500 font-normal">({{ __('leave empty to keep current password') }})</span>
                                    @endif
                                </label>
                                <input
                                    type="password"
                                    id="newUserPassword"
                                    wire:model="newUserPassword"
                                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    {{ $editingUserId ? '' : 'required' }}
                                >
                                @error('newUserPassword')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="newUserRole" class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ __('Role') }}
                                </label>
                                <select
                                    id="newUserRole"
                                    wire:model="newUserRole"
                                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="editor">{{ __('Editor') }}</option>
                                    <option value="administrator">{{ __('Administrator') }}</option>
                                </select>
                                @error('newUserRole')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-800/50 border-t border-gray-800 flex justify-end gap-3">
                            <button
                                type="button"
                                wire:click="closeUserModal"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition"
                            >
                                {{ __('Cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition"
                            >
                                {{ $editingUserId ? __('Save') : __('Create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
