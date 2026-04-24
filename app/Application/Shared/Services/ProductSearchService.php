<?php

namespace App\Application\Shared\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Support\ProductPriceSql;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSearchService
{
    public function search(string $query, array $filters = []): Collection
    {
        $filters['keyword'] = $query;

        return $this->runSearch($filters);
    }

    public function filter(Builder $query, array $filters): Builder
    {
        $query->where('is_active', true)
            ->with(['brand', 'categories', 'primaryCategory']);

        if (! empty($filters['keyword'])) {
            $this->applyKeywordFilter($query, (string) $filters['keyword']);
        }

        if (! empty($filters['product_code'])) {
            $productCode = trim((string) $filters['product_code']);
            $query->where('sku', 'like', '%' . $productCode . '%');
        }

        if (! empty($filters['category_id'])) {
            $categoryId = (int) $filters['category_id'];

            $query->where(function (Builder $builder) use ($categoryId) {
                $builder->where('primary_category_id', $categoryId)
                    ->orWhereHas('categories', function (Builder $relation) use ($categoryId) {
                        $relation->where('product_categories.id', $categoryId);
                    });
            });
        }

        if (! empty($filters['price_min'])) {
            $query->whereRaw(
                $this->effectivePriceExpression() . ' >= ?',
                [ProductPriceSql::normalizeFilterBinding($filters['price_min'])]
            );
        }

        if (! empty($filters['price_max'])) {
            $query->whereRaw(
                $this->effectivePriceExpression() . ' <= ?',
                [ProductPriceSql::normalizeFilterBinding($filters['price_max'])]
            );
        }

        return $query;
    }

    public function getSuggestions(string $query): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return collect();
        }

        return Product::query()
            ->with(['brand', 'primaryCategory'])
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($query) {
                $builder->where('name', 'like', '%' . $query . '%')
                    ->orWhere('sku', 'like', '%' . $query . '%')
                    ->orWhere('slug', 'like', '%' . $query . '%');
            })
            ->orderByRaw(
                'CASE WHEN sku = ? THEN 0 WHEN name LIKE ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END',
                [$query, $query . '%', '%' . $query . '%']
            )
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    public function searchCatalog(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->filter(Product::query(), $filters);

        $this->applySorting($query, $filters);

        return $query->paginate($perPage);
    }

    public function searchLimited(array $filters, int $limit = 12): Collection
    {
        $query = $this->filter(Product::query(), $filters);

        $this->applySorting($query, $filters);

        return $query->limit($limit)->get();
    }

    protected function runSearch(array $filters): Collection
    {
        return $this->searchLimited($filters, (int) ($filters['limit'] ?? 12));
    }

    protected function applyKeywordFilter(Builder $query, string $keyword): void
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return;
        }

        $driver = DB::connection()->getDriverName();

        $query->where(function (Builder $builder) use ($keyword, $driver) {
            if ($this->canUseMysqlFullText($keyword, $driver)) {
                $builder->whereFullText(['name', 'description'], $keyword)
                    ->orWhere('sku', 'like', '%' . $keyword . '%')
                    ->orWhere('slug', 'like', '%' . $keyword . '%')
                    ->orWhere('meta->tags', 'like', '%' . $keyword . '%');

                return;
            }

            $builder->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('sku', 'like', '%' . $keyword . '%')
                ->orWhere('slug', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')
                ->orWhere('meta->tags', 'like', '%' . $keyword . '%');
        });
    }

    protected function canUseMysqlFullText(string $keyword, ?string $driver = null): bool
    {
        if (($driver ?? DB::connection()->getDriverName()) !== 'mysql' || mb_strlen($keyword) < 3) {
            return false;
        }

        if (! Schema::hasTable('products')) {
            return false;
        }

        try {
            return Schema::hasIndex('products', ['name', 'description'], 'fulltext');
        } catch (\Throwable) {
            return false;
        }
    }

    protected function applySorting(Builder $query, array $filters): void
    {
        $this->applyHighlightOrdering($query, $filters);

        match ($filters['sort'] ?? null) {
            'best_selling' => $this->applyBestSellingSorting($query),
            'price_asc' => $query->orderByRaw($this->effectivePriceExpression() . ' asc'),
            'price_desc' => $query->orderByRaw($this->effectivePriceExpression() . ' desc'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'popular' => $query->orderByDesc('views_count')->orderByDesc('id'),
            default => $query->orderByDesc('is_featured')->orderByDesc('views_count')->orderByDesc('id'),
        };
    }

    protected function applyHighlightOrdering(Builder $query, array $filters): void
    {
        $highlightSlug = trim((string) ($filters['highlight_product_slug'] ?? $filters['highlight_product'] ?? ''));

        if ($highlightSlug === '') {
            return;
        }

        $table = $this->productTable();

        $query->orderByRaw(
            'CASE WHEN ' . $table . '.slug = ? THEN 1 ELSE 0 END DESC',
            [$highlightSlug]
        );
    }

    protected function applyBestSellingSorting(Builder $query): void
    {
        if (! $this->canUseBestSellingRanking()) {
            $query->orderByDesc('views_count')
                ->orderByDesc('is_featured')
                ->orderByDesc('id');

            return;
        }

        $table = $this->productTable();
        $salesRanking = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id, SUM(order_items.quantity) as sold_quantity');

        $query->leftJoinSub($salesRanking, 'product_sales_rank', function ($join) use ($table) {
            $join->on('product_sales_rank.product_id', '=', $table . '.id');
        });

        $query->select($table . '.*')
            ->selectRaw('COALESCE(product_sales_rank.sold_quantity, 0) as sold_quantity_rank')
            ->orderByRaw('COALESCE(product_sales_rank.sold_quantity, 0) DESC')
            ->orderByDesc($table . '.views_count')
            ->orderByDesc($table . '.is_featured')
            ->orderByDesc($table . '.id');
    }

    protected function effectivePriceExpression(): string
    {
        return ProductPriceSql::effectivePriceExpression();
    }

    protected function canUseBestSellingRanking(): bool
    {
        return Schema::hasTable('orders')
            && Schema::hasTable('order_items')
            && Schema::hasColumn('orders', 'status')
            && Schema::hasColumn('order_items', 'order_id')
            && Schema::hasColumn('order_items', 'product_id')
            && Schema::hasColumn('order_items', 'quantity');
    }

    protected function productTable(): string
    {
        return (new Product())->getTable();
    }
}
