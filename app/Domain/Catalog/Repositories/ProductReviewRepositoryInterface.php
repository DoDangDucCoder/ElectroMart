<?php

namespace App\Domain\Catalog\Repositories;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Support\Collection;

interface ProductReviewRepositoryInterface
{
    public function getApprovedTreeByProduct(int $productId): Collection;

    public function getAllByProduct(int $productId): Collection;

    public function getRootComments(int $limit = 100): Collection;

    public function find(int $id): ?ProductReview;

    public function create(array $data): ProductReview;

    public function update(ProductReview $review, array $data): ProductReview;

    public function getRepliesByParentIds(array $parentIds): Collection;

    public function averageRating(int $productId): float;
}
