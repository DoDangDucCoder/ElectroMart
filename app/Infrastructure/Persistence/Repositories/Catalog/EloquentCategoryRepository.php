<?php

namespace App\Infrastructure\Persistence\Repositories\Catalog;

use App\Domain\Catalog\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters(ProductCategory::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(ProductCategory::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();
    }

    public function findById(int $id): ?ProductCategory
    {
        return ProductCategory::find($id);
    }

    public function create(array $data): ProductCategory
    {
        return ProductCategory::create($data);
    }

    public function update(ProductCategory $category, array $data): ProductCategory
    {
        $category->fill($data);
        $category->save();

        return $category;
    }

    public function delete(ProductCategory $category): void
    {
        $category->delete();
    }

    public function setStatus(ProductCategory $category, bool $isActive): ProductCategory
    {
        $category->is_active = $isActive;
        $category->save();

        return $category;
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $position => $id) {
            ProductCategory::whereKey($id)->update(['display_order' => $position]);
        }
    }

    public function findBySlug(string $slug, bool $onlyActive = false): ?ProductCategory
    {
        $query = ProductCategory::where('slug', $slug);

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->first();
    }

    protected function applyFilters($query, array $filters)
    {
        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query;
    }
}
