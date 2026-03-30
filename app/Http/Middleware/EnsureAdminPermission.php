<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user()
            ?? $request->user('web')
            ?? $request->user('sanctum');

        if (! $user) {
            if ($request->expectsJson()) {
                return new JsonResponse(['message' => 'Unauthenticated.'], 401);
            }

            return redirect('/admin');
        }

        if (! AdminAccess::canAccess($user)) {
            if ($request->expectsJson()) {
                return new JsonResponse(['message' => 'Forbidden.'], 403);
            }

            abort(403);
        }

        $requiredPermissions = collect($permissions)
            ->map(static fn (string $permission): string => Str::lower(trim($permission)))
            ->filter()
            ->values();

        if ($requiredPermissions->isEmpty()) {
            return $next($request);
        }

        $grantedPermissions = $user->getAllPermissions()
            ->pluck('name')
            ->map(static fn (string $permission): string => Str::lower(trim($permission)));

        if ($grantedPermissions->intersect($requiredPermissions)->isEmpty()) {
            if ($request->expectsJson()) {
                return new JsonResponse(['message' => 'Forbidden.'], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
