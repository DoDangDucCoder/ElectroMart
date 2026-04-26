<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Shared\Services\CacheService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(
        private readonly CacheService $cacheService
    ) {
    }

    public function check(): JsonResponse
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
        ];

        // Check database connection
        try {
            \DB::connection()->getPdo();
            $status['database'] = 'connected';
        } catch (\Exception $e) {
            $status['database'] = 'disconnected';
            $status['status'] = 'unhealthy';
        }

        // Check cache
        try {
            $this->cacheService->remember('health_check', fn() => 'ok', 60);
            $status['cache'] = 'working';
        } catch (\Exception $e) {
            $status['cache'] = 'error';
            $status['status'] = 'degraded';
        }

        // Check storage
        try {
            \Storage::disk('public')->put('health_check.txt', 'ok');
            \Storage::disk('public')->delete('health_check.txt');
            $status['storage'] = 'working';
        } catch (\Exception $e) {
            $status['storage'] = 'error';
            $status['status'] = 'degraded';
        }

        $httpStatus = match($status['status']) {
            'healthy' => 200,
            'degraded' => 200,
            'unhealthy' => 503,
            default => 200,
        };

        return response()->json($status, $httpStatus);
    }

    public function cache(): JsonResponse
    {
        try {
            $stats = $this->cacheService->getCacheStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Thống kê cache hiện tạm thời chưa sẵn sàng.',
                'data' => [],
            ]);
        }
    }

    public function performance(): JsonResponse
    {
        $startTime = defined('LARAVEL_START')
            ? (float) constant('LARAVEL_START')
            : (float) ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));

        $stats = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'execution_time' => max(0, microtime(true) - $startTime),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
