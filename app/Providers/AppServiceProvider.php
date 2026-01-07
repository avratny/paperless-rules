<?php

namespace App\Providers;

use App\Listeners\QueueWorkerHeartbeat;
use App\Listeners\SchedulerHeartbeat;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register queue worker heartbeat listener
        Event::listen(Looping::class, QueueWorkerHeartbeat::class);

        // Register scheduler heartbeat listener
        Event::listen(ScheduledTaskStarting::class, SchedulerHeartbeat::class);
    }
}
