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
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin.access' => \App\Http\Middleware\EnsureAdminAccess::class,
            'admin.permission' => \App\Http\Middleware\EnsureAdminPermission::class,
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
