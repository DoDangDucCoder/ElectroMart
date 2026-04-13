<?php

namespace App\Application\Catalog\Services;

use App\Domain\Catalog\Repositories\ProductReviewRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProductReviewService
{
    public function __construct(
        private readonly ProductReviewRepositoryInterface $reviews
    ) {
    }

    public function getApprovedThread(int $productId): Collection
    {
        return $this->reviews->getApprovedTreeByProduct($productId);
    }

    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviews->paginateByProduct($productId, $perPage);
    }

    public function getAverageRating(int $productId): float
    {
        return round($this->reviews->averageRating($productId));
    }

    public function getAdminRootComments(int $limit = 100): Collection
    {
        return $this->reviews->getRootComments($limit);
    }

    public function getRepliesForParents(array $parentIds): Collection
    {
        return $this->reviews->getRepliesByParentIds($parentIds);
    }

    public function createReview(array $data): ProductReview
    {
        return $this->reviews->create([
            'customer_id' => $data['customer_id'] ?? null,
            'product_id' => $data['product_id'],
            'rating' => $data['rating'] ?? 5,
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'is_public' => $data['is_public'] ?? true,
            'approved_at' => $data['approved_at'] ?? null,
            'meta' => $data['meta'] ?? [
                'source' => 'api_review',
            ],
        ]);
    }

    public function findById(int $id): ?ProductReview
    {
        return $this->reviews->find($id);
    }

    public function updateReview(ProductReview $review, array $data): ProductReview
    {
        $payload = array_filter([
            'rating' => $data['rating'] ?? null,
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
        ], static fn ($value) => $value !== null);

        return $this->reviews->update($review, $payload);
    }

    public function deleteReview(ProductReview $review): void
    {
        $this->reviews->delete($review);
    }

    public function createCustomerComment(int $productId, string $author, string $content): ProductReview
    {
        return $this->reviews->create([
            'product_id' => $productId,
            'author_name' => $author,
            'author_email' => null,
            'content' => $content,
            'rating' => 0,
            'status' => 'pending',
            'is_public' => true,
            'approved_at' => null,
            'meta' => [
                'source' => 'frontend_comment',
            ],
        ]);
    }

    public function createRating(int $productId, int $rating): ProductReview
    {
        return $this->reviews->create([
            'product_id' => $productId,
            'author_name' => null,
            'author_email' => null,
            'content' => '',
            'rating' => max(1, min(5, $rating)),
            'status' => 'approved',
            'is_public' => false,
            'approved_at' => Carbon::now(),
            'meta' => [
                'source' => 'star_rating',
            ],
        ]);
    }

    public function updateStatus(int $reviewId, string $status): ?ProductReview
    {
        $review = $this->reviews->find($reviewId);

        if (! $review) {
            return null;
        }

        $review->status = $status;
        $review->approved_at = $status === 'approved' ? ($review->approved_at ?? Carbon::now()) : null;

        return $this->reviews->update($review, [
            'status' => $review->status,
            'approved_at' => $review->approved_at,
        ]);
    }

    public function replyAsAdmin(int $productId, int $parentId, string $content, string $adminName): ?ProductReview
    {
        $parent = $this->reviews->find($parentId);

        if (! $parent || $parent->product_id !== $productId) {
            return null;
        }

        return $this->reviews->create([
            'product_id' => $productId,
            'parent_id' => $parentId,
            'author_name' => $adminName,
            'author_email' => null,
            'content' => $content,
            'rating' => 0,
            'status' => 'approved',
            'is_public' => true,
            'approved_at' => Carbon::now(),
            'meta' => [
                'source' => 'admin_reply',
            ],
        ]);
    }
}
