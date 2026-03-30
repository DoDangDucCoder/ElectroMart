<?php

namespace App\Application\Reporting\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ProductProfitReportService
{
    public function getReport(array $filters = [], int $perPage = 20): array
    {
        $baseQuery = $this->baseQuery($filters);

        $rows = (clone $baseQuery)
            ->selectRaw('
                order_items.product_id,
                MAX(order_items.product_name) as product_name,
                MAX(products.slug) as product_slug,
                MAX(brands.name) as brand_name,
                MAX(product_categories.name) as category_name,
                SUM(order_items.quantity) as sold_quantity,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM(order_items.cost_price * order_items.quantity) as cost_total,
                SUM((order_items.price - order_items.cost_price) * order_items.quantity) as profit
            ')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('revenue')
            ->paginate($perPage)
            ->appends($filters);

        $summary = (clone $baseQuery)
            ->selectRaw('
                COUNT(DISTINCT order_items.product_id) as products_count,
                SUM(order_items.quantity) as sold_quantity,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM(order_items.cost_price * order_items.quantity) as cost_total,
                SUM((order_items.price - order_items.cost_price) * order_items.quantity) as profit
            ')
            ->first();

        return [
            'rows' => $rows,
            'summary' => [
                'products_count' => (int) ($summary->products_count ?? 0),
                'sold_quantity' => (int) ($summary->sold_quantity ?? 0),
                'revenue' => (float) ($summary->revenue ?? 0),
                'cost_total' => (float) ($summary->cost_total ?? 0),
                'profit' => (float) ($summary->profit ?? 0),
            ],
            'brands' => Brand::query()->orderBy('name')->get(),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ];
    }

    public function getApiReport(array $filters = [], int $perPage = 20): array
    {
        $report = $this->getReport($filters, $perPage);

        /** @var LengthAwarePaginator $rows */
        $rows = $report['rows'];

        return [
            'data' => collect($rows->items())->map(static function ($row) {
                return [
                    'product_id' => $row->product_id,
                    'product_name' => $row->product_name,
                    'product_slug' => $row->product_slug,
                    'brand_name' => $row->brand_name,
                    'category_name' => $row->category_name,
                    'sold_quantity' => (int) $row->sold_quantity,
                    'revenue' => (float) $row->revenue,
                    'cost_total' => (float) $row->cost_total,
                    'profit' => (float) $row->profit,
                ];
            })->values(),
            'summary' => $report['summary'],
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ];
    }

    protected function baseQuery(array $filters): Builder
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.primary_category_id')
            ->where('orders.status', 'completed');

        if (! empty($filters['date_from'])) {
            $query->whereDate(DB::raw('COALESCE(orders.completed_at, orders.created_at)'), '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate(DB::raw('COALESCE(orders.completed_at, orders.created_at)'), '<=', $filters['date_to']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('products.brand_id', (int) $filters['brand_id']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('products.primary_category_id', (int) $filters['category_id']);
        }

        return $query;
    }
}
