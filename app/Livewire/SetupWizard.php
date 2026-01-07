<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SetupWizard extends Component
{
    // Current step (1-3)
    public int $currentStep = 1;

    // Step 1: Paperless Connection
    public string $paperlessUrl = '';
    public string $paperlessApiKey = '';
    public bool $testingConnection = false;
    public ?bool $connectionSuccess = null;
    public ?string $connectionError = null;

    // Step 2: Document Processing
    public string $processingMode = 'polling';
    public int $pollingInterval = 5;
    public int $documentLockDuration = 300;

    // Step 3: Authentication
    public bool $enableAuthentication = false;
    public string $adminName = '';
    public string $adminEmail = '';
    public string $adminPassword = '';
    public string $adminPasswordConfirmation = '';
    public bool $adminExists = false;

    // Track if component is mounted
    private bool $isMounted = false;

    /**
     * Reset connection status when URL or API key changes (after mount)
     */
    public function updated($propertyName): void
    {
        if ($this->isMounted && in_array($propertyName, ['paperlessUrl', 'paperlessApiKey'])) {
            $this->connectionSuccess = null;
            $this->connectionError = null;
        }
    }

    public function mount(SettingsService $settingsService): void
    {
        // If setup is already completed, redirect to home
        // (Setup can only be restarted via Settings page which sets setup.completed to false)
        if ($settingsService->isSetupCompleted()) {
            $this->redirect('/', navigate: true);
            return;
        }

        // Check if admin user already exists
        $this->adminExists = User::where('role', 'administrator')->exists();

        // Load existing settings from database or use defaults
        try {
            $this->paperlessUrl = $settingsService->getPaperlessUrl();
            $this->paperlessApiKey = $settingsService->getPaperlessApiKey();
            $this->processingMode = $settingsService->getDocumentProcessingMode();
            $this->pollingInterval = $settingsService->getPollingInterval();
            $this->documentLockDuration = $settingsService->getDocumentLockDuration();
            $this->enableAuthentication = $settingsService->isLoginEnabled();
        } catch (\Exception $e) {
            // If loading settings fails (e.g., decryption error), use defaults
            $this->paperlessUrl = 'http://localhost:8010';
            $this->paperlessApiKey = '';
            $this->processingMode = 'polling';
            $this->pollingInterval = 5;
            $this->documentLockDuration = 300;
            $this->enableAuthentication = false;
        }

        // Mark as mounted to enable updated() hook
        $this->isMounted = true;
    }

    public function testConnection(): void
    {
        $this->testingConnection = true;

        try {
            $url = rtrim($this->paperlessUrl, '/');
            $apiKey = $this->paperlessApiKey;

            // Try to connect to Paperless API
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
        } finally {
            $this->testingConnection = false;
        }
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            // Reset connection status before testing
            $this->connectionSuccess = null;
            $this->connectionError = null;

            // Validate Paperless connection
            $this->validate([
                'paperlessUrl' => 'required|url',
                'paperlessApiKey' => 'required|string',
            ]);

            // Always test connection before proceeding
            $this->testConnection();

            // Connection must be successful (check after testConnection has run)
            if ($this->connectionSuccess !== true) {
                // Error message is already set by testConnection
                return;
            }

            $this->currentStep = 2;
        } elseif ($this->currentStep === 2) {
            // Validate document processing settings
            $rules = [
                'processingMode' => 'required|in:webhook,polling',
                'pollingInterval' => 'required|integer|min:1',
                'documentLockDuration' => 'required|integer|min:5',
            ];

            // When polling is enabled, document lock duration must be at least as long as polling interval
            if ($this->processingMode === 'polling') {
                $minLockDuration = $this->pollingInterval * 60;
                $rules['documentLockDuration'] = "required|integer|min:{$minLockDuration}";
            }

            $this->validate($rules);
            $this->currentStep = 3;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function completeSetup(SettingsService $settingsService): void
    {
        // Validate authentication settings if enabled and no admin exists yet
        if ($this->enableAuthentication && !$this->adminExists) {
            $this->validate([
                'adminName' => 'required|string|max:255',
                'adminEmail' => 'required|email|max:255|unique:users,email',
                'adminPassword' => 'required|min:8|same:adminPasswordConfirmation',
            ]);

            // Create admin user
            User::create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => Hash::make($this->adminPassword),
                'role' => 'administrator',
            ]);

            // Enable login
            $settingsService->setLoginEnabled(true);
        } elseif ($this->enableAuthentication && $this->adminExists) {
            // Admin exists, just ensure login is enabled
            $settingsService->setLoginEnabled(true);
        }

        // Save Paperless credentials
        $settingsService->setPaperlessCredentials(
            $this->paperlessUrl,
            $this->paperlessApiKey
        );

        // Save document processing settings
        $settingsService->setDocumentProcessingMode($this->processingMode);
        $settingsService->setPollingInterval($this->pollingInterval);
        $settingsService->setDocumentLockDuration($this->documentLockDuration);

        // Mark setup as completed
        $settingsService->markSetupCompleted();

        // Redirect to home
        session()->flash('success', __('Setup completed successfully! Welcome to Paperless Rules.'));
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.setup-wizard')->layout('layouts.guest');
    }
}

