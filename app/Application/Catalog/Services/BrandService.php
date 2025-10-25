<?php

namespace App\Application\Catalog\Services;

use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BrandService
{
    public function __construct(
        private readonly BrandRepositoryInterface $brands
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

        return $this->brands->create($payload);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $payload = $this->preparePayload($data, $brand);

        return $this->brands->update($brand, $payload);
    }

    public function delete(Brand $brand): void
    {
        $this->brands->delete($brand);
    }

    public function updateStatus(Brand $brand, bool $isActive): Brand
    {
        return $this->brands->setStatus($brand, $isActive);
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
