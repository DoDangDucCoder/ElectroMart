<?php

namespace App\Application\Catalog\Services;

use App\Application\Shared\Services\CacheService;
use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BrandService
{
    public function __construct(
        private readonly BrandRepositoryInterface $brands,
        private readonly CacheService $cache
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->brands->paginate($filters, $perPage);
    }

    public function listAll(array $filters = []): Collection
    {
        return $this->brands->all($filters);
    }

    public function listActive(): Collection
    {
        return $this->brands->all(['is_active' => true]);
    }

    public function findById(int $id): ?Brand
    {
        return $this->brands->findById($id);
    }

    public function findBySlug(string $slug, bool $onlyActive = false): ?Brand
    {
        return $this->brands->findBySlug($slug, $onlyActive);
    }

    public function findActiveBySlug(string $slug): ?Brand
    {
        return $this->brands->findBySlug($slug, true);
    }

    public function create(array $data): Brand
    {
        $payload = $this->preparePayload($data);

        $brand = $this->brands->create($payload);
        $this->cache->invalidateBrandCache($brand->id);

        return $brand;
    }

    public function update(Brand $brand, array $data): Brand
    {
        $previousSlug = $brand->slug;
        $payload = $this->preparePayload($data, $brand);

        $updated = $this->brands->update($brand, $payload);
        $this->cache->invalidateBrandCache($updated->id, [
            'brand_slugs' => [$previousSlug],
        ]);

        return $updated;
    }

    public function delete(Brand $brand): void
    {
        $brandId = $brand->id;
        $previousSlug = $brand->slug;
        $this->brands->delete($brand);
        $this->cache->invalidateBrandCache($brandId, [
            'brand_slugs' => [$previousSlug],
        ]);
    }

    public function updateStatus(Brand $brand, bool $isActive): Brand
    {
        $updated = $this->brands->setStatus($brand, $isActive);
        $this->cache->invalidateBrandCache($updated->id);

        return $updated;
    }

    protected function preparePayload(array $data, ?Brand $brand = null): array
    {
        return [
            'name' => $data['name'] ?? $brand?->name,
            'slug' => $data['slug'] ?? $brand?->slug ?? Str::slug($data['name'] ?? $brand?->name ?? Str::random(8)),
            'code' => $data['code'] ?? $brand?->code,
            'description' => $data['description'] ?? $brand?->description,
            'logo_path' => $data['logo_path'] ?? $brand?->logo_path,
            'is_active' => $data['is_active'] ?? $brand?->is_active ?? true,
            'display_order' => $data['display_order'] ?? $brand?->display_order ?? 0,
            'meta' => $data['meta'] ?? $brand?->meta,
        ];
    }
}
