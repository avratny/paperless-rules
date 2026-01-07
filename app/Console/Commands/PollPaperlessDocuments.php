<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentRules;
use App\Services\Paperless\PaperlessService;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollPaperlessDocuments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'paperless:poll';

    /**
     * The console command description.
     */
    protected $description = 'Poll Paperless NGX for new or modified documents and queue them for processing';

    /**
     * Execute the console command.
     */
    public function handle(PaperlessService $paperlessService, SettingsService $settingsService): int
    {
        // Check if polling is enabled
        if (!$settingsService->isPollingEnabled()) {
            $this->info('Polling is disabled. Skipping.');
            return self::SUCCESS;
        }

        $this->info('Starting Paperless NGX polling...');
        Log::info('Paperless polling started');

        try {
            // Get last poll timestamp
            $lastPoll = $settingsService->getLastPollTimestamp();
            
            if ($lastPoll) {
                $this->info("Last poll: {$lastPoll}");
                Log::info("Polling for documents since: {$lastPoll}");
            } else {
                $this->info('First poll - fetching recent documents');
                Log::info('First poll - no previous timestamp found');
            }

            // Fetch documents since last poll
            $documents = $paperlessService->getDocumentsSince($lastPoll);

            if (empty($documents)) {
                $this->info('No new or modified documents found.');
                Log::info('No documents to process');
            } else {
                $this->info("Found {count($documents)} document(s) to process");
                Log::info("Found {count($documents)} documents to queue for processing");

                // Queue each document for processing
                $queuedCount = 0;
                foreach ($documents as $doc) {
                    $documentId = $doc['id'];
                    $eventType = $doc['event_type'];

                    ProcessDocumentRules::dispatch($documentId, $eventType);
                    $queuedCount++;

                    $this->line("  - Queued document {$documentId} ({$eventType})");
                    Log::info("Queued document {$documentId} for processing", [
                        'event_type' => $eventType,
                        'added' => $doc['added'],
                        'modified' => $doc['modified'],
                    ]);
                }

                $this->info("Successfully queued {$queuedCount} document(s)");
            }

            // Update last poll timestamp to now
            $now = now()->toIso8601String();
            $settingsService->setLastPollTimestamp($now);
            Log::info("Updated last poll timestamp to: {$now}");

            $this->info('Polling completed successfully');
            Log::info('Paperless polling completed');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Polling failed: ' . $e->getMessage());
            Log::error('Paperless polling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}

