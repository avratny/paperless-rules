<?php

namespace App\Services\Rules;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;

class ExpressionEngine
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage(null, [new ArrayPropertyAccessProvider()]);
        $this->registerFunctions();
    }

    public function evaluate(string $expression, array $context): mixed
    {
        try {
            // Wrap arrays in ArrayAccess objects to support dot notation
            $wrappedContext = $this->wrapArrays($context);
            return $this->expressionLanguage->evaluate($expression, $wrappedContext);
        } catch (\Exception $e) {
            throw new \Exception("Expression evaluation failed: {$e->getMessage()}");
        }
    }

    private function wrapArrays(array $context): array
    {
        $wrapped = [];
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $wrapped[$key] = new ArrayPropertyAccess($value);
            } else {
                $wrapped[$key] = $value;
            }
        }
        return $wrapped;
    }

    private function registerFunctions(): void
    {
        // String functions
        $this->expressionLanguage->addFunction(ExpressionFunction::fromPhp('strtolower', 'lower'));
        $this->expressionLanguage->addFunction(ExpressionFunction::fromPhp('strtoupper', 'upper'));
        $this->expressionLanguage->addFunction(ExpressionFunction::fromPhp('trim', 'trim'));
        $this->expressionLanguage->addFunction(ExpressionFunction::fromPhp('strlen', 'len'));

        // Custom str_contains function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'str_contains',
            fn($haystack, $needle) => sprintf('(is_string(%1$s) && str_contains(%1$s, %2$s))', $haystack, $needle),
            fn($arguments, $haystack, $needle) => is_string($haystack) && str_contains($haystack, $needle)
        ));

        // Custom startsWith function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'startsWith',
            fn($haystack, $needle) => sprintf('str_starts_with(%s, %s)', $haystack, $needle),
            fn($arguments, $haystack, $needle) => str_starts_with($haystack, $needle)
        ));

        // Custom endsWith function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'endsWith',
            fn($haystack, $needle) => sprintf('str_ends_with(%s, %s)', $haystack, $needle),
            fn($arguments, $haystack, $needle) => str_ends_with($haystack, $needle)
        ));

        // Custom replace function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'replace',
            fn($str, $search, $replace) => sprintf('str_replace(%s, %s, %s)', $search, $replace, $str),
            fn($arguments, $str, $search, $replace) => str_replace($search, $replace, $str)
        ));

        // Custom regex function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'regex',
            fn($pattern, $subject) => sprintf('(preg_match(%s, %s) === 1)', $pattern, $subject),
            fn($arguments, $pattern, $subject) => preg_match($pattern, $subject) === 1
        ));

        // Array functions
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'in',
            fn($needle, $haystack) => sprintf('in_array(%s, %s, true)', $needle, $haystack),
            fn($arguments, $needle, $haystack) => in_array($needle, $haystack, true)
        ));

        $this->expressionLanguage->addFunction(ExpressionFunction::fromPhp('count', 'count'));

        // Ollama AI function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'askOllamaAi',
            fn($prompt) => sprintf('(app(\App\Services\Ollama\OllamaService::class)->askOllamaAi(%s))', $prompt),
            fn($arguments, $prompt) => app(\App\Services\Ollama\OllamaService::class)->askOllamaAi($prompt)
        ));

        // Ollama AI function for creation date extraction
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'askOllamaAiForCreationDate',
            fn($documentContent) => sprintf('(app(\App\Services\Ollama\OllamaService::class)->askOllamaAiForCreationDate(%s))', $documentContent),
            fn($arguments, $documentContent) => app(\App\Services\Ollama\OllamaService::class)->askOllamaAiForCreationDate($documentContent)
        ));

        // Ollama AI function for document number extraction
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'askOllamaAiForDocumentNumber',
            fn($documentContent) => sprintf('(app(\App\Services\Ollama\OllamaService::class)->askOllamaAiForDocumentNumber(%s))', $documentContent),
            fn($arguments, $documentContent) => app(\App\Services\Ollama\OllamaService::class)->askOllamaAiForDocumentNumber($documentContent)
        ));

        // Date reformatting function
        $this->expressionLanguage->addFunction(new ExpressionFunction(
            'reformatDate',
            fn($date, $format) => sprintf('(app(\App\Services\Converter\DateService::class)->reformatDate(%s, %s))', $date, $format),
            fn($arguments, $date, $format) => app(\App\Services\Converter\DateService::class)->reformatDate($date, $format)
        ));
    }
}

