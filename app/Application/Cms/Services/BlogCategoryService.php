<?php

namespace App\Application\Cms\Services;

use App\Domain\Cms\Repositories\BlogCategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogCategoryService
{
    public function __construct(
        private readonly BlogCategoryRepositoryInterface $categories,
    ) {
    }

    public function listAll(array $filters = []): Collection
    {
        if ($this->isActiveOnlyFilter($filters)) {
            return Cache::remember('storefront.blog_categories.active', 3600, fn () => $this->categories->all($filters));
        }

        return $this->categories->all($filters);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->categories->paginate($filters, $perPage);
    }

    public function findById(int $id): ?BlogCategory
    {
        return $this->categories->findById($id);
    }

    public function findBySlug(string $slug, bool $onlyActive = false): ?BlogCategory
    {
        return $this->categories->findBySlug($slug, $onlyActive);
    }

    public function findActiveBySlug(string $slug): ?BlogCategory
    {
        return Cache::remember(
            'storefront.blog_category.slug.'.$slug.'.active.'.$this->cacheVersion(),
            3600,
            fn () => $this->categories->findBySlug($slug, true)
        );
    }

    public function create(array $data): BlogCategory
    {
        $category = $this->categories->create($this->preparePayload($data));
        $this->clearStorefrontCache();

        return $category;
    }

    public function update(BlogCategory $category, array $data): BlogCategory
    {
        $updated = $this->categories->update($category, $this->preparePayload($data, $category));
        $this->clearStorefrontCache();

        return $updated;
    }

    public function delete(BlogCategory $category): void
    {
        $this->categories->delete($category);
        $this->clearStorefrontCache();
    }

    public function updateStatus(BlogCategory $category, bool $isActive): BlogCategory
    {
        $updated = $this->categories->setStatus($category, $isActive);
        $this->clearStorefrontCache();

        return $updated;
    }

    protected function preparePayload(array $data, ?BlogCategory $category = null): array
    {
        return [
            'name' => $data['name'] ?? $category?->name,
            'slug' => $data['slug']
                ?? $category?->slug
                ?? Str::slug($data['name'] ?? Str::random(8)),
            'description' => $data['description'] ?? $category?->description,
            'is_active' => $data['is_active'] ?? $category?->is_active ?? true,
            'display_order' => $data['display_order'] ?? $category?->display_order ?? 0,
        ];
    }

    protected function isActiveOnlyFilter(array $filters): bool
    {
        return count($filters) === 1
            && array_key_exists('is_active', $filters)
            && (bool) $filters['is_active'] === true;
    }

    protected function clearStorefrontCache(): void
    {
        Cache::forget('storefront.blog_categories.active');
        Cache::forever('storefront.blog_cache_version', $this->cacheVersion() + 1);
    }

    protected function cacheVersion(): int
    {
        return (int) Cache::get('storefront.blog_cache_version', 1);
    }
}
