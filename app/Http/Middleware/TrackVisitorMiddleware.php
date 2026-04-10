<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests to the frontend
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Exclude admin, api, and filament internal routes
            if (!$request->is('admin*') && !$request->is('livewire*') && !$request->is('filament*')) {
                AnalyticsService::logPageView();
            }
        }

        return $response;
    }
}
