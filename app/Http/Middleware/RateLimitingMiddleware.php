<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitingMiddleware
{
    private int $maxRequests = 100; // requests per minute
    private int $windowMinutes = 1;

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->getRateLimitKey($request);
        
        if ($this->isRateLimited($key)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'key' => $key,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Bạn gửi quá nhiều yêu cầu. Vui lòng thử lại sau.',
                    'retry_after' => $this->getRetryAfter($key)
                ], 429);
            }

            return response('Bạn gửi quá nhiều yêu cầu. Vui lòng thử lại sau.', 429)
                ->header('Retry-After', $this->getRetryAfter($key));
        }

        $this->incrementRequestCount($key);

        return $next($request);
    }

    private function getRateLimitKey(Request $request): string
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Create a more specific key for authenticated users
        if ($request->user()) {
            return 'rate_limit_user_' . $request->user()->id;
        }
        
        return 'rate_limit_ip_' . md5($ip . $userAgent);
    }

    private function isRateLimited(string $key): bool
    {
        $count = Cache::get($key, 0);
        return $count >= $this->maxRequests;
    }

    private function incrementRequestCount(string $key): void
    {
        // Fix #17: Dùng Cache::increment thay get/put để atomic, tránh race condition
        $ttl = now()->addMinutes($this->windowMinutes);

        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, $ttl);
        }
    }

    private function getRetryAfter(string $key): int
    {
        $expiresAt = Cache::get($key . '_expires');
        if ($expiresAt) {
            return max(0, $expiresAt - time());
        }
        
        return 60; // Default 1 minute
    }
}
