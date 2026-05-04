<?php

use App\Support\DatabaseRuntime;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\XSSProtectionMiddleware::class,
            \App\Http\Middleware\SQLInjectionProtectionMiddleware::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\RateLimitMiddleware::class,
            \App\Http\Middleware\XSSProtectionMiddleware::class,
            \App\Http\Middleware\SQLInjectionProtectionMiddleware::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin.access' => \App\Http\Middleware\EnsureAdminAccess::class,
            'admin.permission' => \App\Http\Middleware\EnsureAdminPermission::class,
            'customer.api' => \App\Http\Middleware\EnsureCustomerApiToken::class,
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'xss.protection' => \App\Http\Middleware\XSSProtectionMiddleware::class,
            'sql.injection.protection' => \App\Http\Middleware\SQLInjectionProtectionMiddleware::class,
            'ip.whitelist' => \App\Http\Middleware\IPWhitelistMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $renderDatabaseUnavailable = function (\Throwable $e, Request $request) {
            if (! DatabaseRuntime::causedByConnectionFailure($e)) {
                return null;
            }

            DatabaseRuntime::markUnavailable();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => DatabaseRuntime::unavailableMessage(),
                ], 503);
            }

            return response()->view('errors.database-unavailable', [
                'message' => DatabaseRuntime::unavailableMessage(),
            ], 503);
        };

        $exceptions->render(function (QueryException $e, Request $request) use ($renderDatabaseUnavailable) {
            return $renderDatabaseUnavailable($e, $request);
        });

        $exceptions->render(function (\PDOException $e, Request $request) use ($renderDatabaseUnavailable) {
            return $renderDatabaseUnavailable($e, $request);
        });
    })->create();
