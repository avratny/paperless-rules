<?php

namespace App\Livewire;

use App\Models\RuleExecutionLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProcessingHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, success, error, skipped
    public $filterEventType = 'all'; // all, on_create, on_change
    public $filterDocumentId = '';
    public $filterRuleId = '';
    public $perPage = 25;
    public $expandedJobs = []; // Track which jobs are expanded

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'filterEventType' => ['except' => 'all'],
        'filterDocumentId' => ['except' => ''],
        'filterRuleId' => ['except' => ''],
        'perPage' => ['except' => 25],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterEventType()
    {
        $this->resetPage();
    }

    public function updatingFilterDocumentId()
    {
        $this->resetPage();
    }

    public function updatingFilterRuleId()
    {
        $this->resetPage();
    }

    public function toggleJob($jobId)
    {
        if (in_array($jobId, $this->expandedJobs)) {
            $this->expandedJobs = array_diff($this->expandedJobs, [$jobId]);
        } else {
            $this->expandedJobs[] = $jobId;
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterStatus',
            'filterEventType',
            'filterDocumentId',
            'filterRuleId',
        ]);
        $this->resetPage();
    }

    public function render()
    {
        // Get unique job IDs with filters applied
        $jobIdsQuery = RuleExecutionLog::query()
            ->select('job_id', DB::raw('MIN(executed_at) as first_executed_at'))
            ->groupBy('job_id');

        // Apply filters to job selection
        if ($this->search) {
            $jobIdsQuery->where('rule_name', 'like', '%' . $this->search . '%');
        }
        if ($this->filterStatus !== 'all') {
            $jobIdsQuery->where('status', $this->filterStatus);
        }
        if ($this->filterEventType !== 'all') {
            $jobIdsQuery->where('event_type', $this->filterEventType);
        }
        if ($this->filterDocumentId) {
            $jobIdsQuery->where('document_id', (int) $this->filterDocumentId);
        }
        if ($this->filterRuleId) {
            $jobIdsQuery->where('rule_id', (int) $this->filterRuleId);
        }

        // Order by first execution time descending
        $jobIdsQuery->orderBy('first_executed_at', 'desc');

        // Paginate job IDs
        $paginatedJobs = $jobIdsQuery->paginate((int) $this->perPage);

        // Get all logs for the paginated job IDs
        $jobIds = $paginatedJobs->pluck('job_id');
        $logs = RuleExecutionLog::query()
            ->with('rule')
            ->whereIn('job_id', $jobIds)
            ->orderBy('executed_at', 'asc')
            ->get()
            ->groupBy('job_id');

        // Get statistics
        $stats = $this->getStatistics();

        return view('livewire.processing-history', [
            'jobs' => $paginatedJobs,
            'logs' => $logs,
            'stats' => $stats,
            'expandedJobs' => $this->expandedJobs,
        ]);
    }

    private function getStatistics(): array
    {
        $baseQuery = RuleExecutionLog::query();

        // Apply same filters as main query
        if ($this->search) {
            $baseQuery->where('rule_name', 'like', '%' . $this->search . '%');
        }
        if ($this->filterEventType !== 'all') {
            $baseQuery->where('event_type', $this->filterEventType);
        }
        if ($this->filterDocumentId) {
            $baseQuery->where('document_id', (int) $this->filterDocumentId);
        }
        if ($this->filterRuleId) {
            $baseQuery->where('rule_id', (int) $this->filterRuleId);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'success' => (clone $baseQuery)->where('status', 'success')->count(),
            'error' => (clone $baseQuery)->where('status', 'error')->count(),
            'skipped' => (clone $baseQuery)->where('status', 'skipped')->count(),
        ];
    }


}

