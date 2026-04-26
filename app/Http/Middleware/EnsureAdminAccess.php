<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards === [] ? ['web'] : $guards;
        $user = null;

        foreach ($guards as $guard) {
            $candidate = $request->user($guard);

            if ($candidate) {
                $user = $candidate;
                break;
            }
        }

        if (! $user) {
            if ($request->expectsJson()) {
                return new JsonResponse(['message' => 'Chưa đăng nhập.'], 401);
            }

            return new RedirectResponse(url('/admin'));
        }

        if (! AdminAccess::canAccess($user)) {
            if ($request->expectsJson()) {
                return new JsonResponse(['message' => 'Không có quyền truy cập.'], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
