<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Reporting\Services\ProductProfitReportService;
use App\Application\Reporting\Services\ProductDiscoveryReportService;
use App\Application\Shared\Services\DatabaseOptimizationService;
use App\Application\Shared\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly DatabaseOptimizationService $dbOptimization,
        private readonly CacheService $cacheService,
        private readonly ProductProfitReportService $productProfitReportService,
        private readonly ProductDiscoveryReportService $productDiscoveryReportService
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

    public function productsProfit(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
        ]);

        $perPage = min((int) $request->input('per_page', 20), 100);

        return response()->json([
            'success' => true,
            ...$this->productProfitReportService->getApiReport($filters, $perPage),
        ]);
    }

    public function productDiscovery(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $perPage = min((int) $request->input('per_page', 20), 100);

        return response()->json([
            'success' => true,
            ...$this->productDiscoveryReportService->getApiReport($filters, $perPage),
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
