@props([
    'label' => null,
    'hint' => null,
    'required' => false,
    'options' => [], // Array of ['value' => '', 'label' => '', 'description' => '', 'icon' => '']
    'wireModel' => null,
    'wireLive' => false,
    'name' => null,
])

@php
    // Build wire:model directive
    $wireModelDirective = null;
    if ($wireModel) {
        if ($wireLive) {
            $wireModelDirective = "wire:model.live=\"{$wireModel}\"";
        } else {
            $wireModelDirective = "wire:model=\"{$wireModel}\"";
        }
    }

    // Determine error name for validation
    $errorName = $wireModel ?? $name;
@endphp

<div class="w-full" {{ $attributes->only(['x-data', 'x-show', 'x-if', 'wire:key']) }}>
    @if($label)
        <label class="block text-sm font-medium text-gray-300 mb-3">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ selected: @entangle($wireModel) }">
        @foreach($options as $option)
            @php
                $value = $option['value'] ?? '';
                $optionLabel = $option['label'] ?? '';
                $description = $option['description'] ?? '';
                $icon = $option['icon'] ?? '';
            @endphp

            <label class="relative flex cursor-pointer group">
                <input
                    type="radio"
                    @if($name) name="{{ $name }}" @endif
                    value="{{ $value }}"
                    @if($wireModelDirective) {!! $wireModelDirective !!} @endif
                    x-model="selected"
                    class="peer sr-only"
                >
                <div
                    class="w-full flex items-start gap-4 p-4 bg-gray-800/50 border-2 rounded-xl transition-all duration-200 group-hover:border-gray-600"
                    :class="selected === '{{ $value }}' ? 'border-indigo-500 bg-indigo-500/10' : 'border-gray-700/50'"
                >
                    @if($icon)
                        <div class="flex-shrink-0 mt-0.5">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                                :class="selected === '{{ $value }}' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-gray-700/50 text-gray-400'"
                            >
                                {!! $icon !!}
                            </div>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-white">{{ $optionLabel }}</span>
                            <div
                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-auto"
                                :class="selected === '{{ $value }}' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-600'"
                            >
                                <div
                                    class="w-2.5 h-2.5 rounded-full bg-white transition-opacity"
                                    :class="selected === '{{ $value }}' ? 'opacity-100' : 'opacity-0'"
                                ></div>
                            </div>
                        </div>
                        @if($description)
                            <p class="mt-1 text-xs text-gray-400">{{ $description }}</p>
                        @endif
                    </div>
                </div>
            </label>
        @endforeach
    </div>

    @if($hint)
        <p class="mt-2 text-xs text-gray-400">{{ $hint }}</p>
    @endif

    @error($errorName)
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

