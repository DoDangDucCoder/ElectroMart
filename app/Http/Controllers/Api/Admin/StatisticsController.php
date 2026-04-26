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
        $dateExpression = $this->salesDateExpression();

        if (! $this->salesTableReady($dateExpression)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'tables_ready' => false,
            ]);
        }

        $sales = \DB::table('orders')
            ->selectRaw('
                DATE(' . $dateExpression . ') as date,
                COUNT(*) as orders_count,
                SUM(total) as total_revenue,
                AVG(total) as avg_order_value
            ')
            ->where('status', 'completed')
            ->whereRaw('DATE(' . $dateExpression . ') >= ?', [now()->subDays(30)->toDateString()])
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

        $activeExpression = $this->schemaHasColumn('products', 'is_active')
            ? 'SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END)'
            : '0';
        $featuredExpression = $this->schemaHasColumn('products', 'is_featured')
            ? 'SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END)'
            : '0';
        $outOfStockExpression = $this->schemaHasColumn('products', 'stock_quantity')
            ? 'SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END)'
            : '0';
        $lowStockExpression = $this->schemaHasColumn('products', 'stock_quantity') && $this->schemaHasColumn('products', 'stock_alert_threshold')
            ? 'SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= stock_alert_threshold THEN 1 ELSE 0 END)'
            : '0';

        $products = \DB::table('products')
            ->selectRaw("
                COUNT(*) as total_products,
                {$activeExpression} as active_products,
                {$featuredExpression} as featured_products,
                {$outOfStockExpression} as out_of_stock,
                {$lowStockExpression} as low_stock
            ")
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
            'message' => 'Tối ưu cơ sở dữ liệu đã hoàn tất.',
            'data' => $results,
        ]);
    }

    public function addIndexes(): JsonResponse
    {
        $results = $this->dbOptimization->addMissingIndexes();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm chỉ mục cơ sở dữ liệu.',
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

    protected function salesDateExpression(): ?string
    {
        $columns = collect(['completed_at', 'placed_at', 'created_at'])
            ->filter(fn (string $column) => $this->schemaHasColumn('orders', $column))
            ->values();

        if ($columns->isEmpty()) {
            return null;
        }

        if ($columns->count() === 1) {
            return $columns->first();
        }

        return 'COALESCE(' . $columns->implode(', ') . ')';
    }

    protected function salesTableReady(?string $dateExpression): bool
    {
        return Schema::hasTable('orders')
            && $this->schemaHasColumn('orders', 'status')
            && $this->schemaHasColumn('orders', 'total')
            && $dateExpression !== null;
    }

    protected function schemaHasColumn(string $table, string $column): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }
}
