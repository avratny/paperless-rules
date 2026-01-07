<?php

namespace App\Livewire\Settings;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class PaperlessApiSettings extends Component
{
    public string $paperlessUrl = '';
    public string $paperlessApiKey = '';

    public bool $testingConnection = false;
    public ?bool $connectionSuccess = null;
    public ?string $connectionError = null;

    public function mount(SettingsService $settingsService): void
    {
        $this->paperlessUrl = $settingsService->getPaperlessUrl();
        $this->paperlessApiKey = $settingsService->getPaperlessApiKey();
    }

    protected function rules(): array
    {
        return [
            'paperlessUrl' => 'required|url',
            'paperlessApiKey' => 'required|string',
        ];
    }

    public function save(SettingsService $settingsService): void
    {
        try {
            $this->validate();

            $settingsService->setPaperlessCredentials(
                $this->paperlessUrl,
                $this->paperlessApiKey
            );

            session()->flash('success', __('Paperless API settings saved successfully!'));

            // Dispatch event to refresh system status
            $this->dispatch('check-system-status');
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
            $url = rtrim($this->paperlessUrl, '/');
            $apiKey = $this->paperlessApiKey;

            if (empty($url) || empty($apiKey)) {
                $this->connectionSuccess = false;
                $this->connectionError = __('URL and API Key are required');
                $this->testingConnection = false;
                return;
            }

            // Try to connect to Paperless API
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Accept' => 'application/json; version=9',
                ])
                ->get($url . '/api/documents/', [
                    'page' => 1,
                    'page_size' => 1,
                ]);

            if ($response->successful()) {
                $this->connectionSuccess = true;
                $this->connectionError = null;
            } elseif ($response->status() === 401 || $response->status() === 403) {
                $this->connectionSuccess = false;
                $this->connectionError = __('Invalid API key');
            } else {
                $this->connectionSuccess = false;
                $this->connectionError = __('HTTP :status', ['status' => $response->status()]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->connectionSuccess = false;
            $this->connectionError = __('Connection failed - please check the URL');
        } catch (\Exception $e) {
            $this->connectionSuccess = false;
            $this->connectionError = __('Error: :message', ['message' => substr($e->getMessage(), 0, 50)]);
        }

        $this->testingConnection = false;
    }

    public function render()
    {
        return view('livewire.settings.paperless-api-settings')->layout('layouts.app');
    }
}

