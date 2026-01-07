<?php

namespace App\Livewire\Settings;

use App\Services\SettingsService;
use Livewire\Component;

class DocumentProcessingSettings extends Component
{
    public int $documentLockDuration = 60;
    public string $processingMode = 'webhook';
    public int $pollingInterval = 5;

    public function mount(SettingsService $settingsService): void
    {
        $this->documentLockDuration = $settingsService->getDocumentLockDuration();
        $this->processingMode = $settingsService->getDocumentProcessingMode();
        $this->pollingInterval = $settingsService->getPollingInterval();
    }

    protected function rules(): array
    {
        $rules = [
            'processingMode' => 'required|in:webhook,polling',
            'pollingInterval' => 'required|integer|min:1',
            'documentLockDuration' => 'required|integer|min:5',
        ];

        // When polling is enabled, document lock duration must be at least as long as polling interval
        if ($this->processingMode === 'polling') {
            $minLockDuration = $this->pollingInterval * 60; // Convert minutes to seconds
            $rules['documentLockDuration'] = "required|integer|min:{$minLockDuration}";
        }

        return $rules;
    }

    public function save(SettingsService $settingsService): void
    {
        try {
            $this->validate();

            $settingsService->setDocumentLockDuration($this->documentLockDuration);
            $settingsService->setDocumentProcessingMode($this->processingMode);
            $settingsService->setPollingInterval($this->pollingInterval);

            session()->flash('success', __('Document processing settings saved successfully!'));
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.document-processing-settings')->layout('layouts.app');
    }
}

