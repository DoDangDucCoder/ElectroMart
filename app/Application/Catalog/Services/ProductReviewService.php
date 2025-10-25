<?php

namespace App\Application\Catalog\Services;

use App\Domain\Catalog\Repositories\ProductReviewRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
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

    public function createCustomerComment(int $productId, string $author, string $content): ProductReview
    {
        return $this->reviews->create([
            'product_id' => $productId,
            'author_name' => $author,
            'author_email' => null,
            'content' => $content,
            'rating' => null,
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
            'rating' => null,
            'status' => 'approved',
            'is_public' => true,
            'approved_at' => Carbon::now(),
            'meta' => [
                'source' => 'admin_reply',
            ],
        ]);
    }
}
