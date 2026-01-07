<?php

namespace App\Services\Rules;

use App\Services\Paperless\Document;
use App\Services\SettingsService;

class RuleService
{
    private DslParser $parser;
    private RuleExecutor $executor;
    private SettingsService $settingsService;

    public function __construct(?SettingsService $settingsService = null)
    {
        $this->parser = new DslParser();
        $this->executor = new RuleExecutor($settingsService);
        $this->settingsService = $settingsService ?? app(SettingsService::class);
    }

    /**
     * Parse DSL text into AST
     */
    public function parse(string $dsl): array
    {
        // Check DSL length limit
        $maxLength = $this->settingsService->getMaxDslLength();
        if (strlen($dsl) > $maxLength) {
            throw new \Exception("DSL too long: " . strlen($dsl) . " characters (max: {$maxLength})");
        }

        return $this->parser->parse($dsl);
    }

    /**
     * Execute a rule (from DSL text) on a document
     */
    public function executeRule(string $dsl, Document $document, bool $dryRun = false): array
    {
        $ast = $this->parse($dsl);
        return $this->executor->execute($ast, $document, $dryRun);
    }

    /**
     * Execute a rule (from AST) on a document
     */
    public function executeAst(array $ast, Document $document, bool $dryRun = false): array
    {
        return $this->executor->execute($ast, $document, $dryRun);
    }

    /**
     * Test a rule without executing it (dry run)
     */
    public function testRule(string $dsl, Document $document): array
    {
        try {
            $result = $this->executeRule($dsl, $document, true);
            return [
                'success' => true,
                'result' => $result
            ];
        } catch (DslParseException $e) {
            return [
                'success' => false,
                'errors' => $e->getErrors()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }
}

