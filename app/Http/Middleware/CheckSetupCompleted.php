<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupCompleted
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for setup wizard route itself
        if ($request->routeIs('setup.wizard')) {
            return $next($request);
        }

        // If setup is not completed, redirect to setup wizard
        if (!$this->settingsService->isSetupCompleted()) {
            return redirect()->route('setup.wizard');
        }

        return $next($request);
    }
}

