<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IPWhitelistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->allowsRequest($request)) {
            return response()->json([
                'error' => 'IP address is not allowed'
            ], 403);
        }

        return $next($request);
    }

    private function allowsRequest(Request $request): bool
    {
        $enabled = filter_var(
            (string) env('IP_WHITELIST_ENABLED', false),
            FILTER_VALIDATE_BOOL
        );

        if (!$enabled) {
            return true;
        }

        $allowedIps = array_values(array_filter(array_map(
            static fn (string $ip): string => trim($ip),
            explode(',', (string) env('IP_WHITELIST', ''))
        )));

        if ($allowedIps === []) {
            return true;
        }

        return in_array((string) $request->ip(), $allowedIps, true);
    }
}
