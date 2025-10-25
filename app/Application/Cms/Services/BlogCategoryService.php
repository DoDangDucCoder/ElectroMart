<?php

namespace App\Application\Cms\Services;

use App\Domain\Cms\Repositories\BlogCategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlogCategoryService
{
    public function __construct(
        private readonly BlogCategoryRepositoryInterface $categories,
    ) {
    }

    public function listAll(array $filters = []): Collection
    {
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
        return $this->categories->findBySlug($slug, true);
    }

    public function create(array $data): BlogCategory
    {
        return $this->categories->create($this->preparePayload($data));
    }

    public function update(BlogCategory $category, array $data): BlogCategory
    {
        return $this->categories->update($category, $this->preparePayload($data, $category));
    }

    public function delete(BlogCategory $category): void
    {
        $this->categories->delete($category);
    }

    public function updateStatus(BlogCategory $category, bool $isActive): BlogCategory
    {
        return $this->categories->setStatus($category, $isActive);
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
}
