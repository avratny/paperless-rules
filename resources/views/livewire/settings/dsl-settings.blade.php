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
                        {{ __('DSL Settings') }}
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

            <!-- Warning Box -->
            <div class="mb-6 bg-amber-500/10 border border-amber-500/20 rounded-2xl p-6 backdrop-blur-sm">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-amber-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="text-amber-400 font-semibold mb-2">{{ __('Warning: Advanced Settings') }}</h4>
                        <p class="text-amber-400/80 text-sm leading-relaxed">
                            {{ __('These settings control the limits of the DSL (Domain Specific Language) parser and executor. Only change these values if you know what you are doing. Incorrect values may cause rules to fail or create security issues.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- DSL Settings Card -->
            <div class="bg-gray-900/50 border border-gray-800/50 overflow-hidden shadow-xl rounded-2xl backdrop-blur-sm">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3 p-2">
                            <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">{{ __('Rule Execution Limits') }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Configure the limits for rule parsing and execution') }}</p>
                        </div>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-6">
                            <!-- Max DSL Length -->
                            <x-forms.text-input
                                id="maxDslLength"
                                label="{{ __('Maximum DSL Length') }}"
                                type="number"
                                wire-model="maxDslLength"
                                :min="1000"
                                :max="100000"
                                :required="true"
                                placeholder="10000"
                                hint="{{ __('Maximum number of characters allowed in a rule (default: 10000)') }}"
                            />

                            <!-- Max LET Count -->
                            <x-forms.text-input
                                id="maxLetCount"
                                label="{{ __('Maximum LET Count') }}"
                                type="number"
                                wire-model="maxLetCount"
                                :min="1"
                                :max="1000"
                                :required="true"
                                placeholder="50"
                                hint="{{ __('Maximum number of LET statements allowed in a rule (default: 50)') }}"
                            />

                            <!-- Max DO Count -->
                            <x-forms.text-input
                                id="maxDoCount"
                                label="{{ __('Maximum DO Count') }}"
                                type="number"
                                wire-model="maxDoCount"
                                :min="1"
                                :max="1000"
                                :required="true"
                                placeholder="50"
                                hint="{{ __('Maximum number of DO actions allowed in a rule (default: 50)') }}"
                            />

                            <!-- Max Nesting Depth -->
                            <x-forms.text-input
                                id="maxNestingDepth"
                                label="{{ __('Maximum Nesting Depth') }}"
                                type="number"
                                wire-model="maxNestingDepth"
                                :min="1"
                                :max="100"
                                :required="true"
                                placeholder="10"
                                hint="{{ __('Maximum nesting depth for WHEN/THEN/ELSE blocks (default: 10)') }}"
                            />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between mt-6">
                            <button
                                type="submit"
                                class="cursor-pointer px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Save Settings') }}</span>
                            </button>
                            <button
                                type="button"
                                wire:click="resetToDefaults"
                                class="px-4 py-2 bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ __('Reset to Defaults') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

