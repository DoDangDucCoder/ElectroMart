<?php

namespace App\Application\Catalog\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductRecommendationImpression;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductSearchQuery;
use Illuminate\Support\Collection;

class ProductDiscoveryTrackingService
{
    public function trackSearch(array $filters, int $resultsCount, ?int $customerId = null, ?string $ipAddress = null, string $source = 'web'): void
    {
        if ($this->isEmptySearch($filters)) {
            return;
        }

        ProductSearchQuery::create([
            'customer_id' => $customerId,
            'category_id' => $filters['category_id'] ?? null,
            'keyword' => $filters['keyword'] ?? null,
            'product_code' => $filters['product_code'] ?? null,
            'price_min' => $filters['price_min'] ?? null,
            'price_max' => $filters['price_max'] ?? null,
            'sort' => $filters['sort'] ?? null,
            'results_count' => max(0, $resultsCount),
            'source' => $source,
            'ip_address' => $ipAddress,
            'searched_at' => now(),
        ]);
    }

    public function trackRecommendationImpressions(
        Collection $products,
        ?int $customerId = null,
        ?string $ipAddress = null,
        string $context = 'storefront',
        array $sources = []
    ): void {
        if ($products->isEmpty()) {
            return;
        }

        $sourceLabel = empty($sources) ? null : implode(', ', $sources);

        $products->each(function (Product $product) use ($customerId, $ipAddress, $context, $sourceLabel) {
            $duplicateQuery = ProductRecommendationImpression::query()
                ->where('product_id', $product->id)
                ->where('context', $context)
                ->where('impressed_at', '>=', now()->subMinutes(15));

            if ($customerId) {
                $duplicateQuery->where('customer_id', $customerId);
            } else {
                $duplicateQuery->whereNull('customer_id')
                    ->where('ip_address', $ipAddress);
            }

            if ($duplicateQuery->exists()) {
                return;
            }

            ProductRecommendationImpression::create([
                'product_id' => $product->id,
                'customer_id' => $customerId,
                'context' => $context,
                'source_label' => $sourceLabel,
                'ip_address' => $ipAddress,
                'impressed_at' => now(),
            ]);
        });
    }

    protected function isEmptySearch(array $filters): bool
    {
        foreach (['keyword', 'product_code', 'category_id', 'price_min', 'price_max'] as $key) {
            if (($filters[$key] ?? null) !== null && ($filters[$key] ?? '') !== '') {
                return false;
            }
        }

        return true;
    }
}
