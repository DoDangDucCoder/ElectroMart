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
            ->withCompleteStorefrontMedia()
            ->with(['brand', 'categories', 'primaryCategory']);

        $this->applyLocalDemoImageSafety($query);

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

        if (! empty($filters['brand_id'])) {
            $query->where($this->productTable() . '.brand_id', (int) $filters['brand_id']);
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== null && $filters['price_min'] !== '') {
            $query->whereRaw(
                $this->effectivePriceExpression() . ' >= ?',
                [ProductPriceSql::normalizeFilterBinding($filters['price_min'])]
            );
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== null && $filters['price_max'] !== '') {
            $query->whereRaw(
                $this->effectivePriceExpression() . ' <= ?',
                [ProductPriceSql::normalizeFilterBinding($filters['price_max'])]
            );
        }

        return $query;
    }

    public function getSuggestions(string $query): Collection
    {
        $keyword = trim($query);

        if ($keyword === '') {
            return collect();
        }

        $query = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->where('is_active', true)
            ->withCompleteStorefrontMedia()
            ->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%')
                    ->orWhere('slug', 'like', '%' . $keyword . '%');
            })
            ->orderByRaw(
                'CASE WHEN sku = ? THEN 0 WHEN name LIKE ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END',
                [$keyword, $keyword . '%', '%' . $keyword . '%']
            )
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit(8);

        $this->applyLocalDemoImageSafety($query);

        return $query->get();
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
        $query->reorder();
        $this->applyHighlightOrdering($query, $filters);

        match ($filters['sort'] ?? null) {
            'best_selling' => $this->applyBestSellingSorting($query),
            'price_asc' => $query->orderByRaw($this->effectivePriceExpression() . ' ASC'),
            'price_desc' => $query->orderByRaw($this->effectivePriceExpression() . ' DESC'),
            'sale_desc' => $this->applySaleSorting($query),
            'name_asc' => $query->orderBy('name', 'ASC'),
            'name_desc' => $query->orderByDesc('name'),
            'created_at_desc' => $query->orderByDesc($this->productTable() . '.created_at')->orderByDesc($this->productTable() . '.id'),
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

    protected function applySaleSorting(Builder $query): void
    {
        $table = $this->productTable();
        $effectivePriceExpression = $this->effectivePriceExpression();

        $query
            ->orderByRaw(
                'CASE WHEN ' . $table . '.price > (' . $effectivePriceExpression . ') THEN ' . $table . '.price - (' . $effectivePriceExpression . ') ELSE 0 END DESC'
            )
            ->orderByRaw($effectivePriceExpression . ' ASC')
            ->orderByDesc($table . '.views_count')
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

    protected function applyLocalDemoImageSafety(Builder $query): Builder
    {
        if (! (bool) config('storefront.local_demo_sales_mode', false)) {
            return $query;
        }

        $table = $this->productTable();

        return $query
            ->whereNotNull($table . '.thumbnail_path')
            ->where($table . '.thumbnail_path', '!=', '')
            ->where(function (Builder $builder) use ($table) {
                $builder->where($table . '.thumbnail_path', 'like', 'assets/%')
                    ->orWhere($table . '.thumbnail_path', 'like', 'uploads/%');
            });
    }
}
