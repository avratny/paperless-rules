<?php

namespace App\Listeners;

use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Support\Facades\Cache;

class SchedulerHeartbeat
{
    /**
     * Handle the event.
     */
    public function handle(ScheduledTaskStarting $event): void
    {
        // Update heartbeat cache every time a scheduled task starts
        Cache::put('scheduler:heartbeat', now(), 120); // 2 minutes TTL
    }
}
