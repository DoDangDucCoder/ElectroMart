<?php

namespace App\Infrastructure\Persistence\Repositories\Catalog;

use App\Domain\Catalog\Repositories\ProductReviewRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentProductReviewRepository implements ProductReviewRepositoryInterface
{
    public function getApprovedTreeByProduct(int $productId): Collection
    {
        return ProductReview::with(['children' => function ($query) {
            $query->where('status', 'approved')
                ->orderByDesc(DB::raw('COALESCE(approved_at, created_at)'))
                ->orderByDesc('id');
        }])
            ->where('product_id', $productId)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->orderByDesc(DB::raw('COALESCE(approved_at, created_at)'))
            ->orderByDesc('id')
            ->get();
    }

    public function getAllByProduct(int $productId): Collection
    {
        return ProductReview::where('product_id', $productId)
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();
    }

    public function getRootComments(int $limit = 100): Collection
    {
        return ProductReview::with(['product'])
            ->whereNull('parent_id')
            ->orderByDesc(DB::raw('COALESCE(approved_at, created_at)'))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function find(int $id): ?ProductReview
    {
        return ProductReview::with(['product', 'parent'])->find($id);
    }

    public function getRepliesByParentIds(array $parentIds): Collection
    {
        if (empty($parentIds)) {
            return collect();
        }

        return ProductReview::whereIn('parent_id', $parentIds)
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): ProductReview
    {
        return ProductReview::create($data);
    }

    public function update(ProductReview $review, array $data): ProductReview
    {
        $review->fill($data);
        $review->save();

        return $review;
    }

    public function averageRating(int $productId): float
    {
        return (float) ProductReview::where('product_id', $productId)
            ->whereNotNull('rating')
            ->avg('rating');
    }
}
