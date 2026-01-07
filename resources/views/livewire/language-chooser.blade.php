<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Language Button -->
    <button
        @click="open = !open"
        class="flex items-center space-x-2 px-3 py-2 cursor-pointer rounded-lg bg-gray-800/50 hover:bg-gray-800 border border-gray-700/50 hover:border-gray-600 transition duration-200"
    >
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>
        <span class="text-sm font-medium text-gray-300 uppercase">{{ $currentLocale }}</span>
        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        class="absolute right-0 mt-2 w-48 bg-gray-900 border border-gray-800 rounded-xl shadow-xl shadow-black/20 overflow-hidden z-50"
        style="display: none;"
    >
        <div class="py-1">
            <!-- Deutsch -->
            <button
                type="button"
                wire:click="switchLanguage('de')"
                class="w-full flex items-center px-4 py-3 cursor-pointer hover:bg-gray-800 transition duration-150 {{ $currentLocale === 'de' ? 'bg-gray-800/50' : '' }}"
            >
                <span class="text-2xl mr-3">🇩🇪</span>
                <div class="flex-1 text-left">
                    <div class="text-sm font-medium text-white">Deutsch</div>
                    <div class="text-xs text-gray-400">German</div>
                </div>
                @if($currentLocale === 'de')
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </button>

            <!-- English -->
            <button
                type="button"
                wire:click="switchLanguage('en')"
                class="w-full flex items-center px-4 py-3 cursor-pointer hover:bg-gray-800 transition duration-150 {{ $currentLocale === 'en' ? 'bg-gray-800/50' : '' }}"
            >
                <span class="text-2xl mr-3">🇬🇧</span>
                <div class="flex-1 text-left">
                    <div class="text-sm font-medium text-white">English</div>
                    <div class="text-xs text-gray-400">Englisch</div>
                </div>
                @if($currentLocale === 'en')
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </button>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('locale-changed', () => {
        window.location.reload();
    });
</script>
@endscript


