import { EditorView, basicSetup } from 'codemirror';
import { EditorState } from '@codemirror/state';
import { json } from '@codemirror/lang-json';
import { oneDark } from '@codemirror/theme-one-dark';
import { dsl } from './dsl-language';

// Export for use in Alpine components
// The initialization functions are in public/js/codemirror-init.js to avoid Alpine.js parsing issues
window.CodeMirror = {
    EditorView,
    EditorState,
    basicSetup,
    json,
    oneDark,
    dsl
};

