<?php

namespace App\Application\Reporting\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ProductProfitReportService
{
    public function getReport(array $filters = [], int $perPage = 20): array
    {
        $filters = $this->normalizeDateFilters($filters);
        $reportTablesReady = $this->reportTablesReady();
        $costPriceExpression = $this->costPriceExpression();

        if (! $reportTablesReady) {
            return [
                'rows' => $this->emptyPaginator($perPage)->appends($filters),
                'summary' => [
                    'products_count' => 0,
                    'sold_quantity' => 0,
                    'revenue' => 0.0,
                    'cost_total' => 0.0,
                    'profit' => 0.0,
                    'tables_ready' => false,
                ],
                'brands' => $this->brandsTableReady() ? Brand::query()->orderBy('name')->get() : collect(),
                'categories' => $this->categoriesTableReady() ? ProductCategory::query()->orderBy('name')->get() : collect(),
            ];
        }

        $baseQuery = $this->baseQuery($filters);

        $rowSelect = [
            '
                order_items.product_id,
                ' . ($this->productsTableReady()
                    ? 'COALESCE(MAX(products.name), MAX(order_items.product_name))'
                    : 'MAX(order_items.product_name)') . ' as product_name,
                SUM(order_items.quantity) as sold_quantity,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM((' . $costPriceExpression . ') * order_items.quantity) as cost_total,
                SUM((order_items.price - (' . $costPriceExpression . ')) * order_items.quantity) as profit
            ',
            $this->productsTableReady() ? 'MAX(products.slug) as product_slug' : 'NULL as product_slug',
            $this->productsTableReady() && $this->brandsTableReady() ? 'MAX(brands.name) as brand_name' : 'NULL as brand_name',
            $this->productsTableReady() && $this->categoriesTableReady() ? 'MAX(product_categories.name) as category_name' : 'NULL as category_name',
        ];

        $rows = (clone $baseQuery)
            ->selectRaw(implode(",\n", $rowSelect))
            ->groupBy('order_items.product_id')
            ->orderByDesc('revenue')
            ->paginate($perPage)
            ->appends($filters);

        $summary = (clone $baseQuery)
            ->selectRaw('
                COUNT(DISTINCT order_items.product_id) as products_count,
                SUM(order_items.quantity) as sold_quantity,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM((' . $costPriceExpression . ') * order_items.quantity) as cost_total,
                SUM((order_items.price - (' . $costPriceExpression . ')) * order_items.quantity) as profit
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
                'tables_ready' => true,
            ],
            'brands' => $this->brandsTableReady() ? Brand::query()->orderBy('name')->get() : collect(),
            'categories' => $this->categoriesTableReady() ? ProductCategory::query()->orderBy('name')->get() : collect(),
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
            'tables_ready' => (bool) ($report['summary']['tables_ready'] ?? false),
        ];
    }

    protected function baseQuery(array $filters): Builder
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed');

        if ($this->productsTableReady()) {
            $query->leftJoin('products', 'products.id', '=', 'order_items.product_id');

            if ($this->brandsTableReady()) {
                $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id');
            }

            if ($this->categoriesTableReady()) {
                $query->leftJoin('product_categories', 'product_categories.id', '=', 'products.primary_category_id');
            }
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate(DB::raw('COALESCE(orders.completed_at, orders.created_at)'), '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate(DB::raw('COALESCE(orders.completed_at, orders.created_at)'), '<=', $filters['date_to']);
        }

        if (! empty($filters['brand_id'])) {
            if ($this->productsTableReady()) {
                $query->where('products.brand_id', (int) $filters['brand_id']);
            }
        }

        if (! empty($filters['category_id'])) {
            if ($this->productsTableReady()) {
                $query->where('products.primary_category_id', (int) $filters['category_id']);
            }
        }

        return $query;
    }

    public function reportTablesReady(): bool
    {
        return Schema::hasTable('order_items') && Schema::hasTable('orders');
    }

    public function brandsTableReady(): bool
    {
        return Schema::hasTable('brands');
    }

    public function categoriesTableReady(): bool
    {
        return Schema::hasTable('product_categories');
    }

    public function productsTableReady(): bool
    {
        return Schema::hasTable('products');
    }

    protected function costPriceExpression(): string
    {
        if (Schema::hasColumn('order_items', 'cost_price')) {
            return 'order_items.cost_price';
        }

        if ($this->productsTableReady() && Schema::hasColumn('products', 'cost_price')) {
            return 'COALESCE(products.cost_price, 0)';
        }

        return '0';
    }

    protected function emptyPaginator(int $perPage): Paginator
    {
        return new Paginator(
            items: collect(),
            total: 0,
            perPage: $perPage,
            currentPage: Paginator::resolveCurrentPage(),
            options: [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    protected function normalizeDateFilters(array $filters): array
    {
        foreach (['date_from', 'date_to'] as $key) {
            if (empty($filters[$key])) {
                continue;
            }

            try {
                $filters[$key] = Carbon::parse($filters[$key])->toDateString();
            } catch (\Throwable) {
                unset($filters[$key]);
            }
        }

        if (
            ! empty($filters['date_from'])
            && ! empty($filters['date_to'])
            && $filters['date_from'] > $filters['date_to']
        ) {
            [$filters['date_from'], $filters['date_to']] = [$filters['date_to'], $filters['date_from']];
        }

        return $filters;
    }
}
