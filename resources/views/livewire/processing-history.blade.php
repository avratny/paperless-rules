<div>
    @section('header')
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Processing History') }}
                </h2>
                <p class="text-gray-400 text-sm mt-1">
                    {{ __('View how documents were processed by rules') }}
                </p>
            </div>
        </div>
    @endsection

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total -->
                <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800/50 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Total Executions') }}</p>
                            <p class="text-white text-3xl font-bold mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Success -->
                <div class="bg-gradient-to-br from-green-900/20 to-green-800/20 border border-green-800/30 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-400 text-sm font-medium">{{ __('Successful') }}</p>
                            <p class="text-white text-3xl font-bold mt-2">{{ number_format($stats['success']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Errors -->
                <div class="bg-gradient-to-br from-red-900/20 to-red-800/20 border border-red-800/30 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-400 text-sm font-medium">{{ __('Errors') }}</p>
                            <p class="text-white text-3xl font-bold mt-2">{{ number_format($stats['error']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Skipped -->
                <div class="bg-gradient-to-br from-yellow-900/20 to-yellow-800/20 border border-yellow-800/30 rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-400 text-sm font-medium">{{ __('Skipped') }}</p>
                            <p class="text-white text-3xl font-bold mt-2">{{ number_format($stats['skipped']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800/50 rounded-2xl p-6 backdrop-blur-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <x-forms.text-input
                        label="{{ __('Search Rule') }}"
                        wire-model="search"
                        :wire-live="true"
                        debounce="300"
                        placeholder="{{ __('Search rule name...') }}"
                    />

                    <!-- Status Filter -->
                    <x-forms.select
                        label="{{ __('Status') }}"
                        wire-model="filterStatus"
                        :wire-live="true"
                    >
                        <option value="all">{{ __('All') }}</option>
                        <option value="success">{{ __('Success') }}</option>
                        <option value="error">{{ __('Error') }}</option>
                        <option value="skipped">{{ __('Skipped') }}</option>
                    </x-forms.select>

                    <!-- Event Type Filter -->
                    <x-forms.select
                        label="{{ __('Event Type') }}"
                        wire-model="filterEventType"
                        :wire-live="true"
                    >
                        <option value="all">{{ __('All') }}</option>
                        <option value="on_create">{{ __('On Create') }}</option>
                        <option value="on_change">{{ __('On Change') }}</option>
                    </x-forms.select>

                    <!-- Document ID Filter -->
                    <x-forms.text-input
                        label="{{ __('Document ID') }}"
                        type="number"
                        wire-model="filterDocumentId"
                        :wire-live="true"
                        debounce="300"
                        placeholder="{{ __('Filter by document...') }}"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <!-- Rule ID Filter -->
                    <x-forms.text-input
                        label="{{ __('Rule ID') }}"
                        type="number"
                        wire-model="filterRuleId"
                        :wire-live="true"
                        debounce="300"
                        placeholder="{{ __('Filter by rule...') }}"
                    />

                    <!-- Per Page -->
                    <x-forms.select
                        label="{{ __('Per Page') }}"
                        wire-model="perPage"
                        :wire-live="true"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-forms.select>



                    <!-- Reset Button -->
                    <div class="flex items-end">
                        <button wire:click="resetFilters"
                            class="w-full bg-gray-700/50 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            {{ __('Reset Filters') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Jobs List -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                    @php
                        $jobLogs = $logs[$job->job_id] ?? collect();
                        $firstLog = $jobLogs->first();
                        $isExpanded = in_array($job->job_id, $expandedJobs);

                        // Calculate job statistics
                        $jobStats = [
                            'total' => $jobLogs->count(),
                            'success' => $jobLogs->where('status', 'success')->count(),
                            'error' => $jobLogs->where('status', 'error')->count(),
                            'skipped' => $jobLogs->where('status', 'skipped')->count(),
                            'modified' => $jobLogs->where('document_modified', true)->count(),
                        ];

                        // Determine overall job status
                        $jobStatus = 'success';
                        if ($jobStats['error'] > 0) {
                            $jobStatus = 'error';
                        } elseif ($jobStats['skipped'] > 0 && $jobStats['success'] === 0) {
                            $jobStatus = 'skipped';
                        }
                    @endphp

                    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800/50 rounded-2xl backdrop-blur-sm overflow-hidden">
                        <!-- Job Header (Clickable) -->
                        <div wire:click="toggleJob('{{ $job->job_id }}')"
                            class="p-6 cursor-pointer hover:bg-gray-800/30 transition duration-150 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Expand Icon -->
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 {{ $isExpanded ? 'rotate-90' : '' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>

                                    <!-- Document Info -->
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-indigo-500/10 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-white font-medium">{{ __('Document') }} #{{ $firstLog->document_id }}</span>
                                                @if($firstLog->event_type === 'on_create')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                        {{ __('Create') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                                        {{ __('Change') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-400 text-sm">
                                                {{ $jobStats['total'] }} {{ __('rules executed') }} •
                                                {{ $firstLog->executed_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Job Statistics -->
                                    <div class="flex items-center space-x-4 ml-auto">
                                        @if($jobStats['success'] > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span class="text-green-400 text-sm font-medium">{{ $jobStats['success'] }}</span>
                                            </div>
                                        @endif
                                        @if($jobStats['error'] > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span class="text-red-400 text-sm font-medium">{{ $jobStats['error'] }}</span>
                                            </div>
                                        @endif
                                        @if($jobStats['skipped'] > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8" />
                                                </svg>
                                                <span class="text-yellow-400 text-sm font-medium">{{ $jobStats['skipped'] }}</span>
                                            </div>
                                        @endif
                                        @if($jobStats['modified'] > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="text-indigo-400 text-sm font-medium">{{ $jobStats['modified'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Overall Status Badge -->
                                <div class="ml-4">
                                    @if($jobStatus === 'success')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                            {{ __('Success') }}
                                        </span>
                                    @elseif($jobStatus === 'error')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                            {{ __('Error') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                                            {{ __('Skipped') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <!-- Expanded Rules List -->
                        @if($isExpanded)
                            <div class="border-t border-gray-800/50 bg-gray-900/30">
                                <div class="p-6 space-y-3">
                                    @foreach($jobLogs as $log)
                                        <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex items-start space-x-3 flex-1">
                                                    <!-- Rule Icon -->
                                                    <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                    </div>

                                                    <!-- Rule Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <h4 class="text-white font-medium">{{ $log->rule_name }}</h4>
                                                            @if($log->rule_id)
                                                                <span class="text-gray-500 text-xs">#{{ $log->rule_id }}</span>
                                                            @else
                                                                <span class="text-gray-500 text-xs italic">{{ __('Rule deleted') }}</span>
                                                            @endif
                                                            <span class="text-xs px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                                {{ __('Order') }}: {{ $log->rule_order ?? 0 }}
                                                            </span>
                                                        </div>

                                                        <!-- Status and Modified -->
                                                        <div class="flex items-center space-x-3 text-sm">
                                                            @if($log->status === 'success')
                                                                <span class="inline-flex items-center text-green-400">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    {{ __('Success') }}
                                                                </span>
                                                            @elseif($log->status === 'error')
                                                                <span class="inline-flex items-center text-red-400">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                    {{ __('Error') }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center text-yellow-400">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8" />
                                                                    </svg>
                                                                    {{ __('Skipped') }}
                                                                </span>
                                                            @endif

                                                            @if($log->document_modified)
                                                                <span class="inline-flex items-center text-indigo-400">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                    </svg>
                                                                    {{ __('Modified') }}
                                                                </span>
                                                            @endif

                                                            <span class="text-gray-500">
                                                                {{ $log->executed_at->format('H:i:s') }}
                                                            </span>
                                                        </div>

                                                        <!-- Error Message -->
                                                        @if($log->error_message)
                                                            <div class="mt-3 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                                                                <p class="text-red-400 text-sm font-medium mb-1">{{ __('Error Message') }}</p>
                                                                <p class="text-red-300 text-sm font-mono">{{ $log->error_message }}</p>
                                                            </div>
                                                        @endif

                                                        <!-- Execution Trace -->
                                                        @if($log->trace && count($log->trace) > 0)
                                                            <div class="mt-3">
                                                                <details class="group">
                                                                    <summary class="cursor-pointer text-sm text-indigo-400 hover:text-indigo-300 flex items-center space-x-1">
                                                                        <svg class="w-4 h-4 transform group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                        </svg>
                                                                        <span>{{ __('Execution Trace') }} ({{ count($log->trace) }} {{ __('steps') }})</span>
                                                                    </summary>
                                                                    <div class="mt-2 space-y-2">
                                                                        @foreach($log->trace as $index => $step)
                                                                            <div class="bg-gray-900/50 border border-gray-700/30 rounded-lg p-3">
                                                                                <div class="flex items-start space-x-2">
                                                                                    <span class="text-gray-500 text-xs font-mono">{{ $index + 1 }}.</span>
                                                                                    <div class="flex-1">
                                                                                        <div class="flex items-center space-x-2 mb-1">
                                                                                            <span class="text-xs font-mono px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                                                                {{ $step['type'] ?? 'UNKNOWN' }}
                                                                                            </span>
                                                                                            <span class="text-white text-sm font-medium">{{ $step['action'] ?? '' }}</span>
                                                                                        </div>
                                                                                        @if(isset($step['args']) && count($step['args']) > 0)
                                                                                            <div class="text-gray-400 text-xs mt-1">
                                                                                                <span class="text-gray-500">{{ __('Args') }}:</span>
                                                                                                <span class="font-mono">{{ json_encode($step['args']) }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($step['result']))
                                                                                            <div class="text-gray-400 text-xs mt-1">
                                                                                                <span class="text-gray-500">{{ __('Result') }}:</span>
                                                                                                <span class="font-mono">{{ is_bool($step['result']) ? ($step['result'] ? 'true' : 'false') : json_encode($step['result']) }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </details>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/50 border border-gray-800/50 rounded-2xl p-12 backdrop-blur-sm text-center">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-xl font-medium text-gray-400 mb-2">{{ __('No execution logs found') }}</h3>
                        <p class="text-gray-500">{{ __('Try adjusting your filters or process some documents') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
