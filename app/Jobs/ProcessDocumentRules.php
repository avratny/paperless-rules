<?php

namespace App\Jobs;

use App\Models\LockedDocument;
use App\Models\Rule;
use App\Models\RuleExecutionLog;
use App\Services\Paperless\PaperlessService;
use App\Services\Rules\RuleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessDocumentRules implements ShouldQueue
{
    use Queueable;

    /**
     * The document ID to process
     */
    public int $documentId;

    /**
     * The event type (on_create or on_change)
     */
    public string $eventType;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(int $documentId, string $eventType)
    {
        $this->documentId = $documentId;
        $this->eventType = $eventType;

        // Ensure only one job per document is processed at a time
        // by using the document ID as the queue name
        $this->onQueue('document-processing');
    }

    /**
     * Execute the job.
     */
    public function handle(PaperlessService $paperlessService, RuleService $ruleService): void
    {
        Log::info("Processing document {$this->documentId} for event type: {$this->eventType}");

        try {
            // Check if document is locked (processed recently)
            if (LockedDocument::isLocked($this->documentId)) {
                Log::warning("Document {$this->documentId} is locked (processed recently). Skipping to prevent infinite loop.");
                return;
            }

            // Lock the document
            LockedDocument::lock($this->documentId);
            Log::info("Document {$this->documentId} locked for processing");

            // Load document from Paperless
            $document = $paperlessService->loadDocument($this->documentId);

            // Get all enabled rules matching the event type
            // Sort by order (ascending), then by created_at (ascending) for same order
            $rules = Rule::where('enabled', true)
                ->where($this->eventType, true)
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($rules->isEmpty()) {
                Log::info("No enabled rules found for event type: {$this->eventType}");
                return;
            }

            Log::info("Found {$rules->count()} rules to execute for document {$this->documentId}");

            // Generate unique job ID for grouping all logs from this execution
            $jobId = Str::uuid()->toString();

            // Execute all matching rules one by one
            $successCount = 0;
            $errorCount = 0;

            foreach ($rules as $rule) {
                $executedAt = now();
                $logData = [
                    'job_id' => $jobId,
                    'document_id' => $this->documentId,
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'rule_order' => $rule->order ?? 0,
                    'event_type' => $this->eventType,
                    'executed_at' => $executedAt,
                ];

                try {
                    Log::info("Executing rule '{$rule->name}' (ID: {$rule->id}) for document {$this->documentId}");

                    // Execute the rule
                    $result = $ruleService->executeRule($rule->rule, $document, false);

                    $successCount++;

                    // Log successful execution
                    RuleExecutionLog::create(array_merge($logData, [
                        'status' => 'success',
                        'trace' => $result['trace'] ?? [],
                        'document_modified' => $result['modified_by_this_rule'] ?? false,
                    ]));

                    Log::info("Rule '{$rule->name}' executed successfully", [
                        'modified_by_this_rule' => $result['modified_by_this_rule'] ?? false,
                        'document_modified_overall' => $result['modified'] ?? false,
                        'trace_count' => count($result['trace'] ?? [])
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;

                    // Log failed execution
                    RuleExecutionLog::create(array_merge($logData, [
                        'status' => 'error',
                        'error_message' => $e->getMessage(),
                        'document_modified' => false,
                    ]));

                    Log::error("Error executing rule '{$rule->name}' (ID: {$rule->id}): " . $e->getMessage(), [
                        'document_id' => $this->documentId,
                        'rule_id' => $rule->id,
                        'exception' => $e
                    ]);

                    // Continue with next rule even if one fails
                }
            }

            // Save document if modified
            if ($document->isModified()) {
                Log::info("Saving modified document {$this->documentId}");
                $document->save();
            }

            Log::info("Finished processing document {$this->documentId}", [
                'total_rules' => $rules->count(),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'document_modified' => $document->isModified()
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process document {$this->documentId}: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed permanently for document {$this->documentId}", [
            'event_type' => $this->eventType,
            'exception' => $exception
        ]);
    }
}
