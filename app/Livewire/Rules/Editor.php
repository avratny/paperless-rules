<?php

namespace App\Livewire\Rules;

use App\Models\Rule;
use App\Services\Rules\DslParser;
use App\Services\Rules\DslParseException;
use App\Services\SettingsService;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Editor extends Component
{
    public ?Rule $rule = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('boolean')]
    public $enabled = false;

    #[Validate('boolean')]
    public $on_create = false;

    #[Validate('boolean')]
    public $on_change = false;

    #[Validate('integer|min:0')]
    public $order = 0;

    #[Validate('nullable|string')]
    public $ruleContent = '';

    // Syntax validation
    public array $syntaxErrors = [];
    public bool $syntaxValid = true;

    // Test execution
    public $testDocumentId = '';
    public $testRunning = false;
    public $testResult = null;
    public $testError = null;
    public $paperlessUrl = '';

    public function mount(SettingsService $settingsService)
    {
        $this->paperlessUrl = rtrim($settingsService->getPaperlessUrl(), '/');

        $ruleId = request()->query('rule');

        if ($ruleId) {
            $this->rule = Rule::findOrFail($ruleId);
            $this->name = $this->rule->name;
            $this->enabled = $this->rule->enabled;
            $this->on_create = $this->rule->on_create;
            $this->on_change = $this->rule->on_change;
            $this->order = $this->rule->order ?? 0;
            $this->ruleContent = $this->rule->rule ?? '';

            // Validate syntax on load
            $this->validateSyntax();
        }
    }

    public function save()
    {
        $this->validate();

        // Validate syntax before saving
        $this->validateSyntax();
        if (!$this->syntaxValid) {
            // Show confirmation dialog
            $this->dispatch('confirm-save-with-errors');
            return;
        }

        $this->performSave(false);
    }

    /**
     * Save the rule with errors (disabled)
     */
    public function saveWithErrors()
    {
        $this->validate();

        $this->performSave(true);
    }

    /**
     * Perform the actual save operation
     */
    private function performSave(bool $forceDisabled = false)
    {
        // Normalize DSL code (remove indentation) before saving
        $normalizedRule = $this->normalizeDsl($this->ruleContent);

        // If saving with errors, force disable the rule
        $enabled = $forceDisabled ? false : $this->enabled;

        if ($forceDisabled) {
            $this->enabled = false;
        }

        if ($this->rule) {
            // Update existing rule
            $this->rule->update([
                'name' => $this->name,
                'enabled' => $enabled,
                'on_create' => $this->on_create,
                'on_change' => $this->on_change,
                'order' => $this->order,
                'rule' => $normalizedRule,
            ]);

            $message = $forceDisabled
                ? __('Rule was saved with errors and disabled!')
                : __('Rule successfully updated!');
            session()->flash('message', $message);
        } else {
            // Create new rule
            Rule::create([
                'name' => $this->name,
                'enabled' => $enabled,
                'on_create' => $this->on_create,
                'on_change' => $this->on_change,
                'order' => $this->order,
                'rule' => $normalizedRule,
            ]);

            $message = $forceDisabled
                ? __('Rule was created with errors and disabled!')
                : __('Rule successfully created!');
            session()->flash('message', $message);

            return redirect()->route('rules.index');
        }
    }

    /**
     * Normalize DSL code by removing indentation and uppercasing keywords
     */
    private function normalizeDsl(?string $dsl): string
    {
        if (empty($dsl)) {
            return '';
        }

        $lines = explode("\n", $dsl);
        $normalized = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                // Uppercase keywords while preserving everything else
                $normalizedLine = $this->uppercaseKeywords($trimmed);
                $normalized[] = $normalizedLine;
            }
        }

        return implode("\n", $normalized);
    }

    /**
     * Uppercase DSL keywords while preserving strings, variables, and other content
     */
    private function uppercaseKeywords(string $line): string
    {
        // Skip comment lines
        if (str_starts_with($line, '//')) {
            return $line;
        }

        // Keywords that should be uppercased
        $keywords = ['WHEN', 'THEN', 'ELSE', 'END', 'LET', 'DO'];

        // Pattern to match keywords at the start of a line (case-insensitive)
        // This ensures we only uppercase keywords, not parts of strings or variable names
        foreach ($keywords as $keyword) {
            // Match keyword at the start of the line
            if (preg_match('/^(' . $keyword . ')(\s|$)/i', $line, $matches)) {
                $line = $keyword . substr($line, strlen($matches[1]));
                break;
            }
        }

        return $line;
    }

    public function cancel()
    {
        return redirect()->route('rules.index');
    }

    /**
     * Validate DSL syntax and update error state
     */
    public function validateSyntax(): void
    {
        $this->syntaxErrors = [];
        $this->syntaxValid = true;

        if (empty(trim($this->ruleContent))) {
            return;
        }

        try {
            $parser = new DslParser();
            $parser->parse($this->ruleContent);
        } catch (DslParseException $e) {
            $this->syntaxErrors = $e->getErrors();
            $this->syntaxValid = false;
        } catch (\Exception $e) {
            $this->syntaxErrors = [$e->getMessage()];
            $this->syntaxValid = false;
        }
    }

    /**
     * Called when ruleContent changes
     */
    public function updatedRuleContent(): void
    {
        $this->validateSyntax();
    }

    /**
     * Test the rule on a specific document
     */
    public function testOnDocument()
    {
        // Validate document ID
        $this->validate([
            'testDocumentId' => 'required|integer|min:1',
        ], [
            'testDocumentId.required' => __('Please enter a document ID.'),
            'testDocumentId.integer' => __('The document ID must be a number.'),
            'testDocumentId.min' => __('The document ID must be at least 1.'),
        ]);

        // Validate all fields
        $this->validate();

        // Check syntax first
        $this->validateSyntax();
        if (!$this->syntaxValid) {
            $this->testError = __('Cannot execute rule with syntax errors.');
            return;
        }

        $this->testRunning = true;
        $this->testResult = null;
        $this->testError = null;

        try {
            // Save the rule first to ensure we're using the current version
            $this->performSave(false);

            $paperlessService = new \App\Services\Paperless\PaperlessService();
            $ruleService = new \App\Services\Rules\RuleService();

            // Load document
            $document = $paperlessService->loadDocument((int) $this->testDocumentId);

            // Execute rule using the current ruleContent (NOT in dry-run mode)
            $result = $ruleService->executeRule($this->ruleContent, $document, false);

            // Save document if modified
            if ($document->isModified()) {
                $document->save();
            }

            $this->testResult = [
                'success' => $result['success'] ?? false,
                'trace' => $result['trace'] ?? [],
                'modified' => $result['modified'] ?? false,
            ];
        } catch (\Exception $e) {
            $this->testError = $e->getMessage();
        } finally {
            $this->testRunning = false;
        }
    }

    /**
     * Close the test modal
     */
    public function closeTestModal()
    {
        $this->testDocumentId = '';
        $this->testResult = null;
        $this->testError = null;
    }

    public function render()
    {
        return view('livewire.rules.editor');
    }
}
