<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-white">{{ __('Welcome to Paperless Rules') }}</h2>
            <p class="mt-2 text-gray-400">{{ __('Let\'s get you set up in just a few steps') }}</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 1 ? 'bg-indigo-500 text-white' : 'bg-gray-700 text-gray-400' }} font-semibold">
                        @if($currentStep > 1)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 1 ? 'text-white' : 'text-gray-400' }}">{{ __('Paperless Connection') }}</span>
                </div>

                <!-- Connector -->
                <div class="w-16 h-1 mx-4 {{ $currentStep >= 2 ? 'bg-indigo-500' : 'bg-gray-700' }}"></div>

                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 2 ? 'bg-indigo-500 text-white' : 'bg-gray-700 text-gray-400' }} font-semibold">
                        @if($currentStep > 2)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 2 ? 'text-white' : 'text-gray-400' }}">{{ __('Document Processing') }}</span>
                </div>

                <!-- Connector -->
                <div class="w-16 h-1 mx-4 {{ $currentStep >= 3 ? 'bg-indigo-500' : 'bg-gray-700' }}"></div>

                <!-- Step 3 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 3 ? 'bg-indigo-500 text-white' : 'bg-gray-700 text-gray-400' }} font-semibold">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $currentStep >= 3 ? 'text-white' : 'text-gray-400' }}">{{ __('Authentication') }}</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Setup Form -->
        <div class="bg-gray-900/50 backdrop-blur-xl border border-gray-800/50 rounded-2xl shadow-xl p-8">
            <form wire:submit.prevent="{{ $currentStep === 3 ? 'completeSetup' : 'nextStep' }}">
                <!-- Step 1: Paperless Connection -->
                @if($currentStep === 1)
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">{{ __('Connect to Paperless NGX') }}</h3>
                            <p class="text-sm text-gray-400">{{ __('Enter your Paperless NGX connection details') }}</p>
                        </div>

                        <!-- Paperless URL -->
                        <x-forms.text-input
                            id="paperlessUrl"
                            label="{{ __('Paperless URL') }}"
                            type="url"
                            wire-model="paperlessUrl"
                            :wire-live="true"
                            placeholder="http://localhost:8010"
                            required
                        />

                        <!-- Paperless API Key -->
                        <x-forms.text-input
                            id="paperlessApiKey"
                            label="{{ __('Paperless API Key') }}"
                            type="password"
                            wire-model="paperlessApiKey"
                            :wire-live="true"
                            placeholder="••••••••••••••••"
                            hint="{{ __('You can create an API token in Paperless by clicking on your username → Edit Profile → API Token') }}"
                            class="font-mono"
                            required
                        />

                        <!-- Connection Status -->
                        @if($connectionSuccess === true)
                            <div class="flex items-center p-4 bg-green-500/10 border border-green-500/50 rounded-lg">
                                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-green-400 text-sm">{{ __('Connection successful!') }}</span>
                            </div>
                        @elseif($connectionSuccess === false)
                            <div class="flex items-center p-4 bg-red-500/10 border border-red-500/50 rounded-lg">
                                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-red-400 text-sm">{{ $connectionError }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Step 2: Document Processing -->
                @if($currentStep === 2)
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">{{ __('Document Processing') }}</h3>
                            <p class="text-sm text-gray-400">{{ __('Configure how documents should be processed') }}</p>
                        </div>

                        <!-- Processing Mode -->
                        <x-forms.radio-group
                            label="{{ __('Processing Mode') }}"
                            wire-model="processingMode"
                            :wire-live="true"
                            name="processingMode"
                            :options="[
                                [
                                    'value' => 'polling',
                                    'label' => __('Polling'),
                                    'description' => __('Periodically check for new or modified documents'),
                                    'icon' => '<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'
                                ],
                                [
                                    'value' => 'webhook',
                                    'label' => __('Webhook'),
                                    'description' => __('Real-time processing when documents are created or modified'),
                                    'icon' => '<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'/></svg>'
                                ],
                            ]"
                        />

                        <!-- Polling Interval (only shown when polling is selected) -->
                        @if($processingMode === 'polling')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-forms.text-input
                                    id="pollingInterval"
                                    label="{{ __('Polling Interval') }}"
                                    type="number"
                                    wire-model="pollingInterval"
                                    :wire-live="true"
                                    :min="1"
                                    :step="1"
                                    placeholder="5"
                                    suffix="{{ __('minutes') }}"
                                    hint="{{ __('How often to check for new or modified documents') }}"
                                    required
                                />

                                <x-forms.text-input
                                    id="documentLockDuration"
                                    label="{{ __('Document Lock Duration') }}"
                                    type="number"
                                    wire-model="documentLockDuration"
                                    :wire-live="true"
                                    :min="$pollingInterval * 60"
                                    :step="1"
                                    placeholder="300"
                                    suffix="{{ __('seconds') }}"
                                    hint="{{ __('Minimum :min seconds when polling is enabled', ['min' => $pollingInterval * 60]) }}"
                                    required
                                />
                            </div>
                        @else
                            <x-forms.text-input
                                id="documentLockDuration"
                                label="{{ __('Document Lock Duration') }}"
                                type="number"
                                wire-model="documentLockDuration"
                                :wire-live="true"
                                :min="5"
                                :step="1"
                                placeholder="60"
                                suffix="{{ __('seconds') }}"
                                hint="{{ __('How long a document is locked after being saved') }}"
                                required
                            />
                        @endif
                    </div>
                @endif

                <!-- Step 3: Authentication -->
                @if($currentStep === 3)
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">{{ __('Authentication') }}</h3>
                            <p class="text-sm text-gray-400">{{ __('Optionally enable authentication to protect your application') }}</p>
                        </div>

                        <!-- Enable Authentication Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
                            <div class="flex-1">
                                <label for="enableAuthentication" class="block text-sm font-medium text-gray-300">
                                    {{ __('Enable Authentication') }}
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Require users to log in to access the application') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="enableAuthentication" id="enableAuthentication" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Admin User Creation (only shown when authentication is enabled) -->
                        @if($enableAuthentication)
                            @if($adminExists)
                                <!-- Admin Already Exists Message -->
                                <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-green-400 font-semibold mb-1">{{ __('Administrator Account Already Exists') }}</h4>
                                            <p class="text-green-400/70 text-sm">
                                                {{ __('An administrator account has already been created. You can manage users in the Authentication settings after completing the setup.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Create Admin Form -->
                                <div class="space-y-4 p-4 bg-gray-800/30 rounded-lg border border-gray-700/50">
                                    <p class="text-sm text-gray-300 font-medium">{{ __('Create Administrator Account') }}</p>

                                    <x-forms.text-input
                                        id="adminName"
                                        label="{{ __('Name') }}"
                                        type="text"
                                        wire-model="adminName"
                                        :wire-live="true"
                                        placeholder="{{ __('John Doe') }}"
                                        required
                                    />

                                    <x-forms.text-input
                                        id="adminEmail"
                                        label="{{ __('Email') }}"
                                        type="email"
                                        wire-model="adminEmail"
                                        :wire-live="true"
                                        placeholder="admin@example.com"
                                        required
                                    />

                                    <x-forms.text-input
                                        id="adminPassword"
                                        label="{{ __('Password') }}"
                                        type="password"
                                        wire-model="adminPassword"
                                        :wire-live="true"
                                        placeholder="••••••••"
                                        hint="{{ __('Minimum 8 characters') }}"
                                        required
                                    />

                                    <x-forms.text-input
                                        id="adminPasswordConfirmation"
                                        label="{{ __('Confirm Password') }}"
                                        type="password"
                                        wire-model="adminPasswordConfirmation"
                                        :wire-live="true"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                <!-- Navigation Buttons -->
                <div class="mt-8 flex items-center justify-between">
                    @if($currentStep > 1)
                        <button
                            type="button"
                            wire:click="previousStep"
                            class="px-6 py-3 bg-gray-800/50 hover:bg-gray-700/50 text-white rounded-lg transition-all duration-200 border border-gray-700/50 hover:border-gray-600/50"
                        >
                            {{ __('Previous') }}
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($currentStep < 3)
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="nextStep"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove wire:target="nextStep">{{ __('Next') }}</span>
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading wire:target="nextStep">
                                @if($currentStep === 1)
                                    {{ __('Testing connection...') }}
                                @else
                                    {{ __('Next') }}
                                @endif
                            </span>
                        </button>
                    @else
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="completeSetup"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove wire:target="completeSetup">{{ __('Complete Setup') }}</span>
                            <svg wire:loading wire:target="completeSetup" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading wire:target="completeSetup">{{ __('Completing...') }}</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

