<?php

namespace App\Services\Rules;

use App\Services\Paperless\Document;
use App\Services\SettingsService;

class RuleExecutor
{
    private ExpressionEngine $expressionEngine;
    private ActionExecutor $actionExecutor;
    private array $limits;

    public function __construct(?SettingsService $settingsService = null)
    {
        $this->expressionEngine = new ExpressionEngine();
        $this->actionExecutor = new ActionExecutor();

        $settingsService = $settingsService ?? app(SettingsService::class);
        $this->limits = $settingsService->getRuleLimits();
    }

    public function execute(array $ast, Document $document, bool $dryRun = false): array
    {
        $this->validateLimits($ast);

        $context = new RuleContext($document);
        $trace = [];

        // Track modification count before executing this rule
        $modificationCountBefore = $document->getModificationCount();

        // Check if this is an unconditional rule (no WHEN)
        if ($ast['when'] === null) {
            // Execute all statements in 'then' block unconditionally
            foreach ($ast['then'] as $statement) {
                $stepTrace = $this->executeStatement($statement, $context, $dryRun);
                $trace[] = $stepTrace;
            }
        } else {
            // Evaluate WHEN condition
            $whenResult = $this->expressionEngine->evaluate(
                $ast['when'],
                $context->getExpressionContext()
            );

            $trace[] = [
                'type' => 'when',
                'expression' => $ast['when'],
                'result' => $whenResult
            ];

            // Choose block based on condition
            $block = $whenResult ? $ast['then'] : $ast['else'];

            // Execute statements
            foreach ($block as $statement) {
                $stepTrace = $this->executeStatement($statement, $context, $dryRun);
                $trace[] = $stepTrace;
            }
        }

        // Check if THIS rule made any modifications by comparing modification counts
        $modificationCountAfter = $document->getModificationCount();
        $modifiedByThisRule = $modificationCountAfter > $modificationCountBefore;

        return [
            'success' => true,
            'trace' => $trace,
            'modified' => $document->isModified(),
            'modified_by_this_rule' => $modifiedByThisRule
        ];
    }

    private function executeStatement(array $statement, RuleContext $context, bool $dryRun): array
    {
        if ($statement['type'] === 'let') {
            return $this->executeLet($statement, $context);
        }

        if ($statement['type'] === 'do') {
            return $this->executeDo($statement, $context, $dryRun);
        }

        if ($statement['type'] === 'when') {
            return $this->executeNestedWhen($statement, $context, $dryRun);
        }

        throw new \Exception("Unknown statement type: {$statement['type']}");
    }

    private function executeNestedWhen(array $statement, RuleContext $context, bool $dryRun): array
    {
        $trace = [
            'type' => 'when',
            'expression' => $statement['when'],
            'nested' => true,
            'statements' => []
        ];

        // Evaluate nested WHEN condition
        $whenResult = $this->expressionEngine->evaluate(
            $statement['when'],
            $context->getExpressionContext()
        );

        $trace['result'] = $whenResult;

        // Choose block based on condition
        $block = $whenResult ? $statement['then'] : $statement['else'];

        // Execute statements in the chosen block
        foreach ($block as $nestedStatement) {
            $stepTrace = $this->executeStatement($nestedStatement, $context, $dryRun);
            $trace['statements'][] = $stepTrace;
        }

        return $trace;
    }

    private function executeLet(array $statement, RuleContext $context): array
    {
        $value = $this->expressionEngine->evaluate(
            $statement['expr'],
            $context->getExpressionContext()
        );

        $context->setVariable($statement['name'], $value);

        return [
            'type' => 'let',
            'name' => $statement['name'],
            'expression' => $statement['expr'],
            'value' => $value
        ];
    }

    private function executeDo(array $statement, RuleContext $context, bool $dryRun): array
    {
        // Resolve arguments
        $resolvedArgs = $this->resolveArgs($statement['args'], $context);

        // Execute action
        if ($dryRun) {
            $result = $this->actionExecutor->dryRun(
                $statement['action'],
                $resolvedArgs,
                $context
            );
        } else {
            $result = $this->actionExecutor->execute(
                $statement['action'],
                $resolvedArgs,
                $context
            );
        }

        return [
            'type' => 'do',
            'action' => $statement['action'],
            'args' => $resolvedArgs,
            'result' => $result
        ];
    }

    private function resolveArgs(array $args, RuleContext $context): array
    {
        $resolved = [];

        foreach ($args as $key => $value) {
            $resolved[$key] = $this->resolveValue($value, $context);
        }

        return $resolved;
    }

    private function resolveValue(mixed $value, RuleContext $context): mixed
    {
        // Handle reference
        if (is_array($value) && isset($value['ref'])) {
            return $context->resolveReference($value['ref']);
        }

        // Handle nested maps
        if (is_array($value)) {
            $resolved = [];
            foreach ($value as $k => $v) {
                $resolved[$k] = $this->resolveValue($v, $context);
            }
            return $resolved;
        }

        // Return primitive values as-is
        return $value;
    }

    private function validateLimits(array $ast): void
    {
        $counts = $this->countStatementsRecursive([$ast['then'], $ast['else']], 1);

        if ($counts['let'] > $this->limits['max_let_count']) {
            throw new \Exception("Too many LET statements: {$counts['let']} (max: {$this->limits['max_let_count']})");
        }

        if ($counts['do'] > $this->limits['max_do_count']) {
            throw new \Exception("Too many DO statements: {$counts['do']} (max: {$this->limits['max_do_count']})");
        }

        if ($counts['depth'] > $this->limits['max_nesting_depth']) {
            throw new \Exception("Too deep nesting: {$counts['depth']} (max: {$this->limits['max_nesting_depth']})");
        }
    }

    private function countStatementsRecursive(array $blocks, int $currentDepth): array
    {
        $letCount = 0;
        $doCount = 0;
        $maxDepth = $currentDepth;

        foreach ($blocks as $block) {
            foreach ($block as $statement) {
                if ($statement['type'] === 'let') {
                    $letCount++;
                } elseif ($statement['type'] === 'do') {
                    $doCount++;
                } elseif ($statement['type'] === 'when') {
                    // Recursively count nested WHEN statements
                    $nestedCounts = $this->countStatementsRecursive(
                        [$statement['then'], $statement['else']],
                        $currentDepth + 1
                    );
                    $letCount += $nestedCounts['let'];
                    $doCount += $nestedCounts['do'];
                    $maxDepth = max($maxDepth, $nestedCounts['depth']);
                }
            }
        }

        return [
            'let' => $letCount,
            'do' => $doCount,
            'depth' => $maxDepth
        ];
    }
}

