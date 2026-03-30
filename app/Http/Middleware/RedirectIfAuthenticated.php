<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * @param  Closure(Request): Response  $next
     * @param  string[]  ...$guards
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }

            $user = Auth::guard($guard)->user();

            if ($guard === null || $guard === 'web') {
                $target = AdminAccess::canAccess($user)
                    ? route('admin.dashboard')
                    : url('/');

                return new RedirectResponse($target);
            }

            return new RedirectResponse(url('/'));
        }

        return $next($request);
    }
}
