<?php

namespace App\Livewire;

use App\Services\SettingsService;
use Livewire\Component;

class Settings extends Component
{
    public function restartSetupWizard(SettingsService $settingsService): void
    {
        $settingsService->set('setup.completed', false);
        $this->redirect('/setup', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings')->layout('layouts.app');
    }
}

