<?php

namespace App\Livewire\Rules;

use App\Models\Rule;
use App\Services\Paperless\PaperlessService;
use App\Services\Rules\RuleService;
use Livewire\Component;

class ManualProcess extends Component
{
    public $documentId = '';
    public $selectedRules = []; // Array of rule IDs in execution order
    public $searchQuery = '';
    public $showDropdown = false;
    public $processing = false;
    public $results = [];
    public $error = null;
    public $success = null;

    protected $rules = [
        'documentId' => 'required|integer|min:1',
        'selectedRules' => 'required|array|min:1',
    ];

    protected function messages()
    {
        return [
            'documentId.required' => __('Please enter a document ID.'),
            'documentId.integer' => __('The document ID must be a number.'),
            'documentId.min' => __('The document ID must be at least 1.'),
            'selectedRules.required' => __('Please select at least one rule.'),
            'selectedRules.min' => __('Please select at least one rule.'),
        ];
    }

    public function addRule($ruleId)
    {
        if (!in_array($ruleId, $this->selectedRules)) {
            $this->selectedRules[] = $ruleId;
        }
        $this->searchQuery = '';
        $this->showDropdown = false;
    }

    public function removeRule($ruleId)
    {
        $this->selectedRules = array_values(array_filter(
            $this->selectedRules,
            fn($id) => $id != $ruleId
        ));
    }

    public function moveRuleUp($index)
    {
        if ($index > 0) {
            $temp = $this->selectedRules[$index - 1];
            $this->selectedRules[$index - 1] = $this->selectedRules[$index];
            $this->selectedRules[$index] = $temp;
        }
    }

    public function moveRuleDown($index)
    {
        if ($index < count($this->selectedRules) - 1) {
            $temp = $this->selectedRules[$index + 1];
            $this->selectedRules[$index + 1] = $this->selectedRules[$index];
            $this->selectedRules[$index] = $temp;
        }
    }

    public function updateOrder($orderedIds)
    {
        $this->selectedRules = $orderedIds;
    }

    public function process()
    {
        $this->validate();

        $this->processing = true;
        $this->results = [];
        $this->error = null;
        $this->success = null;

        try {
            $paperlessService = new PaperlessService();
            $ruleService = new RuleService();

            // Dokument laden
            $document = $paperlessService->loadDocument((int) $this->documentId);

            // Regeln in der festgelegten Reihenfolge ausführen
            foreach ($this->selectedRules as $ruleId) {
                $rule = Rule::where('id', $ruleId)->where('enabled', true)->first();
                if (!$rule) {
                    continue;
                }

                $result = $ruleService->executeRule($rule->rule, $document, false);
                $this->results[] = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'success' => $result['success'] ?? false,
                    'modified' => $result['modified'] ?? false,
                    'trace' => $result['trace'] ?? [],
                ];
            }

            // Änderungen immer nach Paperless speichern (force=true)
            $document->save(force: true);

            $this->success = __('Document was successfully processed and saved.');
        } catch (\Exception $e) {
            $this->error = __('Error: :message', ['message' => $e->getMessage()]);
        } finally {
            $this->processing = false;
        }
    }

    public function getSelectedRulesDataProperty()
    {
        if (empty($this->selectedRules)) {
            return collect();
        }

        // Regeln in der Reihenfolge von selectedRules zurückgeben
        $rules = Rule::whereIn('id', $this->selectedRules)->get()->keyBy('id');

        return collect($this->selectedRules)->map(fn($id) => $rules->get($id))->filter();
    }

    public function getFilteredRulesProperty()
    {
        $query = Rule::where('enabled', true);

        if ($this->searchQuery) {
            $query->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        // Bereits ausgewählte Regeln ausschließen
        if (!empty($this->selectedRules)) {
            $query->whereNotIn('id', $this->selectedRules);
        }

        return $query->orderBy('name')->limit(10)->get();
    }

    public function render()
    {
        return view('livewire.rules.manual-process');
    }
}

