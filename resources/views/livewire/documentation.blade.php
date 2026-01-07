<div x-data x-init="$nextTick(() => { if (window.highlightDslCode) window.highlightDslCode(); })"
     x-on:livewire:navigated.window="$nextTick(() => { if (window.highlightDslCode) window.highlightDslCode(); })">
    <!-- Page Header -->
    <header class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 backdrop-blur-sm border-b border-gray-800/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-[92px] flex items-center">
            <div class="w-full flex items-center justify-between">
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Documentation') }}
                </h2>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8">
            <!-- Sidebar Navigation -->
            <aside class="w-72 flex-shrink-0">
                <div class="sticky top-24">
                    <nav class="bg-gradient-to-br from-gray-900/80 to-gray-800/80 backdrop-blur-md border border-gray-700/50 rounded-2xl shadow-2xl overflow-hidden">
                        <!-- Navigation Header -->
                        <div class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-gray-700/50 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white">{{ __('Documentation') }}</h3>
                                    <p class="text-xs text-gray-400">{{ __('Select a document from the navigation') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Search Field - Opens Modal -->
                        <div class="px-3 py-3 border-b border-gray-700/50">
                            <button
                                @click="$dispatch('open-search-modal')"
                                class="w-full flex items-center gap-2 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-gray-400 hover:border-indigo-500 hover:text-white transition-colors cursor-pointer"
                            >
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>{{ __('Search documentation...') }}</span>
                                <span class="ml-auto text-xs text-gray-500">Ctrl+K</span>
                            </button>
                        </div>

                        <!-- Normal Navigation -->
                        @php
                            $activeSectionKey = '';
                            foreach ($navigation as $sectionKey => $section) {
                                if ($sectionKey === 'Start') continue;

                                if (!empty($section['children'])) {
                                    foreach ($section['children'] as $child) {
                                        if ($child['file'] === $currentFile) {
                                            $activeSectionKey = $sectionKey;
                                            break 2;
                                        }
                                    }
                                } elseif (!empty($section['files'])) {
                                    if (in_array($currentFile, $section['files'])) {
                                        $activeSectionKey = $sectionKey;
                                        break;
                                    }
                                }
                            }

                            if (empty($activeSectionKey)) {
                                $activeSectionKey = collect($navigation)->keys()->filter(fn($key) => $key !== 'Start')->first() ?? '';
                            }
                        @endphp

                        <div
                            class="p-3 space-y-4"
                            x-data="{
                                openSection: '{{ $activeSectionKey }}',
                                userInteracted: false,
                                activeSection: '{{ $activeSectionKey }}'
                            }"
                            wire:key="nav-{{ $currentFile }}"
                            x-effect="if (!userInteracted) { openSection = '{{ $activeSectionKey }}'; }"
                        >
                            @foreach($navigation as $sectionKey => $section)
                                @if($sectionKey === 'Start')
                                    <!-- Start Page - Special Single Item -->
                                    @php
                                        $file = $section['file'] ?? ($section['files'][0] ?? null);
                                        $isActive = $currentFile === $file;
                                    @endphp
                                    @if($file)
                                        <button
                                            type="button"
                                            wire:click="selectDocument('{{ $file }}')"
                                            @click="userInteracted = false"
                                            class="group w-full text-left px-3 py-3 rounded-xl text-sm transition-all duration-200 relative overflow-hidden cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-indigo-500/20 to-purple-500/20 text-white font-medium shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}"
                                        >
                                            <div class="flex items-center gap-3">
                                                <!-- Home Icon -->
                                                <div class="w-5 h-5 {{ $isActive ? 'text-indigo-400' : 'text-gray-500 group-hover:text-indigo-400' }} transition-colors duration-200">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                    </svg>
                                                </div>

                                                <span class="flex-1 font-medium">{{ $section['title'] }}</span>

                                                <!-- Arrow Icon on Hover/Active -->
                                                <svg class="w-4 h-4 transition-all duration-200 {{ $isActive ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- Divider after Start -->
                                        <div class="border-t border-gray-700/50 my-2"></div>
                                    @endif
                                @else
                                    <!-- Regular Section with Items -->
                                    <div>
                                        <!-- Section Header - Clickable -->
                                        <button
                                            type="button"
                                            @click="
                                                userInteracted = true;
                                                openSection = openSection === '{{ $sectionKey }}' ? '' : '{{ $sectionKey }}';
                                            "
                                            class="w-full flex items-center gap-2 px-3 py-2 mb-2 rounded-lg hover:bg-gray-800/30 transition-colors duration-200 cursor-pointer group"
                                        >
                                            @php
                                                $sectionIcon = match($sectionKey) {
                                                    'Basics' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
                                                    'Variables' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
                                                    'Conditions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                                    'Actions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                                                    'Examples' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />',
                                                    default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                                                };
                                            @endphp

                                            <!-- Chevron Icon -->
                                            <div class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-90': openSection === '{{ $sectionKey }}' }">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>

                                            <!-- Section Icon -->
                                            <div class="w-5 h-5 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-200">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $sectionIcon !!}
                                                </svg>
                                            </div>

                                            <h4 class="text-xs font-bold text-gray-300 uppercase tracking-wider group-hover:text-white transition-colors duration-200">
                                                {{ $section['title'] }}
                                            </h4>
                                        </button>

                                        <!-- Section Items - Collapsible -->
                                        <div
                                            x-show="openSection === '{{ $sectionKey }}'"
                                            x-collapse
                                            class="space-y-0.5 ml-6 mt-1"
                                        >
                                            @if(!empty($section['children']))
                                                {{-- New structure with explicit children --}}
                                                @foreach($section['children'] as $child)
                                                    @php
                                                        $isActive = $currentFile === $child['file'];
                                                    @endphp
                                                    <button
                                                        type="button"
                                                        wire:click="selectDocument('{{ $child['file'] }}')"
                                                        @click="userInteracted = false"
                                                        class="group w-full text-left px-3 py-2 rounded-lg text-xs transition-all duration-200 relative overflow-hidden cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-indigo-500/20 to-purple-500/20 text-white font-medium shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <!-- Bullet Point -->
                                                            <div class="w-1 h-1 rounded-full {{ $isActive ? 'bg-indigo-400' : 'bg-gray-600 group-hover:bg-gray-500' }} transition-colors flex-shrink-0"></div>

                                                            <span class="flex-1">{{ $child['title'] }}</span>

                                                            <!-- Arrow Icon on Hover/Active -->
                                                            <svg class="w-3.5 h-3.5 transition-all duration-200 {{ $isActive ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            @else
                                                {{-- Legacy structure with files --}}
                                                @foreach($section['files'] as $file)
                                                    @php
                                                        $fileName = basename($file, '.md');
                                                        $isActive = $currentFile === $file;
                                                    @endphp
                                                    <button
                                                        type="button"
                                                        wire:click="selectDocument('{{ $file }}')"
                                                        @click="userInteracted = false"
                                                        class="group w-full text-left px-3 py-2 rounded-lg text-xs transition-all duration-200 relative overflow-hidden cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-indigo-500/20 to-purple-500/20 text-white font-medium shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <!-- Bullet Point -->
                                                            <div class="w-1 h-1 rounded-full {{ $isActive ? 'bg-indigo-400' : 'bg-gray-600 group-hover:bg-gray-500' }} transition-colors flex-shrink-0"></div>

                                                            <span class="flex-1">{{ ucfirst(str_replace('_', ' ', $fileName)) }}</span>

                                                            <!-- Arrow Icon on Hover/Active -->
                                                            <svg class="w-3.5 h-3.5 transition-all duration-200 {{ $isActive ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Content Area -->
            <main class="flex-1 min-w-0">
                <article class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-8 shadow-xl">
                    @if($renderedContent)
                        <div class="markdown-content overflow-x-auto"
                             x-data="{
                                 highlight() {
                                     setTimeout(() => {
                                         if (window.highlightDslCode) {
                                             window.highlightDslCode();
                                         }
                                     }, 50);
                                 }
                             }"
                             x-init="highlight()"
                             @content-updated.window="highlight()">
                            {!! $renderedContent !!}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-400">{{ __('Select a document from the navigation') }}</p>
                        </div>
                    @endif
                </article>
            </main>
        </div>
    </div>

    @script
    <script>
        // Re-highlight code blocks after Livewire updates
        Livewire.hook('morph.updated', ({ el, component }) => {
            setTimeout(() => {
                if (window.highlightDslCode) {
                    window.highlightDslCode();
                }
            }, 100);
        });

        // Also trigger on commit
        Livewire.hook('commit', ({ component, respond }) => {
            setTimeout(() => {
                if (window.highlightDslCode) {
                    window.highlightDslCode();
                }
            }, 100);
        });
    </script>
    @endscript

    <!-- Search Modal -->
    <div
        x-data="{
            open: false,
            selectedIndex: -1,
            closing: false,
            get resultsCount() {
                return {{ count($searchResults) }};
            },
            selectNext() {
                if (this.resultsCount === 0) return;
                if (this.selectedIndex < this.resultsCount - 1) {
                    this.selectedIndex++;
                } else if (this.selectedIndex === -1 && this.resultsCount > 0) {
                    this.selectedIndex = 0;
                }
                this.scrollToSelected();
            },
            selectPrev() {
                if (this.resultsCount === 0) return;
                if (this.selectedIndex > 0) {
                    this.selectedIndex--;
                } else if (this.selectedIndex === -1 && this.resultsCount > 0) {
                    this.selectedIndex = this.resultsCount - 1;
                }
                this.scrollToSelected();
            },
            scrollToSelected() {
                this.$nextTick(() => {
                    const selected = this.$refs.resultsContainer?.querySelector('[data-index=\'' + this.selectedIndex + '\']');
                    if (selected) {
                        selected.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                });
            },
            selectCurrent() {
                if (this.selectedIndex >= 0 && this.selectedIndex < this.resultsCount) {
                    const selected = this.$refs.resultsContainer?.querySelector('[data-index=\'' + this.selectedIndex + '\']');
                    if (selected) {
                        selected.click();
                    }
                }
            },
            closeModal() {
                this.closing = true;
                this.open = false;
                this.reset();
                setTimeout(() => {
                    this.$wire.clearSearch();
                    this.closing = false;
                }, 300);
            },
            reset() {
                this.selectedIndex = -1;
            }
        }"
        x-on:open-search-modal.window="if (!closing) { open = true; reset(); $nextTick(() => $refs.searchInput.focus()); }"
        x-on:keydown.escape.window="if (open && !closing) { closeModal(); }"
        x-on:keydown.ctrl.k.window.prevent="if (!closing) { open = true; reset(); $nextTick(() => $refs.searchInput.focus()); }"
        x-on:keydown.meta.k.window.prevent="if (!closing) { open = true; reset(); $nextTick(() => $refs.searchInput.focus()); }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="if (!closing) { closeModal(); }"
            class="fixed inset-0 bg-black/90"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-screen items-start justify-center p-4 pt-16">
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                @keydown.arrow-down.prevent="selectNext()"
                @keydown.arrow-up.prevent="selectPrev()"
                @keydown.enter.prevent="selectCurrent()"
                class="relative w-full max-w-xl bg-gray-900 border border-gray-700 rounded-xl shadow-2xl overflow-hidden"
            >
                <!-- Search Input -->
                <div class="p-4 border-b border-gray-700">
                    <div class="relative">
                        <!-- Lupe Icon -->
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Input Field -->
                        <input
                            x-ref="searchInput"
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            placeholder="{{ __('Search documentation...') }}"
                            @input="reset()"
                            @keydown.arrow-down.prevent="selectNext()"
                            @keydown.arrow-up.prevent="selectPrev()"
                            @keydown.enter.prevent="selectCurrent()"
                            class="w-full pl-10 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors"
                            style="padding-right: 2.5rem;"
                        >

                        <!-- Clear Button -->
                        @if($searchQuery)
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button
                                    @click="reset(); $wire.clearSearch();"
                                    type="button"
                                    class="text-gray-400 hover:text-white transition-colors p-1 rounded hover:bg-gray-700"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Search Results -->
                <div class="max-h-[50vh] overflow-y-auto" x-ref="resultsContainer">
                    @if($searchQuery && strlen($searchQuery) >= 3)
                        @if(empty($searchResults))
                            <!-- No Results -->
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-400 text-sm">
                                    {{ __('No results for') }} "<span class="text-white font-medium">{{ $searchQuery }}</span>"
                                </p>
                            </div>
                        @else
                            <!-- Results List -->
                            <div class="p-2">
                                <div class="px-3 py-2 text-xs text-gray-400">
                                    {{ count($searchResults) }} {{ count($searchResults) === 1 ? __('result') : __('results') }}
                                </div>

                                @foreach($searchResults as $index => $result)
                                    <button
                                        type="button"
                                        data-index="{{ $index }}"
                                        @click="
                                            if (!closing) {
                                                closing = true;
                                                open = false;
                                                $wire.selectDocument('{{ $result['file'] }}');
                                                setTimeout(() => {
                                                    $wire.clearSearch();
                                                    reset();
                                                    closing = false;
                                                }, 300);
                                            }
                                        "
                                        :class="selectedIndex === {{ $index }} ? 'bg-indigo-500/20 border-indigo-500' : 'bg-transparent border-transparent hover:bg-gray-800'"
                                        class="group w-full text-left p-3 rounded-lg transition-all duration-150 border"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div
                                                :class="selectedIndex === {{ $index }} ? 'from-indigo-500/30 to-purple-500/30' : 'from-indigo-500/10 to-purple-500/10 group-hover:from-indigo-500/20 group-hover:to-purple-500/20'"
                                                class="w-9 h-9 rounded-lg bg-gradient-to-br flex items-center justify-center flex-shrink-0 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-xs text-indigo-400 font-medium">{{ $result['section'] }}</span>
                                                    <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    <span class="text-xs text-gray-500">{{ $result['title'] }}</span>
                                                </div>
                                                <div class="text-sm text-gray-300 line-clamp-2">
                                                    {!! $result['snippet'] !!}
                                                </div>
                                            </div>
                                            <svg
                                                :class="selectedIndex === {{ $index }} ? 'text-indigo-400' : 'text-gray-600 group-hover:text-gray-500'"
                                                class="w-4 h-4 transition-colors flex-shrink-0 mt-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </button>

                                    @if($index < count($searchResults) - 1)
                                        <div class="mx-3 my-1 border-t border-gray-800"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @elseif($searchQuery && strlen($searchQuery) < 3)
                        <!-- Minimum Characters Message -->
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-400 text-sm">
                                {{ __('Type at least 3 characters to search') }}
                            </p>
                        </div>
                    @else
                        <!-- Initial State -->
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-gray-400 text-sm">
                                {{ __('Start typing to search the documentation') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-4 py-2.5 bg-gray-800 border-t border-gray-700 flex items-center justify-between text-xs text-gray-400">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 bg-gray-700 border border-gray-600 rounded text-xs text-gray-300">↑</kbd>
                            <kbd class="px-1.5 py-0.5 bg-gray-700 border border-gray-600 rounded text-xs text-gray-300">↓</kbd>
                            <span class="ml-1">{{ __('to navigate') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 bg-gray-700 border border-gray-600 rounded text-xs text-gray-300">↵</kbd>
                            <span class="ml-1">{{ __('to select') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-gray-700 border border-gray-600 rounded text-xs text-gray-300">ESC</kbd>
                        <span class="ml-1">{{ __('to close') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

