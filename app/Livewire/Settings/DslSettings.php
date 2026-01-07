<?php

namespace App\Livewire\Settings;

use App\Services\SettingsService;
use Livewire\Component;

class DslSettings extends Component
{
    public int $maxDslLength = 10000;
    public int $maxLetCount = 50;
    public int $maxDoCount = 50;
    public int $maxNestingDepth = 10;

    public function mount(SettingsService $settingsService): void
    {
        $this->maxDslLength = $settingsService->getMaxDslLength();
        $this->maxLetCount = $settingsService->getMaxLetCount();
        $this->maxDoCount = $settingsService->getMaxDoCount();
        $this->maxNestingDepth = $settingsService->getMaxNestingDepth();
    }

    protected function rules(): array
    {
        return [
            'maxDslLength' => 'required|integer|min:1000|max:100000',
            'maxLetCount' => 'required|integer|min:1|max:1000',
            'maxDoCount' => 'required|integer|min:1|max:1000',
            'maxNestingDepth' => 'required|integer|min:1|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'maxDslLength.required' => __('Maximum DSL length is required.'),
            'maxDslLength.integer' => __('Maximum DSL length must be a number.'),
            'maxDslLength.min' => __('Maximum DSL length must be at least 1000 characters.'),
            'maxDslLength.max' => __('Maximum DSL length cannot exceed 100000 characters.'),
            'maxLetCount.required' => __('Maximum LET count is required.'),
            'maxLetCount.integer' => __('Maximum LET count must be a number.'),
            'maxLetCount.min' => __('Maximum LET count must be at least 1.'),
            'maxLetCount.max' => __('Maximum LET count cannot exceed 1000.'),
            'maxDoCount.required' => __('Maximum DO count is required.'),
            'maxDoCount.integer' => __('Maximum DO count must be a number.'),
            'maxDoCount.min' => __('Maximum DO count must be at least 1.'),
            'maxDoCount.max' => __('Maximum DO count cannot exceed 1000.'),
            'maxNestingDepth.required' => __('Maximum nesting depth is required.'),
            'maxNestingDepth.integer' => __('Maximum nesting depth must be a number.'),
            'maxNestingDepth.min' => __('Maximum nesting depth must be at least 1.'),
            'maxNestingDepth.max' => __('Maximum nesting depth cannot exceed 100.'),
        ];
    }

    public function save(SettingsService $settingsService): void
    {
        try {
            $this->validate();

            $settingsService->setMany([
                'limits.max_dsl_length' => $this->maxDslLength,
                'limits.max_let_count' => $this->maxLetCount,
                'limits.max_do_count' => $this->maxDoCount,
                'limits.max_nesting_depth' => $this->maxNestingDepth,
            ]);

            session()->flash('success', __('DSL settings saved successfully!'));
        } catch (\Exception $e) {
            session()->flash('error', __('Error: :message', ['message' => $e->getMessage()]));
        }
    }

    public function resetToDefaults(SettingsService $settingsService): void
    {
        $this->maxDslLength = 10000;
        $this->maxLetCount = 50;
        $this->maxDoCount = 50;
        $this->maxNestingDepth = 10;

        $settingsService->setMany([
            'limits.max_dsl_length' => $this->maxDslLength,
            'limits.max_let_count' => $this->maxLetCount,
            'limits.max_do_count' => $this->maxDoCount,
            'limits.max_nesting_depth' => $this->maxNestingDepth,
        ]);

        session()->flash('success', __('DSL settings reset to defaults!'));
    }

    public function render()
    {
        return view('livewire.settings.dsl-settings')->layout('layouts.app');
    }
}

