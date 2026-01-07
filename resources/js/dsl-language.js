import { StreamLanguage, LanguageSupport, HighlightStyle, syntaxHighlighting } from '@codemirror/language';
import { tags } from '@lezer/highlight';

// Simple tokenizer for our DSL
const dslTokenizer = {
    startState: () => ({ inString: false }),

    token: (stream, state) => {
        // Skip whitespace
        if (stream.eatSpace()) return null;

        // Comments
        if (stream.match('//')) {
            stream.skipToEnd();
            return 'comment';
        }

        // Strings - handle properly
        if (stream.match('"')) {
            while (!stream.eol()) {
                if (stream.next() === '"') break;
            }
            return 'string';
        }

        // Keywords
        if (stream.match(/^(WHEN|THEN|ELSE|END|LET|DO)\b/)) {
            return 'keyword';
        }

        // Operators
        if (stream.match(/^(and|or|not)\b/)) {
            return 'keyword';
        }

        // Arithmetic, comparison, and concatenation operators
        if (stream.match(/^[+\-*/<>=!~]+/)) {
            return 'operator';
        }

        // Boolean
        if (stream.match(/^(true|false)\b/)) {
            return 'bool';
        }

        // Null
        if (stream.match(/^null\b/)) {
            return 'null';
        }

        // Numbers
        if (stream.match(/^-?\d+\.?\d*/)) {
            return 'number';
        }

        // Actions (pink) - must be before functions, use 'meta' tag
        if (stream.match(/^(addTag|removeTag|setDocumentType|setCorrespondent|setCustomField|setTitle|createTag|createDocumentType|createCorrespondent)\(/)) {
            stream.backUp(1);
            return 'meta';
        }

        // Functions (green) - use 'def' tag
        if (stream.match(/^(lower|upper|trim|len|str_contains|str_starts_with|str_ends_with|replace|regex|in|count|askOllamaAi|askOllamaAiForCreationDate|askOllamaAiForDocumentNumber|reformatDate)\(/)) {
            stream.backUp(1);
            return 'def';
        }

        // Document properties (blue) - use 'atom' tag
        // Must check for document.property BEFORE checking for just 'document'
        if (stream.match(/^document\.(id|title|content|tags|created_date|modified|added|original_file_name|archive_serial_number|document_type|correspondent)\b/)) {
            return 'atom';
        }

        // 'now' variable
        if (stream.match(/^now\b/)) {
            return 'variableName';
        }

        // Default - consume one character
        stream.next();
        return null;
    }
};

// DSL Language definition
export const dslLanguage = StreamLanguage.define(dslTokenizer);

// Custom highlight style for DSL
// Colors matched to Syntax-Hilfe component (dsl-syntax-help.blade.php):
// - Keywords (WHEN, THEN, etc.): text-purple-400 (#c084fc)
// - Functions (str_contains, etc.): text-green-400 (#4ade80)
// - Properties (document.x): text-blue-400 (#60a5fa)
// - Actions (addTag, etc.): text-pink-400 (#f472b6)
// - Strings: text-teal-400 (#2dd4bf)
export const dslHighlightStyle = HighlightStyle.define([
    // Keywords - purple
    { tag: tags.keyword, color: '#c084fc', fontWeight: '600' },
    // Strings - teal
    { tag: tags.string, color: '#2dd4bf' },
    // Numbers - blue
    { tag: tags.number, color: '#60a5fa' },
    // Booleans - pink
    { tag: tags.bool, color: '#f472b6' },
    // Null - orange
    { tag: tags.null, color: '#fb923c' },
    // Comments - gray italic
    { tag: tags.comment, color: '#6b7280', fontStyle: 'italic' },
    // Functions (def token) - green
    { tag: tags.definition(tags.variableName), color: '#4ade80', fontWeight: '500' },
    // Actions (meta token) - pink
    { tag: tags.meta, color: '#f472b6', fontWeight: '500' },
    // Properties (atom token) - blue
    { tag: tags.atom, color: '#60a5fa' },
    // Variables - blue
    { tag: tags.variableName, color: '#60a5fa' },
    // Operators - gray
    { tag: tags.operator, color: '#9ca3af' },
    // Punctuation - gray
    { tag: tags.punctuation, color: '#9ca3af' },
]);

// Keywords for autocomplete
const keywords = [
    { label: 'WHEN', type: 'keyword', info: 'Start condition block' },
    { label: 'THEN', type: 'keyword', info: 'Start action block when condition is true' },
    { label: 'ELSE', type: 'keyword', info: 'Start action block when condition is false' },
    { label: 'END', type: 'keyword', info: 'End rule definition' },
    { label: 'LET', type: 'keyword', info: 'Define a variable' },
    { label: 'DO', type: 'keyword', info: 'Execute an action' },
];

// String functions
const stringFunctions = [
    { label: 'lower()', type: 'function', info: 'Convert string to lowercase', detail: 'lower(str)' },
    { label: 'upper()', type: 'function', info: 'Convert string to uppercase', detail: 'upper(str)' },
    { label: 'trim()', type: 'function', info: 'Remove whitespace from both ends', detail: 'trim(str)' },
    { label: 'len()', type: 'function', info: 'Get string length', detail: 'len(str)' },
    { label: 'str_contains()', type: 'function', info: 'Check if string contains substring', detail: 'str_contains(haystack, needle)' },
    { label: 'str_starts_with()', type: 'function', info: 'Check if string starts with prefix', detail: 'str_starts_with(str, prefix)' },
    { label: 'str_ends_with()', type: 'function', info: 'Check if string ends with suffix', detail: 'str_ends_with(str, suffix)' },
    { label: 'replace()', type: 'function', info: 'Replace substring', detail: 'replace(str, search, replace)' },
    { label: 'regex()', type: 'function', info: 'Match regular expression', detail: 'regex(str, pattern)' },
    { label: 'askOllamaAi()', type: 'function', info: 'Ask Ollama AI with a prompt and get a response', detail: 'askOllamaAi(prompt)' },
    { label: 'askOllamaAiForCreationDate()', type: 'function', info: 'Ask Ollama AI to extract creation date from document content', detail: 'askOllamaAiForCreationDate(documentContent)' },
    { label: 'askOllamaAiForDocumentNumber()', type: 'function', info: 'Ask Ollama AI to extract document number (invoice, order, contract number, etc.)', detail: 'askOllamaAiForDocumentNumber(documentContent)' },
];

// Array functions
const arrayFunctions = [
    { label: 'in()', type: 'function', info: 'Check if value is in array', detail: 'in(needle, haystack)' },
    { label: 'count()', type: 'function', info: 'Count array elements', detail: 'count(array)' },
];

// Date functions
const dateFunctions = [
    { label: 'reformatDate()', type: 'function', info: 'Reformat a date string to a different format', detail: 'reformatDate(date, format)' },
];

// Document properties
const documentProperties = [
    { label: 'document.id', type: 'property', info: 'Document ID' },
    { label: 'document.title', type: 'property', info: 'Document title' },
    { label: 'document.content', type: 'property', info: 'Document content (OCR text)' },
    { label: 'document.tags', type: 'property', info: 'Array of tag names' },
    { label: 'document.created_date', type: 'property', info: 'Creation date' },
    { label: 'document.modified', type: 'property', info: 'Modification date' },
    { label: 'document.added', type: 'property', info: 'Date added to system' },
    { label: 'document.original_file_name', type: 'property', info: 'Original filename' },
    { label: 'document.archive_serial_number', type: 'property', info: 'Archive serial number' },
    { label: 'document.document_type', type: 'property', info: 'Document type name (string)' },
    { label: 'document.correspondent', type: 'property', info: 'Correspondent name (string)' },
];

// Context variables
const contextVariables = [
    { label: 'now', type: 'variable', info: 'Current date/time' },
];

// Actions
const actions = [
    { label: 'addTag(tag: "")', type: 'function', info: 'Add a tag to the document', detail: 'addTag(tag: "tagname")' },
    { label: 'removeTag(tag: "")', type: 'function', info: 'Remove a tag from the document', detail: 'removeTag(tag: "tagname")' },
    { label: 'setDocumentType(type: "")', type: 'function', info: 'Set document type', detail: 'setDocumentType(type: "typename")' },
    { label: 'setCorrespondent(name: "")', type: 'function', info: 'Set correspondent', detail: 'setCorrespondent(name: "name")' },
    { label: 'setCustomField(field: "", value: "")', type: 'function', info: 'Set custom field', detail: 'setCustomField(field: "name", value: "value")' },
    { label: 'setTitle(title: "")', type: 'function', info: 'Set document title', detail: 'setTitle(title: "new title")' },
    { label: 'createTag(name: "")', type: 'function', info: 'Create a new tag if it does not exist', detail: 'createTag(name: "tagname")' },
    { label: 'createDocumentType(name: "")', type: 'function', info: 'Create a new document type if it does not exist', detail: 'createDocumentType(name: "typename")' },
    { label: 'createCorrespondent(name: "")', type: 'function', info: 'Create a new correspondent if it does not exist', detail: 'createCorrespondent(name: "name")' },
];

// Operators
const operators = [
    { label: 'and', type: 'keyword', info: 'Logical AND' },
    { label: 'or', type: 'keyword', info: 'Logical OR' },
    { label: 'not', type: 'keyword', info: 'Logical NOT' },
    { label: '~', type: 'operator', info: 'String concatenation' },
    { label: '+', type: 'operator', info: 'Addition' },
    { label: '-', type: 'operator', info: 'Subtraction' },
    { label: '*', type: 'operator', info: 'Multiplication' },
    { label: '/', type: 'operator', info: 'Division' },
    { label: '==', type: 'operator', info: 'Equality' },
    { label: '!=', type: 'operator', info: 'Inequality' },
    { label: '>', type: 'operator', info: 'Greater than' },
    { label: '<', type: 'operator', info: 'Less than' },
    { label: '>=', type: 'operator', info: 'Greater than or equal' },
    { label: '<=', type: 'operator', info: 'Less than or equal' },
];

// Combine all completions
const allCompletions = [
    ...keywords,
    ...stringFunctions,
    ...arrayFunctions,
    ...dateFunctions,
    ...documentProperties,
    ...contextVariables,
    ...actions,
    ...operators,
];

// Autocomplete function
function dslCompletions(context) {
    // Match word characters, dots, underscores, and operators
    let word = context.matchBefore(/[\w.+\-*/<>=!~]+/);

    // Don't show completions if we're not at a word boundary and not explicitly requested
    if (!word && !context.explicit) {
        return null;
    }

    // If we have a word, use its start position, otherwise use current position
    let from = word ? word.from : context.pos;

    return {
        from: from,
        options: allCompletions,
        validFor: /^[\w.+\-*/<>=!~]*$/
    };
}

// Export the complete language support with highlighting
export function dsl() {
    return new LanguageSupport(dslLanguage, [
        syntaxHighlighting(dslHighlightStyle),
        dslLanguage.data.of({
            autocomplete: dslCompletions
        })
    ]);
}

