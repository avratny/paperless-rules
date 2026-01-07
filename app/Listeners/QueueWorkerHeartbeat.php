<?php

namespace App\Listeners;

use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Cache;

class QueueWorkerHeartbeat
{
    /**
     * Handle the event.
     */
    public function handle(Looping $event): void
    {
        // Update heartbeat cache every time the queue worker loops
        Cache::put('queue:worker:heartbeat', now(), 60); // 1 minute TTL
    }
}
