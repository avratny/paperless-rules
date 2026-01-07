<?php

namespace App\Livewire\Settings;

use App\Services\SettingsService;
use Livewire\Component;

class OllamaSettings extends Component
{
    public string $ollamaUrl = '';
    public string $ollamaModel = '';

    public function mount(SettingsService $settingsService): void
    {
        $this->ollamaUrl = $settingsService->getOllamaUrl();
        $this->ollamaModel = $settingsService->getOllamaModel();
    }

    protected function rules(): array
    {
        return [
            'ollamaUrl' => 'required|url',
            'ollamaModel' => 'required|string',
        ];
    }

    public function save(SettingsService $settingsService): void
    {
        try {
            $this->validate();

            $settingsService->setOllamaCredentials(
                $this->ollamaUrl,
                $this->ollamaModel
            );

            session()->flash('success', __('Ollama AI settings saved successfully!'));
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.ollama-settings')->layout('layouts.app');
    }
}

