<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Login routes
Route::get('/login', \App\Livewire\Login::class)->name('login');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Protected routes
Route::middleware(\App\Http\Middleware\CheckAuth::class)->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/rules', \App\Livewire\Rules\RulesList::class)->name('rules.index');
    Route::get('/rules/editor', \App\Livewire\Rules\Editor::class)->name('rules.editor');
    Route::get('/rules/manual', \App\Livewire\Rules\ManualProcess::class)->name('rules.manual');

    Route::get('/processing-history', \App\Livewire\ProcessingHistory::class)->name('processing.history');

    Route::get('/docs', \App\Livewire\Documentation::class)->name('docs');

    // Settings requires admin when login is enabled
    Route::middleware(\App\Http\Middleware\CheckAuth::class . ':admin')->group(function () {
        Route::get('/settings', \App\Livewire\Settings::class)->name('settings');
        Route::get('/settings/paperless-api', \App\Livewire\Settings\PaperlessApiSettings::class)->name('settings.paperless-api');
        Route::get('/settings/document-processing', \App\Livewire\Settings\DocumentProcessingSettings::class)->name('settings.document-processing');
        Route::get('/settings/ollama', \App\Livewire\Settings\OllamaSettings::class)->name('settings.ollama');
        Route::get('/settings/authentication', \App\Livewire\Settings\AuthenticationSettings::class)->name('settings.authentication');
    });
});
