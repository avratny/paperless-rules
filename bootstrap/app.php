<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'auth.check' => \App\Http\Middleware\CheckAuth::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Clean up old document locks every minute
        $schedule->command('documents:cleanup-locks')->everyMinute();

        // Poll Paperless NGX for new documents if polling mode is enabled
        // The interval is dynamically determined by the settings
        try {
            $settingsService = app(\App\Services\SettingsService::class);

            if ($settingsService->isPollingEnabled()) {
                $interval = $settingsService->getPollingInterval();

                // Schedule the polling command based on the configured interval
                $schedule->command('paperless:poll')
                    ->cron("*/{$interval} * * * *")
                    ->withoutOverlapping()
                    ->runInBackground();
            }
        } catch (\Exception $e) {
            // Ignore errors during migration or when database is not ready
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
