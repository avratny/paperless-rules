<?php

namespace App\Livewire\Rules;

use App\Models\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class RulesList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterEnabled = 'all'; // all, enabled, disabled
    public $filterOnCreate = 'all'; // all, yes, no
    public $filterOnChange = 'all'; // all, yes, no
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEnabled' => ['except' => 'all'],
        'filterOnCreate' => ['except' => 'all'],
        'filterOnChange' => ['except' => 'all'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEnabled()
    {
        $this->resetPage();
    }

    public function updatingFilterOnCreate()
    {
        $this->resetPage();
    }

    public function updatingFilterOnChange()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterEnabled = 'all';
        $this->filterOnCreate = 'all';
        $this->filterOnChange = 'all';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $query = Rule::query();

        // Search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Enabled filter
        if ($this->filterEnabled === 'enabled') {
            $query->where('enabled', true);
        } elseif ($this->filterEnabled === 'disabled') {
            $query->where('enabled', false);
        }

        // On Create filter
        if ($this->filterOnCreate === 'yes') {
            $query->where('on_create', true);
        } elseif ($this->filterOnCreate === 'no') {
            $query->where('on_create', false);
        }

        // On Change filter
        if ($this->filterOnChange === 'yes') {
            $query->where('on_change', true);
        } elseif ($this->filterOnChange === 'no') {
            $query->where('on_change', false);
        }

        // Pagination
        if ($this->perPage === 'all') {
            $rules = $query->orderBy('order', 'asc')->orderBy('created_at', 'asc')->get();
            // Erstelle ein Paginator-ähnliches Objekt für "alle"
            $rules = new \Illuminate\Pagination\LengthAwarePaginator(
                $rules,
                $rules->count(),
                $rules->count(),
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $rules = $query->orderBy('order', 'asc')->orderBy('created_at', 'asc')->paginate((int) $this->perPage);
        }

        return view('livewire.rules.rules-list', [
            'rules' => $rules,
        ]);
    }
}

