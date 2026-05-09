<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BlockAdminCreateActions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('admin_features.create_locked', false)) {
            return $next($request);
        }

        if (! $request->isMethod('post')) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $lockedPaths = (array) config('admin_features.create_post_paths', []);

        if (! Str::is($lockedPaths, $path)) {
            return $next($request);
        }

        $message = (string) config('admin_features.create_locked_message', 'Chức năng thêm mới đang tạm khóa.');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 423);
        }

        return redirect()->back()->with('error', $message);
    }
}
