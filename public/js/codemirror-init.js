// CodeMirror initialization - uses MutationObserver to avoid Alpine.js interference
// This runs completely independently of Alpine.js
(function() {
    'use strict';

    const editorInstances = new Map();
    const pendingInitializations = new Map();

    // Suppress Lezer highlight warnings
    const originalWarn = console.warn;
    console.warn = function() {
        const msg = arguments[0];
        if (typeof msg === 'string' && msg.includes('Modifier function used at start of tag')) {
            return; // Suppress this specific warning
        }
        originalWarn.apply(console, arguments);
    };

    // Wait for CodeMirror to be available
    function waitForCodeMirror(callback) {
        if (typeof window.CodeMirror !== 'undefined' && window.CodeMirror.EditorView) {
            callback();
        } else {
            setTimeout(function() { waitForCodeMirror(callback); }, 100);
        }
    }

    waitForCodeMirror(function() {
        const CM = window.CodeMirror;
        const EditorView = CM.EditorView;
        const EditorState = CM.EditorState;

        // Build theme styles
        function buildThemeStyles(height) {
            const styles = {};
            const a = String.fromCharCode(38);

            // Editor base styles
            styles[a] = { height: height, fontSize: '14px', backgroundColor: 'rgba(17, 24, 39, 0.5)', color: '#e5e7eb' };
            styles['.cm-content'] = { caretColor: '#a78bfa', fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace' };
            styles['.cm-cursor, .cm-dropCursor'] = { borderLeftColor: '#a78bfa' };
            styles['.cm-selectionBackground, ' + a + '.cm-focused .cm-selectionBackground'] = { backgroundColor: 'rgba(99, 102, 241, 0.3)' };
            styles['.cm-activeLine'] = { backgroundColor: 'rgba(31, 41, 55, 0.5)' };
            styles['.cm-gutters'] = { backgroundColor: 'rgba(17, 24, 39, 0.8)', color: '#6b7280', border: 'none', borderRight: '1px solid rgba(55, 65, 81, 0.5)' };
            styles['.cm-activeLineGutter'] = { backgroundColor: 'rgba(31, 41, 55, 0.5)', color: '#9ca3af' };
            styles['.cm-lineNumbers .cm-gutterElement'] = { padding: '0 8px 0 8px', minWidth: '40px' };
            styles['.cm-scroller'] = { overflow: 'auto', fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace' };
            styles[a + '.cm-focused'] = { outline: '2px solid rgba(99, 102, 241, 0.5)', outlineOffset: '2px' };
            styles[a + '.cm-focused .cm-selectionBackground'] = { backgroundColor: 'rgba(99, 102, 241, 0.3)' };

            // StreamLanguage token styles - uses .tok-* classes
            // Colors matched to Syntax-Hilfe component:
            // - Keywords (WHEN, THEN, etc.): purple-400 (#c084fc)
            // - Functions (str_contains, etc.): green-400 (#4ade80) - uses 'def' token
            // - Properties (document.x): blue-400 (#60a5fa) - uses 'atom' token
            // - Actions (addTag, etc.): pink-400 (#f472b6) - uses 'meta' token
            // - Strings: teal-400 (#2dd4bf)
            styles['.tok-keyword'] = { color: '#c084fc', fontWeight: '600' };
            styles['.tok-string'] = { color: '#2dd4bf' };
            styles['.tok-number'] = { color: '#60a5fa' };
            styles['.tok-bool'] = { color: '#f472b6' };
            styles['.tok-null'] = { color: '#fb923c' };
            styles['.tok-comment'] = { color: '#6b7280', fontStyle: 'italic' };
            styles['.tok-def'] = { color: '#4ade80', fontWeight: '500' };
            styles['.tok-meta'] = { color: '#f472b6', fontWeight: '500' };
            styles['.tok-atom'] = { color: '#60a5fa' };
            styles['.tok-variableName'] = { color: '#60a5fa' };
            styles['.tok-operator'] = { color: '#9ca3af' };
            styles['.tok-punctuation'] = { color: '#9ca3af' };
            styles['.tok-bracket'] = { color: '#d1d5db' };

            // Also add .ͼ* styles for CodeMirror 6 default highlighting classes
            styles['.ͼc'] = { color: '#c084fc', fontWeight: '600' }; // keyword
            styles['.ͼd'] = { color: '#2dd4bf' }; // string
            styles['.ͼe'] = { color: '#60a5fa' }; // number

            return styles;
        }

        // Initialize an editor - called from cmInit
        function initEditor(container) {
            const editorId = container.id;
            console.log('initEditor called for:', editorId);

            if (!editorId) {
                console.error('Container has no ID');
                return;
            }

            if (editorInstances.has(editorId)) {
                console.log('Editor already initialized:', editorId);
                return;
            }

            const config = pendingInitializations.get(editorId);
            if (!config) {
                console.error('No config found for:', editorId);
                return;
            }

            console.log('Initializing editor with config:', config);

            const basicSetup = CM.basicSetup;
            const json = CM.json;
            const dsl = CM.dsl;

            container.innerHTML = '';
            const themeStyles = buildThemeStyles(config.height);
            const theme = EditorView.theme(themeStyles, { dark: true });
            const langExt = config.language === 'dsl' ? dsl() : json();

            const exts = [basicSetup, langExt, theme];
            const updateLis = EditorView.updateListener.of(function(upd) {
                if (upd.docChanged) {
                    container.setAttribute('data-cm-value', upd.state.doc.toString());
                    container.dispatchEvent(new Event('cm-change', { bubbles: true }));
                }
            });
            exts.push(updateLis);

            // Auto-format DSL code on load
            let initialValue = config.initialValue || '';
            if (config.language === 'dsl' && initialValue.trim() !== '') {
                initialValue = formatDslCode(initialValue);
            }

            const cfg = { doc: initialValue, extensions: exts };
            const st = EditorState.create(cfg);
            const view = new EditorView({ state: st, parent: container });

            editorInstances.set(editorId, view);
            container.setAttribute('data-cm-value', initialValue);
            container.setAttribute('data-cm-ready', 'true');
            console.log('Editor initialized successfully:', editorId, 'with value:', initialValue);
        }

        // DSL code formatter - moved up so it can be used in initEditor
        function formatDslCode(code) {
            const lines = code.split('\n');
            const formatted = [];
            let indentLevel = 0;
            const indentStr = '  '; // 2 spaces

            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].trim();

                // Skip empty lines but preserve them
                if (line === '') {
                    formatted.push('');
                    continue;
                }

                // Check line types
                const isWhen = /^WHEN\s+/i.test(line);
                const isThen = /^THEN$/i.test(line);
                const isElse = /^ELSE$/i.test(line);
                const isEnd = /^END$/i.test(line);

                // Decrease indent BEFORE outputting ELSE or END
                if ((isElse || isEnd) && indentLevel > 0) {
                    indentLevel--;
                }

                // Add the line with current indentation
                formatted.push(indentStr.repeat(indentLevel) + line);

                // Increase indent AFTER THEN or ELSE (not after WHEN!)
                if (isThen || isElse) {
                    indentLevel++;
                }
            }

            return formatted.join('\n');
        }

        // Expose API - cmInit now directly initializes the editor
        window.cmInit = function(id, lang, h, val) {
            console.log('cmInit called:', { id, lang, h, val });

            // Store config first
            pendingInitializations.set(id, { language: lang, height: h, initialValue: val });

            // Try to initialize immediately
            const container = document.getElementById(id);
            if (container) {
                console.log('Container found, initializing directly...');
                initEditor(container);
            } else {
                console.log('Container not found yet, will retry...');
                // Wait for container to appear
                const waitForContainer = function() {
                    const c = document.getElementById(id);
                    if (c) {
                        initEditor(c);
                    } else {
                        setTimeout(waitForContainer, 50);
                    }
                };
                setTimeout(waitForContainer, 50);
            }
        };

        window.cmUpdate = function(id, val) {
            console.log('cmUpdate called for:', id, 'with value:', val);
            const ed = editorInstances.get(id);
            const container = document.getElementById(id);
            if (ed && val !== ed.state.doc.toString()) {
                console.log('Updating editor content');
                ed.dispatch({ changes: { from: 0, to: ed.state.doc.length, insert: val || '' } });
                if (container) {
                    container.setAttribute('data-cm-value', val || '');
                }
            }
        };

        // Format DSL code with proper indentation
        window.cmFormat = function(id) {
            console.log('cmFormat called for:', id);
            const ed = editorInstances.get(id);
            const container = document.getElementById(id);
            if (!ed) {
                console.error('Editor not found:', id);
                return;
            }

            const code = ed.state.doc.toString();
            const formatted = formatDslCode(code);

            if (formatted !== code) {
                ed.dispatch({ changes: { from: 0, to: ed.state.doc.length, insert: formatted } });
                if (container) {
                    container.setAttribute('data-cm-value', formatted);
                    container.dispatchEvent(new Event('cm-change', { bubbles: true }));
                }
                console.log('Code formatted successfully');
            }
        };

        console.log('CodeMirror API ready');
    });
})();

