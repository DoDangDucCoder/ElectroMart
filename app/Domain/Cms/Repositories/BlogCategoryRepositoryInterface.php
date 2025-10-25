<?php

namespace App\Domain\Cms\Repositories;

use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BlogCategoryRepositoryInterface
{
    public function all(array $filters = []): Collection;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug, bool $onlyActive = false): ?BlogCategory;

    public function findById(int $id): ?BlogCategory;

    public function create(array $data): BlogCategory;

    public function update(BlogCategory $category, array $data): BlogCategory;

    public function delete(BlogCategory $category): void;

    public function setStatus(BlogCategory $category, bool $isActive): BlogCategory;
}
