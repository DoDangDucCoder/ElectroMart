<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CSRFProtectionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF for API routes
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Skip CSRF for GET, HEAD, OPTIONS requests
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        // Check CSRF token
        if (!$this->isValidCSRFToken($request)) {
            Log::warning('CSRF token validation failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'CSRF token mismatch',
                    'message' => 'Mã bảo mật không hợp lệ. Vui lòng tải lại trang và thử lại.'
                ], 419);
            }

            return redirect()->back()->withErrors([
                'csrf' => 'Security token expired. Please try again.'
            ]);
        }

        return $next($request);
    }

    private function isValidCSRFToken(Request $request): bool
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token) {
            return false;
        }

        return hash_equals(session()->token(), $token);
    }
}
