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
        $enabled = (bool) config('security.ip_whitelist.enabled', false);

        if (!$enabled) {
            return true;
        }

        $configuredIps = config('security.ip_whitelist.allowed', []);
        if (is_string($configuredIps)) {
            $configuredIps = explode(',', $configuredIps);
        }

        $allowedIps = array_values(array_filter(array_map(
            static fn ($ip): string => trim((string) $ip),
            (array) $configuredIps
        )));

        if ($allowedIps === []) {
            return true;
        }

        return in_array((string) $request->ip(), $allowedIps, true);
    }
}
