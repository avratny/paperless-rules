<div
    x-data="{ showErrorDialog: false, showTestModal: false }"
    x-on:confirm-save-with-errors.window="showErrorDialog = true"
>
    <!-- Confirmation Dialog for Saving with Errors -->
    <template x-teleport="body">
        <div x-show="showErrorDialog"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] overflow-y-auto"
             style="display: none;">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" x-on:click="showErrorDialog = false"></div>

            <!-- Dialog -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-on:click.stop
                     x-show="showErrorDialog"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">

                    <!-- Warning Icon -->
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-amber-500/20 rounded-full">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-white text-center mb-2">
                        {{ __('Syntax errors present') }}
                    </h3>

                    <!-- Message -->
                    <p class="text-gray-400 text-sm text-center mb-6">
                        {{ __('The rule contains syntax errors. It can still be saved, but will be automatically disabled until the errors are fixed.') }}
                    </p>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            x-on:click="showErrorDialog = false"
                            class="flex-1 px-4 py-2.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition text-sm font-medium"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="button"
                            x-on:click="showErrorDialog = false; $wire.saveWithErrors()"
                            class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition text-sm font-medium"
                        >
                            {{ __('Save disabled') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Test Modal -->
    <template x-teleport="body">
        <div x-show="showTestModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] overflow-y-auto"
             style="display: none;">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" x-on:click="showTestModal = false; $wire.closeTestModal()"></div>

            <!-- Dialog -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-on:click.stop
                     x-show="showTestModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full p-6">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-white">
                                {{ __('Execute Rule on Document') }}
                            </h3>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ __('The rule will be executed and changes will be saved to Paperless.') }}
                            </p>
                        </div>
                        <button
                            type="button"
                            x-on:click="showTestModal = false; $wire.closeTestModal()"
                            class="text-gray-400 hover:text-white transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Document ID Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('Document ID') }}
                        </label>
                        <div class="flex gap-3">
                            <input
                                type="number"
                                wire:model="testDocumentId"
                                min="1"
                                placeholder="{{ __('Enter document ID') }}"
                                class="flex-1 px-4 py-2 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                            @if($testDocumentId && $paperlessUrl)
                                <a
                                    href="{{ $paperlessUrl }}/documents/{{ $testDocumentId }}/details"
                                    target="_blank"
                                    class="px-4 py-2 bg-gray-800/50 hover:bg-gray-700 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center gap-2"
                                    title="{{ __('Open in Paperless') }}"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        @error('testDocumentId')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Error Display -->
                    @if($testError)
                        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-red-400">{{ $testError }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Result Display -->
                    @if($testResult)
                        <div class="mb-6">
                            <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-300 mb-3">{{ __('Execution Result') }}</h4>

                                <!-- Success/Modified Status -->
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="flex items-center gap-2">
                                        @if($testResult['success'])
                                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-sm text-green-400">{{ __('Success') }}</span>
                                        @else
                                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-sm text-red-400">{{ __('Failed') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($testResult['modified'])
                                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-sm text-green-400">{{ __('Document modified and saved') }}</span>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-400">{{ __('No modifications') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Trace -->
                                @if(!empty($testResult['trace']))
                                    <div class="space-y-2">
                                        <h5 class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Execution Trace') }}</h5>
                                        <div class="bg-gray-900/50 rounded-lg p-3 max-h-96 overflow-y-auto">
                                            <pre class="text-xs text-gray-300 font-mono">{{ json_encode($testResult['trace'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex gap-3 justify-end">
                        <button
                            type="button"
                            x-on:click="showTestModal = false; $wire.closeTestModal()"
                            class="px-4 py-2.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition text-sm font-medium"
                        >
                            {{ __('Close') }}
                        </button>
                        <button
                            type="button"
                            wire:click="testOnDocument"
                            wire:loading.attr="disabled"
                            :disabled="!$wire.syntaxValid"
                            class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg wire:loading.remove wire:target="testOnDocument" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg wire:loading wire:target="testOnDocument" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('Execute Rule') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Page Header -->
    <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
            <div class="w-full flex items-center justify-between">
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ $rule ? __('Edit Rule') : __('Create New Rule') }}
                </h2>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        x-on:click="showTestModal = true"
                        :disabled="!$wire.syntaxValid"
                        class="cursor-pointer px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 rounded-lg text-emerald-400 hover:text-emerald-300 transition duration-150 flex items-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        title="{{ __('Execute rule on a specific document') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('Execute on Document') }}</span>
                    </button>
                    <button
                        type="button"
                        wire:click="cancel"
                        class="cursor-pointer px-4 py-2 bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center gap-2 text-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ __('Cancel') }}</span>
                    </button>
                    <button
                        type="button"
                        wire:click="save"
                        class="cursor-pointer px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-lg shadow-indigo-500/25"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $rule ? __('Update') : __('Create') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session()->has('message'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save">
                <!-- Name Field -->
                <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-6 mb-6 backdrop-blur-sm">
                    <div class="mb-6">
                        <x-forms.text-input
                            id="name"
                            label="{{ __('Name') }}"
                            wire-model="name"
                            :required="true"
                            placeholder="{{ __('e.g. Automatically categorize invoice') }}"
                        />
                    </div>

                    <!-- Order Field -->
                    <div class="mb-6">
                        <x-forms.text-input
                            id="order"
                            label="{{ __('Execution Order') }}"
                            type="number"
                            wire-model="order"
                            :min="0"
                            placeholder="0"
                            hint="{{ __('Lower numbers are executed first. Rules with the same order are sorted by creation date (oldest first).') }}"
                        />
                    </div>

                    <!-- Toggle Switches -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Enabled -->
                        <label class="relative flex items-center justify-between p-4 bg-gray-800/30 border border-gray-700/50 rounded-xl cursor-pointer hover:bg-gray-800/50 transition group @if(!$syntaxValid) opacity-60 cursor-not-allowed @endif">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-white">{{ __('Active') }}</span>
                                    @if(!$syntaxValid)
                                        <p class="text-xs text-amber-400">{{ __('Fix syntax errors') }}</p>
                                    @else
                                        <p class="text-xs text-gray-500">{{ __('Rule is active') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="enabled"
                                    wire:model="enabled"
                                    @if(!$syntaxValid) disabled @endif
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-emerald-500 peer-focus:ring-2 peer-focus:ring-emerald-500/50 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>

                        <!-- On Create -->
                        <label class="relative flex items-center justify-between p-4 bg-gray-800/30 border border-gray-700/50 rounded-xl cursor-pointer hover:bg-gray-800/50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-500/10 text-blue-400 group-hover:bg-blue-500/20 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-white">{{ __('On Create') }}</span>
                                    <p class="text-xs text-gray-500">{{ __('Runs when new documents are created') }}</p>
                                </div>
                            </div>
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="on_create"
                                    wire:model="on_create"
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-blue-500 peer-focus:ring-2 peer-focus:ring-blue-500/50 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>

                        <!-- On Change -->
                        <label class="relative flex items-center justify-between p-4 bg-gray-800/30 border border-gray-700/50 rounded-xl cursor-pointer hover:bg-gray-800/50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-white">{{ __('On Change') }}</span>
                                    <p class="text-xs text-gray-500">{{ __('Runs when documents are modified') }}</p>
                                </div>
                            </div>
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="on_change"
                                    wire:model="on_change"
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-purple-500 peer-focus:ring-2 peer-focus:ring-purple-500/50 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Rule Content (CodeMirror) -->
                <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-6 mb-6 backdrop-blur-sm"
                     x-data="{
                         editorId: null,
                         validateTimeout: null
                     }"
                     x-init="$nextTick(() => { editorId = $el.querySelector('.cm-container')?.id })"
                     x-on:cm-change.debounce.500ms="$wire.validateSyntax()"
                >
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-300">
                            {{ __('Rule Definition (DSL)') }}
                        </label>
                        <div class="flex items-center gap-3">
                            <!-- Format Button -->
                            <button
                                type="button"
                                x-on:click="if (editorId && window.cmFormat) { window.cmFormat(editorId) }"
                                class="text-xs text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1"
                                title="{{ __('Format') }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                                {{ __('Format') }}
                            </button>
                            <x-dsl-syntax-help />
                        </div>
                    </div>

                    <x-codemirror-editor
                        wire-model="ruleContent"
                        language="dsl"
                        height="500px"
                    />

                    @error('ruleContent')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Syntax Validation Display -->
                    <div class="mt-4 p-4 rounded-lg border {{ count($syntaxErrors) > 0 ? 'bg-red-900/20 border-red-500/50' : 'bg-gray-800/30 border-gray-700/50' }}">
                        @if(count($syntaxErrors) > 0)
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-medium text-red-400">{{ __('Syntax errors found') }}</h4>
                                    <ul class="text-xs text-red-300 space-y-1 font-mono">
                                        @foreach($syntaxErrors as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @elseif(!empty(trim($ruleContent)))
                            <div class="flex items-center gap-2 text-xs text-green-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('Syntax is correct') }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Syntax-Fehler werden hier angezeigt, wenn welche vorhanden sind.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
