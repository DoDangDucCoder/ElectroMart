<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isLocalRequest = app()->environment('local') || in_array($request->getHost(), [
            '127.0.0.1',
            'localhost',
        ], true);

        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', $isLocalRequest ? 'SAMEORIGIN' : 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com https://cdn.datatables.net https://canvasjs.com https://cdn.canvasjs.com https://connect.facebook.net https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/ https://www.paypalobjects.com https://www.paypal.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.datatables.net; " .
               "font-src 'self' https://fonts.gstatic.com data:; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https://connect.facebook.net https://www.paypal.com https://www.paypalobjects.com ws: wss:; " .
               "frame-src 'self' https://www.facebook.com https://web.facebook.com https://www.google.com https://www.google.com/recaptcha/ https://www.paypal.com; " .
               ($isLocalRequest ? "frame-ancestors 'self' http://127.0.0.1:* http://localhost:*;" : "frame-ancestors 'none';");
        
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS (only for HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Remove server information
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
