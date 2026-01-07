<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $requireAdmin = null): Response
    {
        // If login is not enabled, allow access
        if (!$this->settingsService->isLoginEnabled()) {
            return $next($request);
        }

        // Login is enabled, check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // If admin is required, check if user is administrator
        if ($requireAdmin === 'admin' && !auth()->user()->isAdministrator()) {
            abort(403, __('This action is unauthorized.'));
        }

        return $next($request);
    }
}

