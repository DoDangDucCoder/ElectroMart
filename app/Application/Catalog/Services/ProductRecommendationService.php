<?php

namespace App\Application\Catalog\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductView;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRecommendationService
{
    public function recommendForStorefront(
        ?int $customerId = null,
        ?string $ipAddress = null,
        ?int $excludeProductId = null,
        int $limit = 6
    ): array {
        $items = collect();
        $sources = [];
        $excluded = collect([$excludeProductId])->filter()->map(fn ($id) => (int) $id)->values()->all();

        if ($customerId) {
            $ordered = $this->fromOrderHistory($customerId, $excluded, $limit);

            if ($ordered->isNotEmpty()) {
                $items = $items->merge($ordered)->unique('id')->values();
                $sources[] = 'Lịch sử đặt hàng';
                $excluded = array_merge($excluded, $ordered->pluck('id')->all());
            }
        }

        if ($items->count() < $limit) {
            $viewed = $this->fromViewHistory($customerId, $ipAddress, $excluded, $limit - $items->count());

            if ($viewed->isNotEmpty()) {
                $items = $items->merge($viewed)->unique('id')->values();
                $sources[] = 'Lịch sử xem sản phẩm';
                $excluded = array_merge($excluded, $viewed->pluck('id')->all());
            }
        }

        if ($items->count() < $limit) {
            $trending = $this->trending($excluded, $limit - $items->count());

            if ($trending->isNotEmpty()) {
                $items = $items->merge($trending)->unique('id')->values();
                $sources[] = 'Sản phẩm được quan tâm nhiều';
            }
        }

        return [
            'products' => $items->take($limit)->values(),
            'sources' => $sources,
            'description' => $this->buildDescription($customerId, $sources),
        ];
    }

    protected function fromOrderHistory(int $customerId, array $excludeIds, int $limit): Collection
    {
        $purchasedProductIds = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('MAX(order_items.created_at) as last_ordered_at'))
            ->whereNotNull('product_id')
            ->whereHas('order', function (Builder $query) use ($customerId) {
                $query->where('customer_id', $customerId)
                    ->whereNotIn('status', ['cancelled', 'refunded']);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->orderByDesc('last_ordered_at')
            ->limit(12)
            ->get();

        if ($purchasedProductIds->isEmpty()) {
            return collect();
        }

        $purchasedIds = $purchasedProductIds->pluck('product_id')->map(fn ($id) => (int) $id)->all();
        $purchasedProducts = Product::query()
            ->with(['categories'])
            ->whereIn('id', $purchasedIds)
            ->get();

        $categoryIds = $purchasedProducts
            ->flatMap(fn (Product $product) => $this->categoryIdsForProduct($product))
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $brandIds = $purchasedProducts
            ->pluck('brand_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return $this->candidateQuery(array_merge($excludeIds, $purchasedIds))
            ->where(function (Builder $query) use ($categoryIds, $brandIds) {
                $hasCategoryConstraint = false;

                if (! empty($categoryIds)) {
                    $query->where(function (Builder $categoryQuery) use ($categoryIds) {
                        $categoryQuery->whereHas('categories', function (Builder $relation) use ($categoryIds) {
                            $relation->whereIn('product_categories.id', $categoryIds);
                        })->orWhereIn('primary_category_id', $categoryIds);
                    });

                    $hasCategoryConstraint = true;
                }

                if (! empty($brandIds)) {
                    if ($hasCategoryConstraint) {
                        $query->orWhereIn('brand_id', $brandIds);
                    } else {
                        $query->whereIn('brand_id', $brandIds);
                    }
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit(max($limit, 1))
            ->get();
    }

    protected function fromViewHistory(?int $customerId, ?string $ipAddress, array $excludeIds, int $limit): Collection
    {
        $history = ProductView::query()
            ->select('product_id', DB::raw('COUNT(*) as view_count'), DB::raw('MAX(viewed_at) as last_viewed_at'))
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            }, function ($query) use ($ipAddress) {
                $query->where('ip_address', $ipAddress);
            })
            ->groupBy('product_id')
            ->orderByDesc('view_count')
            ->orderByDesc('last_viewed_at')
            ->limit(12)
            ->get();

        if ($history->isEmpty()) {
            return collect();
        }

        $viewedIds = $history->pluck('product_id')->map(fn ($id) => (int) $id)->all();

        $directMatches = $this->candidateQuery($excludeIds)
            ->whereIn('id', $viewedIds)
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($directMatches->count() >= $limit) {
            return $directMatches;
        }

        $viewedProducts = Product::query()
            ->with(['categories'])
            ->whereIn('id', $viewedIds)
            ->get();

        $categoryIds = $viewedProducts
            ->flatMap(fn (Product $product) => $this->categoryIdsForProduct($product))
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $related = $this->candidateQuery(array_merge($excludeIds, $directMatches->pluck('id')->all()))
            ->when(! empty($categoryIds), function (Builder $query) use ($categoryIds) {
                $query->where(function (Builder $categoryQuery) use ($categoryIds) {
                    $categoryQuery->whereHas('categories', function (Builder $relation) use ($categoryIds) {
                        $relation->whereIn('product_categories.id', $categoryIds);
                    })->orWhereIn('primary_category_id', $categoryIds);
                });
            })
            ->orderByDesc('views_count')
            ->orderByDesc('id')
            ->limit(max($limit - $directMatches->count(), 1))
            ->get();

        return $directMatches->merge($related)->unique('id')->values();
    }

    protected function trending(array $excludeIds, int $limit): Collection
    {
        $trendingIds = ProductView::query()
            ->select('product_id', DB::raw('COUNT(*) as recent_views'))
            ->where('viewed_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->orderByDesc('recent_views')
            ->limit(20)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $query = $this->candidateQuery($excludeIds);

        if (! empty($trendingIds)) {
            return $query->whereIn('id', $trendingIds)
                ->orderByDesc('views_count')
                ->orderByDesc('id')
                ->limit(max($limit, 1))
                ->get();
        }

        return $query->orderByDesc('views_count')
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->limit(max($limit, 1))
            ->get();
    }

    protected function candidateQuery(array $excludeIds): Builder
    {
        return Product::query()
            ->with(['brand', 'categories', 'primaryCategory'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->when(! empty($excludeIds), function (Builder $query) use ($excludeIds) {
                $query->whereNotIn('id', array_values(array_unique(array_map('intval', $excludeIds))));
            });
    }

    protected function buildDescription(?int $customerId, array $sources): string
    {
        if (empty($sources)) {
            return 'Danh sách này đang chờ thêm dữ liệu mua hàng hoặc lượt xem để gợi ý chính xác hơn.';
        }

        if ($customerId) {
            return 'Ưu tiên gợi ý từ lịch sử đặt hàng, sau đó bổ sung từ lịch sử xem và mức độ quan tâm hiện tại trong cửa hàng.';
        }

        return 'Bạn chưa có lịch sử mua hàng, nên hệ thống ưu tiên lượt xem gần đây và sản phẩm đang được quan tâm nhiều.';
    }

    protected function categoryIdsForProduct(Product $product): array
    {
        return $product->categories
            ->pluck('id')
            ->push($product->primary_category_id)
            ->filter()
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
