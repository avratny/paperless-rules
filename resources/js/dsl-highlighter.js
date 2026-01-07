/**
 * DSL Syntax Highlighter for Documentation
 * Matches the colors from the Rule Editor (CodeMirror)
 */

export function highlightDslCode() {
    // Find all code blocks with language-dsl class
    document.querySelectorAll('pre code.language-dsl').forEach((block) => {
        // Skip if already highlighted and content hasn't changed
        if (block.classList.contains('highlighted') && block.dataset.originalCode === block.textContent) {
            return;
        }

        const code = block.textContent;
        block.dataset.originalCode = code;
        const highlighted = highlightDsl(code);
        block.innerHTML = highlighted;
        block.classList.add('highlighted');
    });
}

function highlightDsl(code) {
    // Split code into tokens to avoid overlapping replacements
    const tokens = tokenizeDsl(code);
    return tokens.map(token => {
        const escaped = escapeHtml(token.value);

        switch (token.type) {
            case 'keyword':
                return `<span class="keyword">${escaped}</span>`;
            case 'action':
                return `<span class="action">${escaped}</span>`;
            case 'function':
                return `<span class="function">${escaped}</span>`;
            case 'property':
                return `<span class="property">${escaped}</span>`;
            case 'boolean':
                return `<span class="boolean">${escaped}</span>`;
            case 'null':
                return `<span class="null">${escaped}</span>`;
            case 'number':
                return `<span class="number">${escaped}</span>`;
            case 'string':
                return `<span class="string">${escaped}</span>`;
            case 'comment':
                return `<span class="comment">${escaped}</span>`;
            case 'operator':
                return `<span class="operator">${escaped}</span>`;
            default:
                return escaped;
        }
    }).join('');
}

function tokenizeDsl(code) {
    const tokens = [];
    let i = 0;

    while (i < code.length) {
        let matched = false;

        // Skip whitespace but preserve it
        if (/\s/.test(code[i])) {
            let ws = '';
            while (i < code.length && /\s/.test(code[i])) {
                ws += code[i];
                i++;
            }
            tokens.push({ type: 'whitespace', value: ws });
            continue;
        }

        // Comments
        if (code.substr(i, 2) === '//') {
            let comment = '';
            while (i < code.length && code[i] !== '\n') {
                comment += code[i];
                i++;
            }
            tokens.push({ type: 'comment', value: comment });
            continue;
        }

        // Strings
        if (code[i] === '"') {
            let str = '"';
            i++;
            while (i < code.length && code[i] !== '"') {
                if (code[i] === '\\' && i + 1 < code.length) {
                    str += code[i] + code[i + 1];
                    i += 2;
                } else {
                    str += code[i];
                    i++;
                }
            }
            if (i < code.length) {
                str += '"';
                i++;
            }
            tokens.push({ type: 'string', value: str });
            continue;
        }

        // Numbers
        const numberMatch = code.substr(i).match(/^-?\d+\.?\d*/);
        if (numberMatch && numberMatch[0].length > 0 && /\d/.test(numberMatch[0])) {
            tokens.push({ type: 'number', value: numberMatch[0] });
            i += numberMatch[0].length;
            continue;
        }

        // Keywords
        const keywordMatch = code.substr(i).match(/^(WHEN|THEN|ELSE|END|LET|DO|and|or|not)\b/);
        if (keywordMatch) {
            tokens.push({ type: 'keyword', value: keywordMatch[0] });
            i += keywordMatch[0].length;
            continue;
        }

        // Booleans
        const boolMatch = code.substr(i).match(/^(true|false)\b/);
        if (boolMatch) {
            tokens.push({ type: 'boolean', value: boolMatch[0] });
            i += boolMatch[0].length;
            continue;
        }

        // Null
        const nullMatch = code.substr(i).match(/^null\b/);
        if (nullMatch) {
            tokens.push({ type: 'null', value: nullMatch[0] });
            i += nullMatch[0].length;
            continue;
        }

        // Actions
        const actionMatch = code.substr(i).match(/^(addTag|removeTag|setDocumentType|setCorrespondent|setCustomField|setTitle|createTag|createDocumentType|createCorrespondent)\b/);
        if (actionMatch) {
            tokens.push({ type: 'action', value: actionMatch[0] });
            i += actionMatch[0].length;
            continue;
        }

        // Functions
        const functionMatch = code.substr(i).match(/^(lower|upper|trim|len|str_contains|str_starts_with|str_ends_with|replace|regex|in|count|askOllamaAi|askOllamaAiForCreationDate|askOllamaAiForDocumentNumber|reformatDate)\b/);
        if (functionMatch) {
            tokens.push({ type: 'function', value: functionMatch[0] });
            i += functionMatch[0].length;
            continue;
        }

        // Document properties
        const propMatch = code.substr(i).match(/^document\.(id|title|content|tags|created_date|modified|added|original_file_name|archive_serial_number|document_type|correspondent)\b/);
        if (propMatch) {
            tokens.push({ type: 'property', value: propMatch[0] });
            i += propMatch[0].length;
            continue;
        }

        // Operators
        const opMatch = code.substr(i).match(/^[+\-*/<>=!~]+/);
        if (opMatch) {
            tokens.push({ type: 'operator', value: opMatch[0] });
            i += opMatch[0].length;
            continue;
        }

        // Default: single character
        tokens.push({ type: 'text', value: code[i] });
        i++;
    }

    return tokens;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    // Escape HTML but preserve whitespace characters as-is
    // The CSS white-space: pre will handle the rendering
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Auto-highlight on page load
if (typeof window !== 'undefined') {
    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', highlightDslCode);
    } else {
        highlightDslCode();
    }

    // Also expose globally for Livewire updates
    window.highlightDslCode = highlightDslCode;
}

