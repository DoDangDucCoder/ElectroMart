<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $this->policy());
        }

        return $response;
    }

    private function policy(): string
    {
        return implode('; ', [
            "default-src 'self' https: data: blob:",
            "img-src 'self' https: data: blob:",
            "style-src 'self' https: 'unsafe-inline'",
            "script-src 'self' https: 'unsafe-inline' 'unsafe-eval'",
            "font-src 'self' https: data:",
            "connect-src 'self' https: ws: wss:",
            "frame-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https:",
        ]);
    }
}
