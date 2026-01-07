@props(['value' => '', 'language' => 'json', 'height' => '400px', 'wireModel' => null])

@php
    $editorId = 'codemirror-' . uniqid();
@endphp

{{-- Updated: 2026-01-01 v7 - Script before container --}}
<div
    x-data="{
        value: @if($wireModel) @entangle($wireModel) @else '{{ addslashes($value) }}' @endif,
        editorId: '{{ $editorId }}'
    }"
    wire:ignore
    class="codemirror-wrapper"
>
    <script>
        // This script runs BEFORE the container is added to DOM
        (function() {
            const editorId = '{{ $editorId }}';

            console.log('Pre-registering editor:', editorId);

            // Wait for cmInit to be available, then register config
            function registerConfig() {
                if (window.cmInit) {
                    console.log('cmInit available, waiting for Alpine and container...');

                    // Now wait for container and Alpine
                    function waitAndInit() {
                        const container = document.getElementById(editorId);
                        const parent = container ? container.closest('[x-data]') : null;

                        // Alpine.js v3 uses _x_dataStack
                        const hasAlpine = parent && parent._x_dataStack && parent._x_dataStack.length > 0;

                        console.log('Checking:', { container: !!container, parent: !!parent, hasAlpine });

                        if (container && parent && hasAlpine) {
                            // Get Alpine data from the stack
                            const alpineData = parent._x_dataStack[0];

                            console.log('Alpine data found:', alpineData);
                            console.log('Initializing with Alpine value:', alpineData.value);

                            // Initialize editor with Alpine value
                            window.cmInit(editorId, '{{ $language }}', '{{ $height }}', alpineData.value || '');

                            // Listen for changes FROM editor TO Alpine
                            container.addEventListener('cm-change', function(e) {
                                const newVal = e.target.getAttribute('data-cm-value');
                                console.log('Editor changed to:', newVal);
                                if (newVal !== alpineData.value) {
                                    console.log('Updating Alpine value');
                                    alpineData.value = newVal;
                                }
                            });

                            // Watch for changes FROM Alpine TO editor
                            let lastAlpineValue = alpineData.value;
                            setInterval(function() {
                                const ready = container.getAttribute('data-cm-ready');
                                if (ready && window.cmUpdate) {
                                    if (alpineData.value !== lastAlpineValue) {
                                        console.log('Alpine value changed to:', alpineData.value);
                                        const currentEditorValue = container.getAttribute('data-cm-value') || '';
                                        if (alpineData.value !== currentEditorValue) {
                                            console.log('Updating editor');
                                            window.cmUpdate(editorId, alpineData.value);
                                        }
                                        lastAlpineValue = alpineData.value;
                                    }
                                }
                            }, 100);

                            console.log('Editor sync setup complete');
                        } else {
                            setTimeout(waitAndInit, 50);
                        }
                    }

                    waitAndInit();
                } else {
                    setTimeout(registerConfig, 50);
                }
            }

            registerConfig();
        })();
    </script>

    <div
        id="{{ $editorId }}"
        class="cm-container border border-gray-700/50 rounded-lg overflow-hidden"
        x-ignore
    ></div>
</div>
