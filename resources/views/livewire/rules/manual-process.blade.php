<div>
    <!-- Page Header -->
    <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
            <div class="w-full flex items-center justify-between">
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Manual Mode') }}
                </h2>
                <button
                    wire:click="process"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="cursor-pointer px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
                    @if(empty($selectedRules)) disabled @endif
                >
                    <svg wire:loading wire:target="process" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="process" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span wire:loading.remove wire:target="process">{{ __('Process document') }}</span>
                    <span wire:loading wire:target="process">{{ __('Processing...') }}</span>
                </button>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Document ID Input -->
            <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-6 mb-6 backdrop-blur-sm">
                <h3 class="text-lg font-medium text-white mb-4">{{ __('Select document') }}</h3>
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <x-forms.text-input
                                    id="documentId"
                                    label="{{ __('Paperless Document ID') }}"
                                    type="number"
                                    wire-model="documentId"
                                    :min="1"
                                    placeholder="{{ __('e.g. 123') }}"
                                />
                            </div>
                            @if($documentId && $paperlessUrl)
                                <a
                                    href="{{ $paperlessUrl }}/documents/{{ $documentId }}/details"
                                    target="_blank"
                                    class="px-4 py-2 bg-gray-800/50 hover:bg-gray-700 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center gap-2 h-fit mt-8"
                                    title="{{ __('Open in Paperless') }}"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('Open in Paperless') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rules Selection -->
            <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-6 mb-6 backdrop-blur-sm relative z-10">
                <h3 class="text-lg font-medium text-white mb-4">{{ __('Select rules') }}</h3>

                @error('selectedRules')
                    <p class="mb-4 text-sm text-red-400">{{ $message }}</p>
                @enderror

                <!-- Search Input -->
                <div class="relative mb-4 z-50">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.200ms="searchQuery"
                            wire:focus="$set('showDropdown', true)"
                            placeholder="{{ __('Search and add rule...') }}"
                            class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                    </div>

                    <!-- Dropdown Results -->
                    @if($showDropdown && ($searchQuery || $this->filteredRules->isNotEmpty()))
                        <div class="absolute z-50 w-full mt-2 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                            @forelse($this->filteredRules as $rule)
                                <button
                                    wire:click="addRule({{ $rule->id }})"
                                    class="w-full px-4 py-3 text-left hover:bg-gray-700/50 transition duration-150 flex items-center justify-between group"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-white">{{ $rule->name }}</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            @empty
                                <div class="px-4 py-3 text-gray-400 text-sm">
                                    @if($searchQuery)
                                        {{ __('No rules found for ":query"', ['query' => $searchQuery]) }}
                                    @else
                                        {{ __('All rules have already been added') }}
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                <!-- Click outside to close dropdown -->
                @if($showDropdown)
                    <div wire:click="$set('showDropdown', false)" class="fixed inset-0 z-40" style="background: transparent;"></div>
                @endif

                <!-- Selected Rules List -->
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-300">
                            {{ __('Execution order (:count rules)', ['count' => count($selectedRules)]) }}
                        </span>
                    </div>

                    @if(empty($selectedRules))
                        <div class="text-center py-8 border-2 border-dashed border-gray-700 rounded-xl">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-gray-400">{{ __('No rules selected') }}</p>
                            <p class="text-gray-500 text-sm mt-1">{{ __('Search for rules above and add them') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($this->selectedRulesData as $index => $rule)
                                <div class="group flex items-center gap-3 p-3 bg-gray-800/30 hover:bg-gray-800/50 border border-gray-700/50 rounded-xl transition duration-150">
                                    <!-- Order Number -->
                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                        {{ $index + 1 }}
                                    </div>

                                    <!-- Rule Name -->
                                    <div class="flex-1 min-w-0">
                                        <span class="text-white font-medium truncate block">{{ $rule->name }}</span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                        <!-- Move Up -->
                                        <button
                                            wire:click="moveRuleUp({{ $index }})"
                                            @if($index === 0) disabled @endif
                                            class="p-1.5 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-white transition disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="{{ __('Move up') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>

                                        <!-- Move Down -->
                                        <button
                                            wire:click="moveRuleDown({{ $index }})"
                                            @if($index === count($selectedRules) - 1) disabled @endif
                                            class="p-1.5 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-white transition disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="{{ __('Move down') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Divider -->
                                        <div class="w-px h-4 bg-gray-700 mx-1"></div>

                                        <!-- Open in Editor -->
                                        <a
                                            href="{{ route('rules.editor', ['rule' => $rule->id]) }}"
                                            target="_blank"
                                            class="p-1.5 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-indigo-400 transition"
                                            title="{{ __('Open in editor') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>

                                        <!-- Remove -->
                                        <button
                                            wire:click="removeRule({{ $rule->id }})"
                                            class="p-1.5 rounded-lg hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition"
                                            title="{{ __('Remove') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if($success)
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-green-400">{{ $success }}</span>
                    </div>
                </div>
            @endif

            @if($error)
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-red-400">{{ $error }}</span>
                    </div>
                </div>
            @endif

            <!-- Results -->
            @if(!empty($results))
                <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl overflow-hidden backdrop-blur-sm">
                    <div class="px-6 py-4 border-b border-gray-800">
                        <h3 class="text-lg font-medium text-white">{{ __('Results') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-800">
                        @foreach($results as $index => $result)
                            <div class="p-4 hover:bg-gray-800/30 transition duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <!-- Step Number -->
                                        <div class="flex-shrink-0 w-6 h-6 bg-gray-700 rounded-full flex items-center justify-center text-xs text-gray-300 font-medium">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($result['success'])
                                                <div class="w-8 h-8 bg-green-500/10 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-white">{{ $result['rule_name'] }}</div>
                                            <div class="text-xs text-gray-400">
                                                @if($result['modified'])
                                                    {{ __('Document was modified') }}
                                                @else
                                                    {{ __('No changes') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($result['success'])
                                        <span class="px-2 py-1 text-xs font-medium bg-green-500/10 text-green-400 rounded-full border border-green-500/20">
                                            {{ __('Successful') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-500/10 text-red-400 rounded-full border border-red-500/20">
                                            {{ __('Failed') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

