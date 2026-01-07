@props([
    'id' => null,
    'name' => null,
    'type' => 'text',
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'wireModel' => null,
    'wireLive' => false,
    'debounce' => null,
    'hint' => null,
    'error' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'prefix' => null,
    'suffix' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'pattern' => null,
    'autocomplete' => null,
    'autofocus' => false,
    'value' => null,
])

@php
    // Generate unique ID if not provided
    $inputId = $id ?? $name ?? 'input-' . uniqid();

    // Build wire:model directive
    $wireModelDirective = null;
    if ($wireModel) {
        if ($wireLive && $debounce) {
            $wireModelDirective = "wire:model.live.debounce.{$debounce}ms=\"{$wireModel}\"";
        } elseif ($wireLive) {
            $wireModelDirective = "wire:model.live=\"{$wireModel}\"";
        } else {
            $wireModelDirective = "wire:model=\"{$wireModel}\"";
        }
    }

    // Determine error name for validation
    $errorName = $error ?? $wireModel ?? $name;

    // Build input classes
    $inputClasses = 'w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition';

    // Add padding for icon, prefix, or suffix
    if ($icon) {
        if ($iconPosition === 'left') {
            $inputClasses .= ' pl-10';
        } else {
            $inputClasses .= ' pr-10';
        }
    }
    if ($prefix) {
        $inputClasses .= ' pl-12';
    }
    if ($suffix) {
        $inputClasses .= ' pr-16';
    }

    // Add disabled/readonly styles
    if ($disabled) {
        $inputClasses .= ' opacity-50 cursor-not-allowed';
    }
    if ($readonly) {
        $inputClasses .= ' bg-gray-900/50';
    }

    // Merge with custom classes from attributes
    $inputClasses = $attributes->get('class') ? $inputClasses . ' ' . $attributes->get('class') : $inputClasses;
@endphp

<div class="w-full" {{ $attributes->only(['x-data', 'x-show', 'x-if', 'wire:key']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="text-gray-400 text-sm">{{ $prefix }}</span>
            </div>
        @endif

        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            @if($name) name="{{ $name }}" @endif
            @if($wireModelDirective) {!! $wireModelDirective !!} @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step !== null) step="{{ $step }}" @endif
            @if($pattern) pattern="{{ $pattern }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($autofocus) autofocus @endif
            @if($value !== null) value="{{ $value }}" @endif
            class="{{ $inputClasses }}"
            {{ $attributes->except(['class', 'x-data', 'x-show', 'x-if', 'wire:key']) }}
        >

        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <span class="text-gray-400 text-sm">{{ $suffix }}</span>
            </div>
        @endif

        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
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

