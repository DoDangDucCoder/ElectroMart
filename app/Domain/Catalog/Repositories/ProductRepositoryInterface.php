<?php

namespace App\Domain\Catalog\Repositories;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findBySlug(string $slug): ?Product;

    public function findById(int $id): ?Product;

    public function paginateProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function paginateByCategorySlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function paginateByBrandSlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function relatedProducts(Product $product, int $limit = 6): Collection;

    public function latestProducts(int $limit = 6): Collection;

    public function incrementViews(Product $product): void;

    public function productsByTag(string $tag, int $limit = 12): Collection;

    public function productsByCategoryIds(array $ids, int $limit = 12): Collection;

    public function priceRangeByCategorySlug(string $slug): array;

    public function latestAfterId(?int $afterId, int $limit = 6): Collection;

    public function randomActivePaginate(int $perPage = 6): LengthAwarePaginator;

    public function searchActive(string $keyword, ?int $limit = null): Collection;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;

    public function setStatus(Product $product, bool $isActive): Product;
}
