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

        $rows = (clone $baseQuery)
            ->selectRaw(implode(",\n", $this->rowSelect($costPriceExpression)))
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

    public function getPrintableReport(array $filters = []): array
    {
        $filters = $this->normalizeDateFilters($filters);
        $reportTablesReady = $this->reportTablesReady();
        $costPriceExpression = $this->costPriceExpression();

        if (! $reportTablesReady) {
            return [
                'rows' => collect(),
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

        $rows = (clone $baseQuery)
            ->selectRaw(implode(",\n", $this->rowSelect($costPriceExpression)))
            ->groupBy('order_items.product_id')
            ->orderByDesc('revenue')
            ->get();

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

        if ($this->canJoinProducts()) {
            $query->leftJoin('products', 'products.id', '=', 'order_items.product_id');

            if ($this->canJoinBrands()) {
                $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id');
            }

            if ($this->canJoinCategories()) {
                $query->leftJoin('product_categories', 'product_categories.id', '=', 'products.primary_category_id');
            }
        }

        $orderDateExpression = $this->orderDateExpression();

        if (! empty($filters['date_from'])) {
            if ($orderDateExpression) {
                $query->whereDate(DB::raw($orderDateExpression), '>=', $filters['date_from']);
            }
        }

        if (! empty($filters['date_to'])) {
            if ($orderDateExpression) {
                $query->whereDate(DB::raw($orderDateExpression), '<=', $filters['date_to']);
            }
        }

        if (! empty($filters['brand_id'])) {
            if ($this->productsHaveColumn('brand_id')) {
                $query->where('products.brand_id', (int) $filters['brand_id']);
            }
        }

        if (! empty($filters['category_id'])) {
            if ($this->productsHaveColumn('primary_category_id')) {
                $query->where('products.primary_category_id', (int) $filters['category_id']);
            }
        }

        return $query;
    }

    protected function rowSelect(string $costPriceExpression): array
    {
        return [
            '
                order_items.product_id,
                ' . $this->productNameExpression() . ' as product_name,
                SUM(order_items.quantity) as sold_quantity,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM((' . $costPriceExpression . ') * order_items.quantity) as cost_total,
                SUM((order_items.price - (' . $costPriceExpression . ')) * order_items.quantity) as profit
            ',
            $this->canSelectProductSlug() ? 'MAX(products.slug) as product_slug' : 'NULL as product_slug',
            $this->canJoinBrands() ? 'MAX(brands.name) as brand_name' : 'NULL as brand_name',
            $this->canJoinCategories() ? 'MAX(product_categories.name) as category_name' : 'NULL as category_name',
        ];
    }

    public function reportTablesReady(): bool
    {
        return Schema::hasTable('order_items')
            && Schema::hasTable('orders')
            && $this->ordersHaveRequiredColumns()
            && $this->orderItemsHaveRequiredColumns();
    }

    public function brandsTableReady(): bool
    {
        return Schema::hasTable('brands')
            && $this->schemaHasColumn('brands', 'id')
            && $this->schemaHasColumn('brands', 'name');
    }

    public function categoriesTableReady(): bool
    {
        return Schema::hasTable('product_categories')
            && $this->schemaHasColumn('product_categories', 'id')
            && $this->schemaHasColumn('product_categories', 'name');
    }

    public function productsTableReady(): bool
    {
        return Schema::hasTable('products');
    }

    protected function costPriceExpression(): string
    {
        if ($this->schemaHasColumn('order_items', 'cost_price')) {
            if ($this->canJoinProducts() && $this->productsHaveColumn('cost_price')) {
                return 'COALESCE(NULLIF(order_items.cost_price, 0), products.cost_price, 0)';
            }

            return 'COALESCE(order_items.cost_price, 0)';
        }

        if ($this->productsHaveColumn('cost_price')) {
            return 'COALESCE(products.cost_price, 0)';
        }

        return '0';
    }

    protected function orderDateExpression(): ?string
    {
        $columns = collect(['completed_at', 'placed_at', 'created_at'])
            ->filter(fn (string $column) => $this->schemaHasColumn('orders', $column))
            ->map(fn (string $column) => 'orders.' . $column)
            ->values();

        if ($columns->isEmpty()) {
            return null;
        }

        if ($columns->count() === 1) {
            return $columns->first();
        }

        return 'COALESCE(' . $columns->implode(', ') . ')';
    }

    protected function productNameExpression(): string
    {
        if ($this->canJoinProducts() && $this->productsHaveColumn('name') && $this->schemaHasColumn('order_items', 'product_name')) {
            return 'COALESCE(MAX(products.name), MAX(order_items.product_name))';
        }

        if ($this->canJoinProducts() && $this->productsHaveColumn('name')) {
            return 'MAX(products.name)';
        }

        if ($this->schemaHasColumn('order_items', 'product_name')) {
            return 'MAX(order_items.product_name)';
        }

        return 'NULL';
    }

    protected function canJoinProducts(): bool
    {
        return $this->productsTableReady()
            && $this->schemaHasColumn('products', 'id');
    }

    protected function canJoinBrands(): bool
    {
        return $this->canJoinProducts()
            && $this->brandsTableReady()
            && $this->productsHaveColumn('brand_id');
    }

    protected function canJoinCategories(): bool
    {
        return $this->canJoinProducts()
            && $this->categoriesTableReady()
            && $this->productsHaveColumn('primary_category_id');
    }

    protected function canSelectProductSlug(): bool
    {
        return $this->canJoinProducts()
            && $this->productsHaveColumn('slug');
    }

    protected function ordersHaveRequiredColumns(): bool
    {
        foreach (['id', 'status'] as $column) {
            if (! $this->schemaHasColumn('orders', $column)) {
                return false;
            }
        }

        return true;
    }

    protected function orderItemsHaveRequiredColumns(): bool
    {
        foreach (['order_id', 'product_id', 'quantity', 'price'] as $column) {
            if (! $this->schemaHasColumn('order_items', $column)) {
                return false;
            }
        }

        return true;
    }

    protected function productsHaveColumn(string $column): bool
    {
        return $this->productsTableReady()
            && $this->schemaHasColumn('products', $column);
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
