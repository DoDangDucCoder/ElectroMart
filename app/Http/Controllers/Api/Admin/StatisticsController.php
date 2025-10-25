<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Shared\Services\DatabaseOptimizationService;
use App\Application\Shared\Services\CacheService;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly DatabaseOptimizationService $dbOptimization,
        private readonly CacheService $cacheService
    ) {
    }

    public function index(): JsonResponse
    {
        $stats = $this->cacheService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function sales(): JsonResponse
    {
        $sales = \DB::table('orders')
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as orders_count,
                SUM(total) as total_revenue,
                AVG(total) as avg_order_value
            ')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    public function products(): JsonResponse
    {
        $products = \DB::table('products')
            ->selectRaw('
                COUNT(*) as total_products,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_products,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock_quantity <= stock_alert_threshold THEN 1 ELSE 0 END) as low_stock
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function optimizeDatabase(): JsonResponse
    {
        $results = $this->dbOptimization->optimizeTables();

        return response()->json([
            'success' => true,
            'message' => 'Database optimization completed',
            'data' => $results,
        ]);
    }

    public function addIndexes(): JsonResponse
    {
        $results = $this->dbOptimization->addMissingIndexes();

        return response()->json([
            'success' => true,
            'message' => 'Database indexes added',
            'data' => $results,
        ]);
    }

    public function slowQueries(): JsonResponse
    {
        $queries = $this->dbOptimization->getSlowQueries();

        return response()->json([
            'success' => true,
            'data' => $queries,
        ]);
    }
}
