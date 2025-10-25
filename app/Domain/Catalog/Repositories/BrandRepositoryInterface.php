<?php

namespace App\Domain\Catalog\Repositories;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BrandRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function all(array $filters = []): Collection;

    public function findById(int $id): ?Brand;

    public function findBySlug(string $slug, bool $onlyActive = false): ?Brand;

    public function create(array $data): Brand;

    public function update(Brand $brand, array $data): Brand;

    public function delete(Brand $brand): void;

    public function setStatus(Brand $brand, bool $isActive): Brand;
}
