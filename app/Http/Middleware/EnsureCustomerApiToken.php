<?php

namespace App\Http\Middleware;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Customer) {
            return response()->json([
                'message' => 'Customer API token required.',
            ], 403);
        }

        return $next($request);
    }
}
