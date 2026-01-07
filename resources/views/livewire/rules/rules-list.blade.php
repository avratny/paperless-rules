<div>
    @section('header')
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight">
                {{ __('Rules') }}
            </h2>
            <a href="{{ route('rules.editor') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg transition duration-150 flex items-center gap-2 text-sm shadow-lg shadow-indigo-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('New Rule') }}</span>
            </a>
        </div>
    @endsection

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
        <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl p-6 mb-6 backdrop-blur-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <x-forms.text-input
                    id="search"
                    label="{{ __('Search') }}"
                    wire-model="search"
                    :wire-live="true"
                    debounce="300"
                    placeholder="{{ __('Search name...') }}"
                />

                <!-- Enabled Filter -->
                <x-forms.select
                    id="filterEnabled"
                    label="{{ __('Status') }}"
                    wire-model="filterEnabled"
                    :wire-live="true"
                >
                    <option value="all">{{ __('All') }}</option>
                    <option value="enabled">{{ __('Enabled') }}</option>
                    <option value="disabled">{{ __('Disabled') }}</option>
                </x-forms.select>

                <!-- On Create Filter -->
                <x-forms.select
                    id="filterOnCreate"
                    label="{{ __('On Create') }}"
                    wire-model="filterOnCreate"
                    :wire-live="true"
                >
                    <option value="all">{{ __('All') }}</option>
                    <option value="yes">{{ __('Yes') }}</option>
                    <option value="no">{{ __('No') }}</option>
                </x-forms.select>

                <!-- On Change Filter -->
                <x-forms.select
                    id="filterOnChange"
                    label="{{ __('On Change') }}"
                    wire-model="filterOnChange"
                    :wire-live="true"
                >
                    <option value="all">{{ __('All') }}</option>
                    <option value="yes">{{ __('Yes') }}</option>
                    <option value="no">{{ __('No') }}</option>
                </x-forms.select>
            </div>

            <!-- Reset Button and Per Page -->
            <div class="mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <label for="perPage" class="text-sm font-medium text-gray-300">
                        {{ __('Entries per page:') }}
                    </label>
                    <x-forms.select
                        id="perPage"
                        wire-model="perPage"
                        :wire-live="true"
                        class="px-3 py-2"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">{{ __('All') }}</option>
                    </x-forms.select>
                </div>
                <button
                    wire:click="resetFilters"
                    class="px-4 py-2 bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:text-white transition duration-150 flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>

        <!-- Rules Table -->
        <div class="bg-gray-900/50 border border-gray-800/50 rounded-2xl overflow-hidden backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-800/50 border-b border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                                {{ __('Order') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                                {{ __('Trigger') }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-800/30 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-white truncate">{{ $rule->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-mono text-indigo-400">{{ $rule->order ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($rule->enabled)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Aktiviert
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Deaktiviert
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @if($rule->on_create)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20" title="On Create">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Create
                                            </span>
                                        @endif
                                        @if($rule->on_change)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20" title="On Change">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Change
                                            </span>
                                        @endif
                                        @if(!$rule->on_create && !$rule->on_change)
                                            <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('rules.editor', ['rule' => $rule->id]) }}" class="text-indigo-400 hover:text-indigo-300 transition duration-150">
                                        {{ __('Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-400 text-lg font-medium">{{ __('No rules found') }}</p>
                                        <p class="text-gray-500 text-sm mt-1">{{ __('Try adjusting your filters') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($rules->hasPages())
                <div class="px-6 py-4 border-t border-gray-800">
                    {{ $rules->links() }}
                </div>
            @endif
        </div>

            <!-- Stats -->
            <div class="mt-6 text-sm text-gray-400">
                Zeige {{ $rules->count() }} von {{ $rules->total() }} Rules
            </div>
        </div>
    </div>
</div>

