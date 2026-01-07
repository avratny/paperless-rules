<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;

class LanguageChooser extends Component
{
    public $currentLocale;

    public function mount()
    {
        $this->currentLocale = session('locale', config('app.locale', 'en'));
    }

    public function switchLanguage($locale)
    {
        if (in_array($locale, ['en', 'de'])) {
            session(['locale' => $locale]);
            $this->currentLocale = $locale;

            // Dispatch Event für JavaScript
            $this->dispatch('locale-changed');
        }
    }

    public function render()
    {
        return view('livewire.language-chooser');
    }
}
