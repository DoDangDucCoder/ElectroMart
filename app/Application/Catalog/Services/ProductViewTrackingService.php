<?php

namespace App\Application\Catalog\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductView;

class ProductViewTrackingService
{
    public function track(
        Product $product,
        ?int $customerId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        if (! $customerId && ! $ipAddress) {
            return;
        }

        $recentView = ProductView::query()
            ->where('product_id', $product->id)
            ->when($customerId || $ipAddress, function ($query) use ($customerId, $ipAddress) {
                $query->where(function ($match) use ($customerId, $ipAddress) {
                    if ($customerId) {
                        $match->orWhere('customer_id', $customerId);
                    }

                    if ($ipAddress) {
                        $match->orWhere('ip_address', $ipAddress);
                    }
                });
            }, function ($query) use ($ipAddress) {
                $query->where('ip_address', $ipAddress);
            })
            ->where('viewed_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($recentView) {
            return;
        }

        ProductView::create([
            'product_id' => $product->id,
            'customer_id' => $customerId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
            'viewed_at' => now(),
        ]);

        $product->increment('views_count');
    }
}
