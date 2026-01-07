<div class="relative" x-data="{ open: false }" @click.away="open = false" wire:poll.10s="checkStatus">
    <!-- System Status Button -->
    <button
        @click.prevent="open = !open; if(open) $wire.checkStatus()"
        type="button"
        class="flex items-center space-x-2 px-3 py-2 cursor-pointer rounded-lg bg-gray-800/50 hover:bg-gray-800 border border-gray-700/50 hover:border-gray-600 transition duration-200"
    >
        <div class="relative">
            @if($this->overallStatus === 'healthy')
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            @elseif($this->overallStatus === 'warning')
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                </span>
            @else
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            @endif
        </div>
        <span class="text-sm font-medium text-gray-300">System</span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-72 bg-gray-900 border border-gray-800 rounded-xl shadow-xl shadow-black/20 overflow-hidden z-50"
        style="display: none;"
    >
        <div class="p-4">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">System Status</div>

            <!-- Queue Worker Status -->
            <div class="flex items-center justify-between py-3 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 {{ $queueRunning ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-white">Queue Worker</div>
                        <div class="text-xs {{ $queueRunning ? 'text-green-400' : 'text-red-400' }}">
                            {{ $queueRunning ? 'Running' : 'Not Running' }}
                        </div>
                    </div>
                </div>
                @if($pendingJobs > 0)
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ $pendingJobs }} {{ $pendingJobs === 1 ? 'Job' : 'Jobs' }}
                    </span>
                @else
                    <svg class="w-5 h-5 {{ $queueRunning ? 'text-green-400' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </div>

            <!-- Scheduler Status -->
            <div class="flex items-center justify-between py-3 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 {{ $schedulerRunning ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-white">Scheduler</div>
                        <div class="text-xs {{ $schedulerRunning ? 'text-green-400' : 'text-red-400' }}">
                            {{ $schedulerRunning ? 'Running' : 'Not Running' }}
                        </div>
                    </div>
                </div>
                <svg class="w-5 h-5 {{ $schedulerRunning ? 'text-green-400' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Paperless Connection -->
            <div class="flex items-center justify-between py-3 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 {{ $paperlessConnected ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-white">Paperless API</div>
                        <div class="text-xs {{ $paperlessConnected ? 'text-green-400' : 'text-red-400' }}">
                            {{ $paperlessConnected ? 'Connected' : ($paperlessError ?? 'Not Connected') }}
                        </div>
                    </div>
                </div>
                <svg class="w-5 h-5 {{ $paperlessConnected ? 'text-green-400' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Locked Documents -->
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-white">Locked Documents</div>
                        <div class="text-xs text-gray-400">
                            Currently locked
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $lockedDocuments > 0 ? 'bg-orange-500/20 text-orange-300 border border-orange-500/30' : 'bg-gray-700/50 text-gray-400 border border-gray-600/30' }}">
                    {{ $lockedDocuments }}
                </span>
            </div>
        </div>
    </div>
</div>
