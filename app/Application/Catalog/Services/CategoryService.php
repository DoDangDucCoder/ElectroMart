<?php

namespace App\Application\Catalog\Services;

use App\Application\Shared\Services\CacheService;
use App\Domain\Catalog\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->categories->paginate($filters, $perPage);
    }

    public function listAll(array $filters = []): Collection
    {
        return $this->categories->all($filters);
    }

    public function listActive(): Collection
    {
        return $this->categories->all(['is_active' => true]);
    }

    public function listRootActive(): Collection
    {
        return $this->categories->all([
            'parent_id' => null,
            'is_active' => true,
        ]);
    }

    public function getCategoryTree(): array
    {
        $categories = $this->categories->all(['is_active' => true]);

        $grouped = $categories->groupBy(static function ($category) {
            return $category->parent_id ?? 0;
        });

        $buildTree = function (int $parentId) use (&$buildTree, $grouped): array {
            return $grouped->get($parentId, collect())
                ->sortBy('display_order')
                ->map(function ($category) use ($buildTree) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'display_order' => $category->display_order,
                        'children' => $buildTree((int) $category->id),
                    ];
                })
                ->values()
                ->all();
        };

        return $buildTree(0);
    }

    public function create(array $data): ProductCategory
    {
        $payload = $this->preparePayload($data);

        $category = $this->categories->create($payload);
        $this->cache->invalidateCategoryCache($category->id);

        return $category;
    }

    public function update(ProductCategory $category, array $data): ProductCategory
    {
        $previousSlug = $category->slug;
        $payload = $this->preparePayload($data, $category);

        $updated = $this->categories->update($category, $payload);
        $this->cache->invalidateCategoryCache($updated->id, [
            'category_slugs' => [$previousSlug],
        ]);

        return $updated;
    }

    public function delete(ProductCategory $category): void
    {
        $categoryId = $category->id;
        $previousSlug = $category->slug;
        $this->categories->delete($category);
        $this->cache->invalidateCategoryCache($categoryId, [
            'category_slugs' => [$previousSlug],
        ]);
    }

    public function updateStatus(ProductCategory $category, bool $isActive): ProductCategory
    {
        $updated = $this->categories->setStatus($category, $isActive);
        $this->cache->invalidateCategoryCache($updated->id, [
            'category_slugs' => [$category->slug],
        ]);

        return $updated;
    }

    public function reorder(array $orderedIds): void
    {
        $this->categories->reorder($orderedIds);
        $this->cache->invalidateCategoryCache((int) ($orderedIds[0] ?? 0));
    }

    public function findActiveBySlug(string $slug): ?ProductCategory
    {
        return $this->categories->findBySlug($slug, true);
    }

    public function getDescendantIds(ProductCategory $category, bool $onlyActive = true): array
    {
        $filters = ['parent_id' => $category->id];

        if ($onlyActive) {
            $filters['is_active'] = true;
        }

        $children = $this->categories->all($filters);

        $ids = [];

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child, $onlyActive));
        }

        return $ids;
    }

    protected function preparePayload(array $data, ?ProductCategory $category = null): array
    {
        $payload = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? ($category?->slug ?? Str::slug($data['name'])),
            'code' => $data['code'] ?? $category?->code,
            'parent_id' => $data['parent_id'] ?? null,
            'thumbnail_path' => $data['thumbnail_path'] ?? $category?->thumbnail_path,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'display_order' => $data['display_order'] ?? $category?->display_order ?? 0,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta' => $data['meta'] ?? $category?->meta,
        ];

        return $payload;
    }
}
