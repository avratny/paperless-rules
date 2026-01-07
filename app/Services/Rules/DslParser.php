<?php

namespace App\Services\Rules;

class DslParser
{
    private array $lines = [];
    private int $currentLine = 0;
    private array $errors = [];

    // Valid function names that can be used in expressions
    private const VALID_FUNCTIONS = [
        'lower', 'upper', 'trim', 'len',
        'str_contains', 'str_starts_with', 'str_ends_with',
        'replace', 'regex',
        'in', 'count',
        'askOllamaAi', 'askOllamaAiForCreationDate', 'askOllamaAiForDocumentNumber',
        'reformatDate'
    ];

    // Valid action names that can be used in DO statements
    private const VALID_ACTIONS = [
        'addTag', 'removeTag',
        'setDocumentType', 'setCorrespondent', 'setCustomField', 'setTitle',
        'createTag', 'createDocumentType', 'createCorrespondent'
    ];

    public function parse(string $dsl): array
    {
        $this->lines = $this->prepareLines($dsl);
        $this->currentLine = 0;
        $this->errors = [];

        try {
            return $this->parseRule();
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            throw new DslParseException($this->errors);
        }
    }

    private function prepareLines(string $dsl): array
    {
        $lines = explode("\n", $dsl);
        $prepared = [];

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            // Skip empty lines and comment lines (starting with //)
            if ($trimmed !== '' && !str_starts_with($trimmed, '//')) {
                $prepared[] = [
                    'content' => $trimmed,
                    'number' => $index + 1
                ];
            }
        }

        return $prepared;
    }

    private function parseRule(): array
    {
        if (!$this->hasMoreLines()) {
            throw new \Exception(__('Line :line: Empty rule', ['line' => 1]));
        }

        $firstLine = $this->currentLineContent();

        // Check if it starts with WHEN (conditional rule)
        if (preg_match('/^WHEN\s+(.+)$/i', $firstLine, $matches)) {
            return $this->parseConditionalRule($matches[1]);
        }

        // Otherwise, parse as unconditional actions (DO/LET statements only)
        return $this->parseUnconditionalRule();
    }

    private function parseConditionalRule(string $condition): array
    {
        $condition = trim($condition);

        // Validate the condition expression
        $this->validateExpression($condition);

        $ast = [
            'when' => $condition,
            'then' => [],
            'else' => []
        ];

        $this->advance();

        // Check for optional THEN keyword
        if ($this->hasMoreLines() && preg_match('/^THEN$/i', $this->currentLineContent())) {
            $this->advance();
        }

        // Parse THEN block
        $ast['then'] = $this->parseBlock();

        // Check for ELSE
        if ($this->hasMoreLines() && preg_match('/^ELSE$/i', $this->currentLineContent())) {
            $this->advance();
            $ast['else'] = $this->parseBlock();
        }

        // Expect END
        if (!$this->hasMoreLines() || !preg_match('/^END$/i', $this->currentLineContent())) {
            throw new \Exception(__('Line :line: Expected END statement', ['line' => $this->currentLineNumber()]));
        }

        return $ast;
    }

    private function parseUnconditionalRule(): array
    {
        $ast = [
            'when' => null,
            'then' => [],
            'else' => []
        ];

        // Parse all statements as unconditional actions
        while ($this->hasMoreLines()) {
            $line = $this->currentLineContent();

            // Parse nested WHEN (conditional block within unconditional rule)
            if (preg_match('/^WHEN\s+(.+)$/i', $line, $matches)) {
                $ast['then'][] = $this->parseNestedWhen($matches[1]);
                continue;
            }

            // Parse LET
            if (preg_match('/^LET\s+([A-Za-z_]\w*)\s*=\s*(.+)$/i', $line, $matches)) {
                $expr = trim($matches[2]);

                // Validate the expression
                $this->validateExpression($expr);

                $ast['then'][] = [
                    'type' => 'let',
                    'name' => $matches[1],
                    'expr' => $expr
                ];
                $this->advance();
                continue;
            }

            // Parse DO
            if (preg_match('/^DO\s+([A-Za-z_]\w*)\s*\((.*)\)$/i', $line, $matches)) {
                $actionName = $matches[1];

                // Validate action name
                if (!in_array($actionName, self::VALID_ACTIONS)) {
                    throw new \Exception(__('Line :line: Unknown action ":action". Valid actions are: :valid', [
                        'line' => $this->currentLineNumber(),
                        'action' => $actionName,
                        'valid' => implode(', ', self::VALID_ACTIONS)
                    ]));
                }

                $ast['then'][] = [
                    'type' => 'do',
                    'action' => $actionName,
                    'args' => $this->parseArgs(trim($matches[2]))
                ];
                $this->advance();
                continue;
            }

            throw new \Exception(__('Line :line: Invalid statement. Expected DO, LET, or WHEN', ['line' => $this->currentLineNumber()]));
        }

        if (empty($ast['then'])) {
            throw new \Exception(__('Line :line: Rule must contain at least one action', ['line' => 1]));
        }

        return $ast;
    }

    private function parseBlock(): array
    {
        $statements = [];

        while ($this->hasMoreLines()) {
            $line = $this->currentLineContent();

            // Check for block terminators
            if (preg_match('/^(ELSE|END)$/i', $line)) {
                break;
            }

            // Parse nested WHEN (recursive)
            if (preg_match('/^WHEN\s+(.+)$/i', $line, $matches)) {
                $statements[] = $this->parseNestedWhen($matches[1]);
                continue;
            }

            // Parse LET
            if (preg_match('/^LET\s+([A-Za-z_]\w*)\s*=\s*(.+)$/i', $line, $matches)) {
                $expr = trim($matches[2]);

                // Validate the expression
                $this->validateExpression($expr);

                $statements[] = [
                    'type' => 'let',
                    'name' => $matches[1],
                    'expr' => $expr
                ];
                $this->advance();
                continue;
            }

            // Parse DO
            if (preg_match('/^DO\s+([A-Za-z_]\w*)\s*\((.*)\)$/i', $line, $matches)) {
                $actionName = $matches[1];

                // Validate action name
                if (!in_array($actionName, self::VALID_ACTIONS)) {
                    throw new \Exception(__('Line :line: Unknown action ":action". Valid actions are: :valid', [
                        'line' => $this->currentLineNumber(),
                        'action' => $actionName,
                        'valid' => implode(', ', self::VALID_ACTIONS)
                    ]));
                }

                $statements[] = [
                    'type' => 'do',
                    'action' => $actionName,
                    'args' => $this->parseArgs(trim($matches[2]))
                ];
                $this->advance();
                continue;
            }

            throw new \Exception(__('Line :line: Invalid statement', ['line' => $this->currentLineNumber()]));
        }

        return $statements;
    }

    private function parseNestedWhen(string $condition): array
    {
        $condition = trim($condition);

        // Validate the condition expression
        $this->validateExpression($condition);

        $nested = [
            'type' => 'when',
            'when' => $condition,
            'then' => [],
            'else' => []
        ];

        $this->advance();

        // Check for optional THEN keyword
        if ($this->hasMoreLines() && preg_match('/^THEN$/i', $this->currentLineContent())) {
            $this->advance();
        }

        // Parse THEN block
        $nested['then'] = $this->parseBlock();

        // Check for ELSE
        if ($this->hasMoreLines() && preg_match('/^ELSE$/i', $this->currentLineContent())) {
            $this->advance();
            $nested['else'] = $this->parseBlock();
        }

        // Expect END for nested WHEN
        if (!$this->hasMoreLines() || !preg_match('/^END$/i', $this->currentLineContent())) {
            throw new \Exception(__('Line :line: Expected END for nested WHEN statement', ['line' => $this->currentLineNumber()]));
        }

        $this->advance(); // consume END

        return $nested;
    }

    private function parseArgs(string $argsStr): array
    {
        if (empty($argsStr)) {
            return [];
        }

        $args = [];
        $pairs = $this->splitTopLevel($argsStr, ',');

        foreach ($pairs as $pair) {
            $parts = $this->splitTopLevel($pair, ':', 2);
            if (count($parts) !== 2) {
                throw new \Exception(__('Line :line: Invalid argument format', ['line' => $this->currentLineNumber()]));
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $args[$key] = $this->parseValue($value);
        }

        return $args;
    }

    private function parseValue(string $value): mixed
    {
        $value = trim($value);

        // String
        if (preg_match('/^"(.*)"$/s', $value, $matches)) {
            return $matches[1];
        }

        // Number (int or float)
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        // Boolean
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        // Null
        if (strtolower($value) === 'null') {
            return null;
        }

        // Map
        if (preg_match('/^\{(.+)\}$/s', $value, $matches)) {
            return $this->parseMap(trim($matches[1]));
        }

        // Variable reference (identifier or dotted path)
        if (preg_match('/^[A-Za-z_]\w*(\.[A-Za-z_]\w*)*$/', $value)) {
            return ['ref' => $value];
        }

        throw new \Exception(__('Line :line: Invalid value format: :value', ['line' => $this->currentLineNumber(), 'value' => $value]));
    }

    private function parseMap(string $mapStr): array
    {
        $map = [];
        $pairs = $this->splitTopLevel($mapStr, ',');

        foreach ($pairs as $pair) {
            $parts = $this->splitTopLevel($pair, ':', 2);
            if (count($parts) !== 2) {
                throw new \Exception(__('Line :line: Invalid map format', ['line' => $this->currentLineNumber()]));
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $map[$key] = $this->parseValue($value);
        }

        return $map;
    }

    private function splitTopLevel(string $str, string $delimiter, int $limit = PHP_INT_MAX): array
    {
        $result = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $len = strlen($str);
        $parts = 0;

        for ($i = 0; $i < $len; $i++) {
            $char = $str[$i];

            if ($char === '"' && ($i === 0 || $str[$i - 1] !== '\\')) {
                $inString = !$inString;
                $current .= $char;
                continue;
            }

            if (!$inString) {
                if ($char === '{') {
                    $depth++;
                } elseif ($char === '}') {
                    $depth--;
                }

                if ($depth === 0 && $char === $delimiter && $parts < $limit - 1) {
                    $result[] = $current;
                    $current = '';
                    $parts++;
                    continue;
                }
            }

            $current .= $char;
        }

        if ($current !== '') {
            $result[] = $current;
        }

        return $result;
    }

    private function hasMoreLines(): bool
    {
        return $this->currentLine < count($this->lines);
    }

    private function currentLineContent(): string
    {
        return $this->lines[$this->currentLine]['content'] ?? '';
    }

    private function currentLineNumber(): int
    {
        return $this->lines[$this->currentLine]['number'] ?? 0;
    }

    private function advance(): void
    {
        $this->currentLine++;
    }

    /**
     * Validate an expression for unknown function calls
     */
    private function validateExpression(string $expression): void
    {
        // Find all function calls in the expression
        // Pattern matches: functionName(
        preg_match_all('/([a-zA-Z_]\w*)\s*\(/', $expression, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $functionName) {
                // Check if it's a valid function
                if (!in_array($functionName, self::VALID_FUNCTIONS)) {
                    throw new \Exception(__('Line :line: Unknown function ":function" in expression. Valid functions are: :valid', [
                        'line' => $this->currentLineNumber(),
                        'function' => $functionName,
                        'valid' => implode(', ', self::VALID_FUNCTIONS)
                    ]));
                }
            }
        }
    }
}
