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
                        {{ __('Document Processing Configuration') }}
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

            <!-- Document Processing Configuration Card -->
            <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-800/50 shadow-xl overflow-hidden">
                <div class="p-8">
                    <!-- Card Header -->
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3 p-2">
                            <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">{{ __('Processing Settings') }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Configure how and when documents are processed') }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Processing Mode -->
                        <x-forms.radio-group
                            label="{{ __('Processing Mode') }}"
                            wire-model="processingMode"
                            :wire-live="true"
                            name="processingMode"
                            :options="[
                                [
                                    'value' => 'webhook',
                                    'label' => __('Webhook'),
                                    'description' => __('Real-time processing when documents are created or modified'),
                                    'icon' => '<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'/></svg>'
                                ],
                                [
                                    'value' => 'polling',
                                    'label' => __('Polling'),
                                    'description' => __('Periodic check for new or modified documents'),
                                    'icon' => '<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'
                                ]
                            ]"
                        />

                        <!-- Polling Interval and Document Lock Duration -->
                        @if($processingMode === 'polling')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Polling Interval -->
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
                                />

                                <!-- Document Lock Duration -->
                                <x-forms.text-input
                                    id="documentLockDuration"
                                    label="{{ __('Document Lock Duration') }}"
                                    type="number"
                                    wire-model="documentLockDuration"
                                    :wire-live="true"
                                    :min="$pollingInterval * 60"
                                    :step="1"
                                    placeholder="60"
                                    suffix="{{ __('seconds') }}"
                                    hint="{{ __('Minimum :min seconds when polling is enabled', ['min' => $pollingInterval * 60]) }}"
                                />
                            </div>
                        @else
                            <!-- Document Lock Duration (full width when webhook mode) -->
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
                            />
                        @endif

                        <!-- Warning (only shown when webhook mode is selected) -->
                        @if($processingMode === 'webhook')
                            <div class="p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-yellow-400/90 text-xs leading-relaxed">
                                        {{ __('Warning: If the lock duration is too short, it may cause an infinite loop with Paperless, as saved documents trigger new processing events.') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Save Button -->
                        <div class="flex justify-start">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

