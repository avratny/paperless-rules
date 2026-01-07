<div x-data="{ open: false }">
    <!-- Trigger Button -->
    <button x-on:click="open = true" type="button" class="text-xs text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('Syntax Help') }}
    </button>

    <!-- Modal - Teleported to body to avoid form submission issues -->
    <template x-teleport="body">
        <div x-show="open"
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
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" x-on:click="open = false"></div>

            <!-- Modal Content -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-on:click.stop
                     x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">

                    <!-- Header -->
                    <div class="sticky top-0 bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">{{ __('DSL Syntax Help') }}</h3>
                        <button type="button" x-on:click="open = false" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                <!-- Content -->
                <div class="p-6 space-y-6">

                    <!-- Basic Structure -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('Basic Structure') }}</h4>
                        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 font-mono text-sm">
                            <pre class="text-gray-300"><span class="text-purple-400">WHEN</span> <span class="text-gray-500">condition</span>
<span class="text-purple-400">THEN</span>
<span class="text-purple-400">DO</span> <span class="text-green-400">action</span>
<span class="text-purple-400">END</span></pre>
                        </div>
                    </section>

                    <!-- Keywords -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('Keywords') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">WHEN</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Starts the condition block') }}</p>
                            </div>
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">THEN</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Actions when condition is true') }}</p>
                            </div>
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">ELSE</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Actions when condition is false') }}</p>
                            </div>
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">END</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Ends the rule') }}</p>
                            </div>
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">LET</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Defines a variable') }}</p>
                            </div>
                            <div class="bg-gray-800/30 border border-gray-700/50 rounded-lg p-3">
                                <code class="text-purple-400 font-mono">DO</code>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Executes an action') }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Document Properties -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('Document Properties') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.title</code>
                                <span class="text-gray-400">{{ __('Document title') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.content</code>
                                <span class="text-gray-400">{{ __('Document content (OCR text)') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.tags</code>
                                <span class="text-gray-400">{{ __('Array of tag names') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.document_type</code>
                                <span class="text-gray-400">{{ __('Document type name (string)') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.correspondent</code>
                                <span class="text-gray-400">{{ __('Correspondent name (string)') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-blue-400 font-mono mr-3 min-w-[200px]">document.created_date</code>
                                <span class="text-gray-400">{{ __('Creation date') }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- String Functions -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('String Functions') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">str_contains(str, needle)</code>
                                <span class="text-gray-400">{{ __('Checks if string contains') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">str_starts_with(str, prefix)</code>
                                <span class="text-gray-400">{{ __('Checks if string starts with') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">lower(str)</code>
                                <span class="text-gray-400">{{ __('Converts to lowercase') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">upper(str)</code>
                                <span class="text-gray-400">{{ __('Converts to uppercase') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">askOllamaAi(prompt)</code>
                                <span class="text-gray-400">{{ __('Ask Ollama AI with a prompt') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">askOllamaAiForCreationDate(content)</code>
                                <span class="text-gray-400">{{ __('Extract creation date from document') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">askOllamaAiForDocumentNumber(content)</code>
                                <span class="text-gray-400">{{ __('Extract document number from document') }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Date Functions -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('Date Functions') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <code class="text-green-400 font-mono mr-3 min-w-[250px]">reformatDate(date, format)</code>
                                <span class="text-gray-400">{{ __('Reformat date to different format') }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Actions -->
                    <section>
                        <h4 class="text-lg font-semibold text-indigo-400 mb-3">{{ __('Actions') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">addTag(tag: "name")</code>
                                <span class="text-gray-400">{{ __('Adds a tag') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">setDocumentType(type: "name")</code>
                                <span class="text-gray-400">{{ __('Sets the document type') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">setTitle(title: "new title")</code>
                                <span class="text-gray-400">{{ __('Sets the title') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">createTag(name: "name")</code>
                                <span class="text-gray-400">{{ __('Creates a new tag') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">createDocumentType(name: "name")</code>
                                <span class="text-gray-400">{{ __('Creates a new document type') }}</span>
                            </div>
                            <div class="flex items-start">
                                <code class="text-pink-400 font-mono mr-3 min-w-[300px]">createCorrespondent(name: "name")</code>
                                <span class="text-gray-400">{{ __('Creates a new correspondent') }}</span>
                            </div>
                        </div>
                    </section>

                </div>

            </div>
        </div>
    </div>
    </template>
</div>

