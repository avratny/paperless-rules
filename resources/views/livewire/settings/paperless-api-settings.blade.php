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
                        {{ __('Paperless API Configuration') }}
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

            <!-- Paperless API Settings Card -->
            <div class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 p-2">
                            <svg class="w-full h-full text-white" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                <path d="M6.338 23.028c-0.117 -0.56 -0.353 -1.678 -0.382 -1.678 -4.977 -2.975 -4.388 -8.128 -2.739 -11.073 0.353 3.71 6.92 6.273 3.092 10.808 -0.03 0.059 0.177 0.765 0.353 1.413 0.766 -1.296 1.915 -2.856 1.856 -3.004C3.806 8.01 18.53 7.126 21.592 0c1.385 6.89 -0.706 17.55 -12.544 20.26 -0.06 0.03 -2.15 3.71 -2.238 3.74 0 -0.059 -0.884 -0.03 -0.766 -0.324 0.059 -0.177 0.177 -0.412 0.294 -0.648zm-0.147 -2.768c1.502 -1.737 -0.265 -4.712 -1.325 -5.683 1.796 3.092 1.679 4.888 1.325 5.683z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">{{ __('Connection Settings') }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Configure the connection to your Paperless NGX instance') }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Paperless URL -->
                        <x-forms.text-input
                            id="paperlessUrl"
                            label="{{ __('Paperless URL') }}"
                            type="url"
                            wire-model="paperlessUrl"
                            :wire-live="true"
                            placeholder="http://localhost:8010"
                        />

                        <!-- Paperless API Key -->
                        <x-forms.text-input
                            id="paperlessApiKey"
                            label="{{ __('Paperless API Key') }}"
                            type="password"
                            wire-model="paperlessApiKey"
                            :wire-live="true"
                            placeholder="••••••••••••••••"
                            hint="{{ __('You can find your API key in Paperless under Settings → API Tokens') }}"
                            class="font-mono"
                        />

                        <!-- Connection Test Result -->
                        @if ($connectionSuccess !== null)
                            <div class="p-4 rounded-xl {{ $connectionSuccess ? 'bg-green-500/10 border border-green-500/20' : 'bg-red-500/10 border border-red-500/20' }}">
                                <div class="flex items-start">
                                    @if ($connectionSuccess)
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <p class="text-green-400 font-medium">{{ __('Connection successful!') }}</p>
                                            <p class="text-green-400/70 text-sm mt-1">{{ __('Successfully connected to Paperless API') }}</p>
                                        </div>
                                    @else
                                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <div>
                                            <p class="text-red-400 font-medium">{{ __('Connection failed') }}</p>
                                            <p class="text-red-400/70 text-sm mt-1">{{ $connectionError }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <button
                                type="button"
                                wire:click="save"
                                wire:loading.attr="disabled"
                                class="cursor-pointer px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="save">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="testConnection"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="testConnection">{{ __('Test Connection') }}</span>
                                <span wire:loading wire:target="testConnection" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Testing...') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

