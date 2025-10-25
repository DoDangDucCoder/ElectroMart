<?php

namespace App\Infrastructure\Persistence\Repositories\Cms;

use App\Domain\Cms\Repositories\BlogCategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentBlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(BlogCategory::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters(BlogCategory::query(), $filters)
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug, bool $onlyActive = false): ?BlogCategory
    {
        return BlogCategory::query()
            ->when($onlyActive, fn (Builder $query) => $query->where('is_active', true))
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?BlogCategory
    {
        return BlogCategory::find($id);
    }

    public function create(array $data): BlogCategory
    {
        return BlogCategory::create($data);
    }

    public function update(BlogCategory $category, array $data): BlogCategory
    {
        $category->fill($data);
        $category->save();

        return $category;
    }

    public function delete(BlogCategory $category): void
    {
        $category->delete();
    }

    public function setStatus(BlogCategory $category, bool $isActive): BlogCategory
    {
        $category->is_active = $isActive;
        $category->save();

        return $category;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['ids'])) {
            $query->whereIn('id', (array) $filters['ids']);
        }

        return $query;
    }
}
