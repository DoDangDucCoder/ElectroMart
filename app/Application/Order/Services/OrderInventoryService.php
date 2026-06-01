<?php

namespace App\Application\Order\Services;

use App\Application\Shared\Services\CacheService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderInventoryService
{
    public function __construct(
        private readonly CacheService $cacheService,
    ) {
    }

    public function reserve(Order $order, string $source = 'order_created'): void
    {
        if ($this->hasReservedInventory($order)) {
            return;
        }

        DB::transaction(function () use ($order, $source): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($this->hasReservedInventory($lockedOrder)) {
                return;
            }

            $items = $lockedOrder->items()->with(['product', 'variant'])->get();

            foreach ($items as $item) {
                $this->decrementStock($item);
            }

            $this->markInventory($lockedOrder, true, $source);
        });
    }

    public function release(Order $order, string $source = 'order_cancelled'): void
    {
        if (! $this->hasReservedInventory($order)) {
            return;
        }

        DB::transaction(function () use ($order, $source): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! $this->hasReservedInventory($lockedOrder)) {
                return;
            }

            $items = $lockedOrder->items()->with(['product', 'variant'])->get();

            foreach ($items as $item) {
                $this->incrementStock($item);
            }

            $this->markInventory($lockedOrder, false, $source);
        });
    }

    public function restoreUntracked(Order $order, string $source = 'legacy_order_cancelled'): void
    {
        DB::transaction(function () use ($order, $source): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($this->hasReservedInventory($lockedOrder)) {
                $this->release($lockedOrder, $source);

                return;
            }

            $items = $lockedOrder->items()->with(['product', 'variant'])->get();

            foreach ($items as $item) {
                $this->incrementStock($item);
            }

            $meta = $lockedOrder->meta ?? [];
            $meta['inventory'] = [
                'reserved' => false,
                'updated_at' => now()->toIso8601String(),
                'source' => $source,
                'legacy_untracked_restore' => true,
            ];

            $lockedOrder->meta = $meta;
            $lockedOrder->save();
        });
    }

    public function hasReservedInventory(Order $order): bool
    {
        return (bool) data_get($order->meta, 'inventory.reserved', false);
    }

    protected function decrementStock(OrderItem $item): void
    {
        $quantity = max(0, (int) $item->quantity);

        if ($quantity === 0) {
            return;
        }

        if ($item->product_variant_id) {
            $variant = ProductVariant::query()->lockForUpdate()->find($item->product_variant_id);

            if (! $variant) {
                throw new RuntimeException('Không tìm thấy phiên bản sản phẩm của đơn hàng.');
            }

            $availableQuantity = (int) ($variant->stock_quantity ?? 0);
            if ($quantity > $availableQuantity) {
                throw new RuntimeException(
                    "Phiên bản sản phẩm \"{$variant->name}\" không đủ tồn kho để đặt hàng."
                );
            }

            $variant->stock_quantity = $availableQuantity - $quantity;
            $variant->save();
            $this->cacheService->invalidateProductCache((int) $variant->product_id);

            return;
        }

        $product = Product::query()->lockForUpdate()->find($item->product_id);

        if (! $product) {
            throw new RuntimeException('Không tìm thấy sản phẩm của đơn hàng.');
        }

        $availableQuantity = (int) ($product->stock_quantity ?? 0);
        if ($quantity > $availableQuantity) {
            throw new RuntimeException(
                "Sản phẩm \"{$product->name}\" không đủ tồn kho để đặt hàng."
            );
        }

        $product->stock_quantity = $availableQuantity - $quantity;
        $product->save();
        $this->cacheService->invalidateProductCache((int) $product->id);
    }

    protected function incrementStock(OrderItem $item): void
    {
        $quantity = max(0, (int) $item->quantity);

        if ($quantity === 0) {
            return;
        }

        if ($item->product_variant_id) {
            $variant = ProductVariant::query()->lockForUpdate()->find($item->product_variant_id);

            if ($variant) {
                $variant->stock_quantity = (int) ($variant->stock_quantity ?? 0) + $quantity;
                $variant->save();
                $this->cacheService->invalidateProductCache((int) $variant->product_id);
            }

            return;
        }

        $product = Product::query()->lockForUpdate()->find($item->product_id);

        if ($product) {
            $product->stock_quantity = (int) ($product->stock_quantity ?? 0) + $quantity;
            $product->save();
            $this->cacheService->invalidateProductCache((int) $product->id);
        }
    }

    protected function markInventory(Order $order, bool $reserved, string $source): void
    {
        $meta = $order->meta ?? [];
        $meta['inventory'] = [
            'reserved' => $reserved,
            'updated_at' => now()->toIso8601String(),
            'source' => $source,
        ];

        $order->meta = $meta;
        $order->save();
    }
}
