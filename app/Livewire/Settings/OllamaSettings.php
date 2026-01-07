<?php

namespace App\Livewire\Settings;

use App\Services\Ollama\OllamaService;
use App\Services\SettingsService;
use Livewire\Component;

class OllamaSettings extends Component
{
    public string $ollamaUrl = '';
    public string $ollamaModel = '';
    public array $availableModels = [];

    public bool $testingConnection = false;
    public bool $loadingModels = false;
    public ?bool $connectionSuccess = null;
    public ?string $connectionError = null;

    public function mount(SettingsService $settingsService): void
    {
        $this->ollamaUrl = $settingsService->getOllamaUrl();
        $this->ollamaModel = $settingsService->getOllamaModel();

        // Try to load available models if URL is configured
        if (!empty($this->ollamaUrl)) {
            $this->loadAvailableModels();
        }
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

    public function testConnection(): void
    {
        $this->validate();

        $this->testingConnection = true;
        $this->connectionSuccess = null;
        $this->connectionError = null;

        try {
            $url = rtrim($this->ollamaUrl, '/');

            if (empty($url)) {
                $this->connectionSuccess = false;
                $this->connectionError = __('URL is required');
                $this->testingConnection = false;
                return;
            }

            // Test connection using Ollama client directly
            try {
                $client = \ArdaGnsrn\Ollama\Ollama::client($url);
                $client->models()->list();

                $this->connectionSuccess = true;
                $this->connectionError = null;

                // Reload available models after successful connection
                $this->loadAvailableModels();
            } catch (\Exception $e) {
                $this->connectionSuccess = false;
                $this->connectionError = __('Connection failed - please check the URL');
            }
        } catch (\Exception $e) {
            $this->connectionSuccess = false;
            $this->connectionError = __('Error: :message', ['message' => substr($e->getMessage(), 0, 50)]);
        }

        $this->testingConnection = false;
    }

    public function loadAvailableModels(): void
    {
        $this->loadingModels = true;

        try {
            // Use the current URL if available
            $url = !empty($this->ollamaUrl) ? rtrim($this->ollamaUrl, '/') : null;

            if (!$url) {
                $this->availableModels = [];
                $this->loadingModels = false;
                return;
            }

            $client = \ArdaGnsrn\Ollama\Ollama::client($url);
            $response = $client->models()->list();

            $models = [];
            foreach ($response->models as $model) {
                $models[] = $model->name;
            }

            $this->availableModels = $models;
        } catch (\Exception $e) {
            $this->availableModels = [];
        }

        $this->loadingModels = false;
    }

    public function render()
    {
        return view('livewire.settings.ollama-settings')->layout('layouts.app');
    }
}

