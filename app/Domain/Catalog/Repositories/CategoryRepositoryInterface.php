<?php

namespace App\Domain\Catalog\Repositories;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function all(array $filters = []): Collection;

    public function create(array $data): ProductCategory;

    public function update(ProductCategory $category, array $data): ProductCategory;

    public function delete(ProductCategory $category): void;

    public function setStatus(ProductCategory $category, bool $isActive): ProductCategory;

    public function reorder(array $orderedIds): void;

    public function findBySlug(string $slug, bool $onlyActive = false): ?ProductCategory;
}
