<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Reporting\Services\ProductProfitReportService;
use App\Application\Reporting\Services\ProductDiscoveryReportService;
use App\Application\Shared\Services\DatabaseOptimizationService;
use App\Application\Shared\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

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
        if (! Schema::hasTable('orders')) {
            return response()->json([
                'success' => true,
                'data' => [],
                'tables_ready' => false,
            ]);
        }

        $sales = \DB::table('orders')
            ->selectRaw('
                DATE(COALESCE(completed_at, placed_at, created_at)) as date,
                COUNT(*) as orders_count,
                SUM(total) as total_revenue,
                AVG(total) as avg_order_value
            ')
            ->where('status', 'completed')
            ->whereRaw('DATE(COALESCE(completed_at, placed_at, created_at)) >= ?', [now()->subDays(30)->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales,
            'tables_ready' => true,
        ]);
    }

    public function products(): JsonResponse
    {
        if (! Schema::hasTable('products')) {
            return response()->json([
                'success' => true,
                'data' => (object) [
                    'total_products' => 0,
                    'active_products' => 0,
                    'featured_products' => 0,
                    'out_of_stock' => 0,
                    'low_stock' => 0,
                ],
                'tables_ready' => false,
            ]);
        }

        $products = \DB::table('products')
            ->selectRaw('
                COUNT(*) as total_products,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_products,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= stock_alert_threshold THEN 1 ELSE 0 END) as low_stock
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $products,
            'tables_ready' => true,
        ]);
    }

    public function productsProfit(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'brand_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('brands') ? 'exists:brands,id' : null,
            ])),
            'category_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('product_categories') ? 'exists:product_categories,id' : null,
            ])),
        ]);

        $perPage = max(1, min((int) $request->input('per_page', 20), 100));

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

        $perPage = max(1, min((int) $request->input('per_page', 20), 100));

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
