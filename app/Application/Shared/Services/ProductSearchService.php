<?php

namespace App\Application\Shared\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            $query->whereRaw($this->effectivePriceExpression() . ' >= ?', [(float) $filters['price_min']]);
        }

        if (! empty($filters['price_max'])) {
            $query->whereRaw($this->effectivePriceExpression() . ' <= ?', [(float) $filters['price_max']]);
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
            if ($driver === 'mysql' && mb_strlen($keyword) >= 3) {
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

    protected function applySorting(Builder $query, array $filters): void
    {
        match ($filters['sort'] ?? null) {
            'price_asc' => $query->orderByRaw($this->effectivePriceExpression() . ' asc'),
            'price_desc' => $query->orderByRaw($this->effectivePriceExpression() . ' desc'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'popular' => $query->orderByDesc('views_count')->orderByDesc('id'),
            default => $query->orderByDesc('is_featured')->orderByDesc('views_count')->orderByDesc('id'),
        };
    }

    protected function effectivePriceExpression(): string
    {
        return 'CAST(CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END AS REAL)';
    }
}
