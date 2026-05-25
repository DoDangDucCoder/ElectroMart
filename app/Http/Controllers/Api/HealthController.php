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
        return response()->json(['status' => 'ok']);
    }

    public function cache(): JsonResponse
    {
        $this->abortDetailedHealthOutsideLocal();

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
        $this->abortDetailedHealthOutsideLocal();

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

    private function abortDetailedHealthOutsideLocal(): void
    {
        abort_unless(app()->environment('local'), 404);
    }
}
