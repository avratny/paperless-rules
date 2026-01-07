<?php

namespace App\Console\Commands;

use App\Models\LockedDocument;
use App\Services\SettingsService;
use Illuminate\Console\Command;

class CleanupLockedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cleanup-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old document locks';

    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settingsService): int
    {
        $lockDuration = $settingsService->getDocumentLockDuration();

        $this->info("Cleaning up old document locks (older than {$lockDuration} seconds)...");

        $deletedCount = LockedDocument::cleanup();

        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} old document lock(s).");
        } else {
            $this->info('No old locks to clean up.');
        }

        return Command::SUCCESS;
    }
}
