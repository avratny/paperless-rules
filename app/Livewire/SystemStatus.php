<?php

namespace App\Livewire;

use App\Models\LockedDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;
use Livewire\Component;

class SystemStatus extends Component
{
    public bool $queueRunning = false;
    public bool $schedulerRunning = false;
    public int $pendingJobs = 0;
    public int $lockedDocuments = 0;
    public bool $paperlessConnected = false;
    public ?string $paperlessError = null;

    public function mount(): void
    {
        $this->checkStatus();
    }

    #[On('check-system-status')]
    public function checkStatus(): void
    {
        $this->queueRunning = $this->isQueueRunning();
        $this->schedulerRunning = $this->isSchedulerRunning();
        $this->pendingJobs = $this->getPendingJobsCount();
        $this->checkPaperlessConnection();
        $this->lockedDocuments = $this->getLockedDocumentsCount();
    }

    /**
     * Check if queue worker is running by looking at heartbeat
     */
    private function isQueueRunning(): bool
    {
        // Check for queue worker heartbeat (set by queue:work command via event listener)
        $heartbeat = Cache::get('queue:worker:heartbeat');

        if ($heartbeat && Carbon::parse($heartbeat)->isAfter(Carbon::now()->subSeconds(30))) {
            return true;
        }

        return false;
    }

    /**
     * Check if scheduler is running by looking at heartbeat
     */
    private function isSchedulerRunning(): bool
    {
        // Check for scheduler heartbeat (set when scheduled tasks start)
        $heartbeat = Cache::get('scheduler:heartbeat');

        if ($heartbeat && Carbon::parse($heartbeat)->isAfter(Carbon::now()->subMinutes(2))) {
            return true;
        }

        return false;
    }

    /**
     * Get count of pending jobs in queue
     */
    private function getPendingJobsCount(): int
    {
        try {
            return DB::table('jobs')
                ->where('queue', 'document-processing')
                ->whereNull('reserved_at')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get count of currently locked documents
     */
    private function getLockedDocumentsCount(): int
    {
        try {
            return LockedDocument::where('locked_at', '>', Carbon::now()->subSeconds(60))->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check Paperless connection and API credentials
     */
    private function checkPaperlessConnection(): void
    {
        try {
            $settingsService = app(\App\Services\SettingsService::class);
            $url = rtrim($settingsService->getPaperlessUrl(), '/');
            $apiKey = $settingsService->getPaperlessApiKey();

            if (empty($url) || empty($apiKey)) {
                $this->paperlessConnected = false;
                $this->paperlessError = 'Configuration missing';
                return;
            }

            // Try to connect to Paperless API with a simple request
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Accept' => 'application/json; version=9',
                ])
                ->get($url . '/api/documents/', [
                    'page' => 1,
                    'page_size' => 1,
                ]);

            if ($response->successful()) {
                $this->paperlessConnected = true;
                $this->paperlessError = null;
            } elseif ($response->status() === 401 || $response->status() === 403) {
                $this->paperlessConnected = false;
                $this->paperlessError = 'Invalid API key';
            } else {
                $this->paperlessConnected = false;
                $this->paperlessError = 'HTTP ' . $response->status();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->paperlessConnected = false;
            $this->paperlessError = 'Connection failed';
        } catch (\Exception $e) {
            $this->paperlessConnected = false;
            $this->paperlessError = 'Error: ' . substr($e->getMessage(), 0, 30);
        }
    }

    /**
     * Get overall system status
     */
    public function getOverallStatusProperty(): string
    {
        if ($this->queueRunning && $this->schedulerRunning && $this->paperlessConnected) {
            return 'healthy';
        } elseif ($this->queueRunning || $this->schedulerRunning) {
            return 'warning';
        }
        return 'error';
    }

    public function render()
    {
        return view('livewire.system-status');
    }
}
