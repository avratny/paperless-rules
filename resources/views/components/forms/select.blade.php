@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'wireModel' => null,
    'wireLive' => false,
    'hint' => null,
    'error' => null,
    'options' => [],
    'placeholder' => null,
])

@php
    // Generate unique ID if not provided
    $selectId = $id ?? $name ?? 'select-' . uniqid();

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
    $errorName = $error ?? $wireModel ?? $name;

    // Build select classes with proper padding for chevron
    $selectClasses = 'w-full pl-4 pr-10 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none cursor-pointer';

    // Add disabled styles
    if ($disabled) {
        $selectClasses .= ' opacity-50 cursor-not-allowed';
    }

    // Merge with custom classes from attributes
    $selectClasses = $attributes->get('class') ? $selectClasses . ' ' . $attributes->get('class') : $selectClasses;
@endphp

<div class="w-full" {{ $attributes->only(['x-data', 'x-show', 'x-if', 'wire:key']) }}>
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $selectId }}"
            @if($name) name="{{ $name }}" @endif
            @if($wireModelDirective) {!! $wireModelDirective !!} @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="{{ $selectClasses }}"
            {{ $attributes->except(['class', 'x-data', 'x-show', 'x-if', 'wire:key']) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @if(!empty($options))
                @foreach($options as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

        <!-- Chevron Icon -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($hint && !$errorName)
        <p class="mt-2 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @if($errorName)
        @error($errorName)
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror

        @if($hint && !$errors->has($errorName))
            <p class="mt-2 text-xs text-gray-500">{{ $hint }}</p>
        @endif
    @endif
</div>

