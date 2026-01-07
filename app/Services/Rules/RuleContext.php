<?php

namespace App\Services\Rules;

use App\Services\Paperless\Document;
use Carbon\Carbon;

class RuleContext
{
    private array $variables = [];
    private array $contextData = [];

    public function __construct(Document $document)
    {
        // Convert document to array for expression evaluation
        $this->contextData['document'] = $this->documentToArray($document);
        $this->contextData['now'] = Carbon::now();

        // Store original document object for actions
        $this->contextData['_documentObject'] = $document;
    }

    public function setVariable(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    public function getVariable(string $name): mixed
    {
        return $this->variables[$name] ?? null;
    }

    public function hasVariable(string $name): bool
    {
        return isset($this->variables[$name]);
    }

    public function getExpressionContext(): array
    {
        // Merge context data with variables for expression evaluation
        return array_merge($this->contextData, $this->variables);
    }

    public function resolveReference(string $ref): mixed
    {
        // Check if it's a variable reference (no dot)
        if (!str_contains($ref, '.')) {
            if ($this->hasVariable($ref)) {
                return $this->getVariable($ref);
            }
        }

        // Otherwise resolve from context data
        return $this->resolveNestedPath($ref);
    }

    public function getDocument(): Document
    {
        return $this->contextData['_documentObject'];
    }

    private function resolveNestedPath(string $path): mixed
    {
        $parts = explode('.', $path);
        $current = $this->getExpressionContext();

        foreach ($parts as $part) {
            if (is_array($current) && isset($current[$part])) {
                $current = $current[$part];
            } elseif (is_object($current) && isset($current->$part)) {
                $current = $current->$part;
            } else {
                return null;
            }
        }

        return $current;
    }

    private function documentToArray(Document $document): array
    {
        return [
            'id' => $document->id,
            'title' => $document->title,
            'content' => $document->content,
            'tags' => $document->getTags(),
            'created_date' => $document->created_date,
            'modified' => $document->modified,
            'added' => $document->added,
            'original_file_name' => $document->original_file_name,
            'archive_serial_number' => $document->archive_serial_number,
            'document_type' => $document->getDocumentType(),
            'correspondent' => $document->getCorrespondent(),
        ];
    }
}

