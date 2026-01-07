<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Paperless\PaperlessService;
use App\Services\Rules\RuleService;

class TestCreateActions extends Command
{
    protected $signature = 'test:create-actions {documentId}';
    protected $description = 'Test createTag, createDocumentType, and createCorrespondent actions';

    public function handle()
    {
        $documentId = (int) $this->argument('documentId');
        
        $this->info("Testing create actions with document ID: {$documentId}");
        $this->newLine();

        try {
            $paperlessService = new PaperlessService();
            $ruleService = new RuleService();

            // Load document
            $this->info("Loading document...");
            $document = $paperlessService->loadDocument($documentId);
            $this->info("✓ Document loaded: {$document->title}");
            $this->newLine();

            // Test 1: Create Tag
            $this->info("Test 1: Creating tag 'Test'");
            $dslTag = <<<'DSL'
DO createTag(name: "Test")
DSL;
            
            $result = $ruleService->testRule($dslTag, $document);
            if ($result['success']) {
                $this->info("✓ Dry run successful");
                $trace = $result['result']['trace'][0] ?? null;
                if ($trace) {
                    $message = $trace['result']['result']['message'] ?? 'No message';
                    $this->line("  Message: {$message}");
                }
            } else {
                $this->error("✗ Dry run failed");
                $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
            }
            $this->newLine();

            // Test 2: Create Document Type
            $this->info("Test 2: Creating document type 'Test'");
            $dslType = <<<'DSL'
DO createDocumentType(name: "Test")
DSL;
            
            $result = $ruleService->testRule($dslType, $document);
            if ($result['success']) {
                $this->info("✓ Dry run successful");
                $trace = $result['result']['trace'][0] ?? null;
                if ($trace) {
                    $message = $trace['result']['result']['message'] ?? 'No message';
                    $this->line("  Message: {$message}");
                }
            } else {
                $this->error("✗ Dry run failed");
                $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
            }
            $this->newLine();

            // Test 3: Create Correspondent
            $this->info("Test 3: Creating correspondent 'Test'");
            $dslCorrespondent = <<<'DSL'
DO createCorrespondent(name: "Test")
DSL;
            
            $result = $ruleService->testRule($dslCorrespondent, $document);
            if ($result['success']) {
                $this->info("✓ Dry run successful");
                $trace = $result['result']['trace'][0] ?? null;
                if ($trace) {
                    $message = $trace['result']['result']['message'] ?? 'No message';
                    $this->line("  Message: {$message}");
                }
            } else {
                $this->error("✗ Dry run failed");
                $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
            }
            $this->newLine();

            // Execute all actions
            if ($this->confirm('Do you want to execute these actions in the live system?', false)) {
                $this->info("Executing actions...");
                
                // Execute Tag
                $this->info("Creating tag...");
                $ruleService->executeRule($dslTag, $document, false);
                
                // Execute Document Type
                $this->info("Creating document type...");
                $ruleService->executeRule($dslType, $document, false);
                
                // Execute Correspondent
                $this->info("Creating correspondent...");
                $ruleService->executeRule($dslCorrespondent, $document, false);
                
                $this->newLine();
                $this->info("✓ All actions executed successfully!");
                $this->info("Check your Paperless instance for the new tag, document type, and correspondent named 'Test'");
            } else {
                $this->info("Execution cancelled.");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}

