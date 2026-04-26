<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $key = 'default', int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'key' => $key,
                'retry_after' => $retryAfter,
            ]);

            return response()->json([
                'message' => 'Bạn gửi quá nhiều yêu cầu. Vui lòng thử lại sau.',
                'retry_after' => $retryAfter,
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
        $response->headers->set('X-RateLimit-Reset', time() + RateLimiter::availableIn($key));

        return $response;
    }

    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $user = $request->user();
        
        if ($user) {
            return "rate_limit:{$key}:user:{$user->id}";
        }

        return "rate_limit:{$key}:ip:" . $request->ip();
    }
}
