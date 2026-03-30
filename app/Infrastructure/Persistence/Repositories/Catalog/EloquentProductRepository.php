<?php

namespace App\Infrastructure\Persistence\Repositories\Catalog;

use App\Domain\Catalog\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findBySlug(string $slug): ?Product
    {
        return Product::with([
            'brand',
            'categories',
            'media' => fn ($query) => $query->orderBy('display_order')->orderBy('id'),
            'variants.attributeValues.attribute',
        ])
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?Product
    {
        return Product::with([
            'brand',
            'categories',
            'media' => fn ($query) => $query->orderBy('display_order')->orderBy('id'),
            'variants.attributeValues.attribute',
        ])->find($id);
    }

    public function paginateProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->applyFilters(Product::with(['brand', 'categories']), $filters);

        return $this->applySorting($query, $filters)->paginate($perPage);
    }

    public function paginateByCategorySlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->applyFilters(Product::with(['brand', 'categories']), $filters)
            ->whereHas('categories', fn (Builder $builder) => $builder->where('slug', $slug));

        return $this->applySorting($query, $filters)->paginate($perPage);
    }

    public function paginateByBrandSlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->applyFilters(Product::with(['brand', 'categories']), $filters)
            ->whereHas('brand', fn (Builder $builder) => $builder->where('slug', $slug));

        return $this->applySorting($query, $filters)->paginate($perPage);
    }

    public function relatedProducts(Product $product, int $limit = 6): Collection
    {
        return Product::with(['brand', 'categories'])
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->when($product->primary_category_id, function ($query, $categoryId) {
                $query->where('primary_category_id', $categoryId);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function latestProducts(int $limit = 6): Collection
    {
        return Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function incrementViews(Product $product): void
    {
        $product->increment('views_count');
    }

    public function productsByTag(string $tag, int $limit = 12): Collection
    {
        $normalized = trim(str_replace('-', ' ', $tag));

        return Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->where(function (Builder $query) use ($normalized) {
                $query->where('name', 'like', "%{$normalized}%")
                    ->orWhere('slug', 'like', "%{$normalized}%")
                    ->orWhere('meta->tags', 'like', "%{$normalized}%");
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function productsByCategoryIds(array $ids, int $limit = 12): Collection
    {
        if (empty($ids)) {
            return collect();
        }

        return Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->whereHas('categories', fn (Builder $builder) => $builder->whereIn('product_categories.id', $ids))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function priceRangeByCategorySlug(string $slug): array
    {
        $query = Product::where('is_active', true)
            ->whereHas('categories', fn (Builder $builder) => $builder->where('slug', $slug));

        $min = (float) (clone $query)->min('price');
        $max = (float) (clone $query)->max('price');

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    public function latestAfterId(?int $afterId, int $limit = 6): Collection
    {
        $query = Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->orderByDesc('id');

        if ($afterId) {
            $query->where('id', '<', $afterId);
        }

        return $query->limit($limit)->get();
    }

    public function randomActivePaginate(int $perPage = 6): LengthAwarePaginator
    {
        return Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->paginate($perPage);
    }

    public function searchActive(string $keyword, ?int $limit = null): Collection
    {
        $query = Product::with(['brand', 'categories'])
            ->where('is_active', true)
            ->where('name', 'like', "%{$keyword}%");

        $query->orderByDesc('id');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $onlyActive = $filters['only_active'] ?? true;

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meta->tags', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (! empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        if (! empty($filters['category_ids'])) {
            $categoryIds = (array) $filters['category_ids'];
            $query->whereHas('categories', fn (Builder $builder) => $builder->whereIn('id', $categoryIds));
        }

        if (! empty($filters['brand_ids'])) {
            $query->whereIn('brand_id', (array) $filters['brand_ids']);
        }

        return $query;
    }

    protected function applySorting(Builder $query, array $filters): Builder
    {
        return match ($filters['sort'] ?? null) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->orderByDesc('id'),
        };
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function setStatus(Product $product, bool $isActive): Product
    {
        $product->is_active = $isActive;
        $product->save();

        return $product;
    }
}
