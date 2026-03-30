<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTPSRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRedirectToHttps($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    private function shouldRedirectToHttps(Request $request): bool
    {
        $forceHttps = filter_var(
            (string) env('FORCE_HTTPS', app()->environment('production')),
            FILTER_VALIDATE_BOOL
        );

        if (!$forceHttps) {
            return false;
        }

        return !$request->secure()
            && strtolower((string) $request->headers->get('X-Forwarded-Proto')) !== 'https';
    }
}
