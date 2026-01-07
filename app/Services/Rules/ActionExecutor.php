<?php

namespace App\Services\Rules;

class ActionExecutor
{
    private array $actions = [];

    public function __construct()
    {
        $this->registerActions();
    }

    public function execute(string $actionName, array $args, RuleContext $context): array
    {
        if (!isset($this->actions[$actionName])) {
            throw new \Exception("Unknown action: {$actionName}");
        }

        $action = $this->actions[$actionName];

        // Validate required parameters
        $this->validateArgs($actionName, $args, $action['params']);

        // Execute the action
        return $action['execute']($args, $context);
    }

    public function dryRun(string $actionName, array $args, RuleContext $context): array
    {
        if (!isset($this->actions[$actionName])) {
            throw new \Exception("Unknown action: {$actionName}");
        }

        $action = $this->actions[$actionName];

        // Validate required parameters
        $this->validateArgs($actionName, $args, $action['params']);

        // Return what would be done
        return [
            'action' => $actionName,
            'args' => $args,
            'description' => $action['description']($args)
        ];
    }

    private function validateArgs(string $actionName, array $args, array $requiredParams): void
    {
        foreach ($requiredParams as $param) {
            if (!isset($args[$param])) {
                throw new \Exception("Action '{$actionName}' requires parameter: {$param}");
            }
        }
    }

    private function registerActions(): void
    {
        // Add Tag
        $this->actions['addTag'] = [
            'params' => ['tag'],
            'description' => fn($args) => "Add tag: {$args['tag']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $createIfNotExists = $args['create'] ?? true;
                $document->addTagByName($args['tag'], $createIfNotExists);

                return [
                    'success' => true,
                    'message' => "Tag '{$args['tag']}' added"
                ];
            }
        ];

        // Remove Tag
        $this->actions['removeTag'] = [
            'params' => ['tag'],
            'description' => fn($args) => "Remove tag: {$args['tag']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $document->removeTagByName($args['tag']);

                return [
                    'success' => true,
                    'message' => "Tag '{$args['tag']}' removed"
                ];
            }
        ];

        // Set Document Type
        $this->actions['setDocumentType'] = [
            'params' => ['type'],
            'description' => fn($args) => "Set document type: {$args['type']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $createIfNotExists = $args['create'] ?? true;
                $document->setDocumentType($args['type'], $createIfNotExists);

                return [
                    'success' => true,
                    'message' => "Document type set to '{$args['type']}'"
                ];
            }
        ];

        // Set Correspondent
        $this->actions['setCorrespondent'] = [
            'params' => ['name'],
            'description' => fn($args) => "Set correspondent: {$args['name']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $createIfNotExists = $args['create'] ?? true;
                $document->setCorrespondent($args['name'], $createIfNotExists);

                return [
                    'success' => true,
                    'message' => "Correspondent set to '{$args['name']}'"
                ];
            }
        ];

        // Set Custom Field
        $this->actions['setCustomField'] = [
            'params' => ['field', 'value'],
            'description' => fn($args) => "Set custom field '{$args['field']}' to '{$args['value']}'",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $document->setCustomField($args['field'], $args['value']);

                return [
                    'success' => true,
                    'message' => "Custom field '{$args['field']}' set to '{$args['value']}'"
                ];
            }
        ];

        // Set Title
        $this->actions['setTitle'] = [
            'params' => ['title'],
            'description' => fn($args) => "Set title: {$args['title']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $document->setTitle($args['title']);

                return [
                    'success' => true,
                    'message' => "Title set to '{$args['title']}'"
                ];
            }
        ];

        // Create Tag
        $this->actions['createTag'] = [
            'params' => ['name'],
            'description' => fn($args) => "Create tag: {$args['name']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $service = $document->getService();

                // Check if tag already exists
                $existingTag = $service->findTagByName($args['name']);
                if ($existingTag) {
                    return [
                        'success' => true,
                        'message' => "Tag '{$args['name']}' already exists (ID: {$existingTag['id']})",
                        'already_exists' => true,
                        'tag_id' => $existingTag['id']
                    ];
                }

                // Create new tag
                $tag = $service->createTag($args['name']);

                return [
                    'success' => true,
                    'message' => "Tag '{$args['name']}' created (ID: {$tag['id']})",
                    'created' => true,
                    'tag_id' => $tag['id']
                ];
            }
        ];

        // Create Document Type
        $this->actions['createDocumentType'] = [
            'params' => ['name'],
            'description' => fn($args) => "Create document type: {$args['name']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $service = $document->getService();

                // Check if document type already exists
                $existingType = $service->findDocumentTypeByName($args['name']);
                if ($existingType) {
                    return [
                        'success' => true,
                        'message' => "Document type '{$args['name']}' already exists (ID: {$existingType['id']})",
                        'already_exists' => true,
                        'type_id' => $existingType['id']
                    ];
                }

                // Create new document type
                $type = $service->createDocumentType($args['name']);

                return [
                    'success' => true,
                    'message' => "Document type '{$args['name']}' created (ID: {$type['id']})",
                    'created' => true,
                    'type_id' => $type['id']
                ];
            }
        ];

        // Create Correspondent
        $this->actions['createCorrespondent'] = [
            'params' => ['name'],
            'description' => fn($args) => "Create correspondent: {$args['name']}",
            'execute' => function ($args, RuleContext $context) {
                $document = $context->getDocument();
                $service = $document->getService();

                // Check if correspondent already exists
                $existingCorrespondent = $service->findCorrespondentByName($args['name']);
                if ($existingCorrespondent) {
                    return [
                        'success' => true,
                        'message' => "Correspondent '{$args['name']}' already exists (ID: {$existingCorrespondent['id']})",
                        'already_exists' => true,
                        'correspondent_id' => $existingCorrespondent['id']
                    ];
                }

                // Create new correspondent
                $correspondent = $service->createCorrespondent($args['name']);

                return [
                    'success' => true,
                    'message' => "Correspondent '{$args['name']}' created (ID: {$correspondent['id']})",
                    'created' => true,
                    'correspondent_id' => $correspondent['id']
                ];
            }
        ];

        // Ask Ollama AI
        $this->actions['askOllamaAi'] = [
            'params' => ['prompt'],
            'description' => fn($args) => "Ask Ollama AI: {$args['prompt']}",
            'execute' => function ($args, RuleContext $context) {
                $ollamaService = app(\App\Services\Ollama\OllamaService::class);

                $response = $ollamaService->askOllamaAi($args['prompt']);

                return [
                    'success' => true,
                    'message' => "Ollama AI response: " . ($response ?: '(empty)'),
                    'response' => $response
                ];
            }
        ];

        // Ask Ollama AI for creation date
        $this->actions['askOllamaAiForCreationDate'] = [
            'params' => ['documentContent'],
            'description' => fn($args) => "Ask Ollama AI for creation date from document content",
            'execute' => function ($args, RuleContext $context) {
                $ollamaService = app(\App\Services\Ollama\OllamaService::class);

                $date = $ollamaService->askOllamaAiForCreationDate($args['documentContent']);

                return [
                    'success' => true,
                    'message' => "Ollama AI extracted date: " . ($date ?: '(no valid date found)'),
                    'date' => $date
                ];
            }
        ];

        // Ask Ollama AI for document number
        $this->actions['askOllamaAiForDocumentNumber'] = [
            'params' => ['documentContent'],
            'description' => fn($args) => "Ask Ollama AI for document number from document content",
            'execute' => function ($args, RuleContext $context) {
                $ollamaService = app(\App\Services\Ollama\OllamaService::class);

                $number = $ollamaService->askOllamaAiForDocumentNumber($args['documentContent']);

                return [
                    'success' => true,
                    'message' => "Ollama AI extracted document number: " . ($number ?: '(no number found)'),
                    'number' => $number
                ];
            }
        ];
    }
}

