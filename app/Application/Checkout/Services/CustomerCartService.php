<?php

namespace App\Application\Checkout\Services;

use App\Application\Catalog\Services\ProductPricingService;
use App\Infrastructure\Persistence\Eloquent\Checkout\Cart;
use App\Infrastructure\Persistence\Eloquent\Checkout\CartItem;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use Illuminate\Support\Collection;
use RuntimeException;

class CustomerCartService
{
    public function __construct(
        private readonly ProductPricingService $pricingService
    ) {
    }

    public function getCart(int $customerId): Cart
    {
        $cart = Cart::firstOrCreate(
            ['customer_id' => $customerId],
            [
                'subtotal' => 0,
                'discount_amount' => 0,
                'shipping_fee' => 0,
                'total' => 0,
                'items_count' => 0,
            ]
        );

        return $this->loadCart($cart);
    }

    public function addItem(int $customerId, int $productId, int $quantity = 1, ?int $variantId = null): Cart
    {
        $cart = $this->getCart($customerId);

        $product = Product::find($productId);
        if (! $product) {
            throw new RuntimeException('Không tìm thấy sản phẩm.');
        }

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)->find($variantId);
            if (! $variant) {
                throw new RuntimeException('Không tìm thấy biến thể sản phẩm.');
            }
        }

        $item = $cart->items()
            ->where('product_id', $productId)
            ->when($variantId, fn ($query) => $query->where('product_variant_id', $variantId))
            ->when(! $variantId, fn ($query) => $query->whereNull('product_variant_id'))
            ->first();

        $pricingMeta = $this->buildPricingMeta($product, $variant);
        $price = (float) ($pricingMeta['final_price'] ?? 0);
        $quantity = max(1, $quantity);

        if ($item) {
            $item->quantity += $quantity;
            $item->price = $price;
            $item->subtotal = $item->price * $item->quantity;
            $item->meta = $pricingMeta;
            $item->save();
        } else {
            $item = $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'meta' => $pricingMeta,
            ]);
        }

        $this->recalculateCart($cart);

        return $this->loadCart($cart);
    }

    public function updateItem(int $customerId, int $itemId, int $quantity): Cart
    {
        $cart = $this->getCart($customerId);
        $item = $cart->items()->whereKey($itemId)->first();

        if (! $item) {
            throw new RuntimeException('Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->quantity = $quantity;
            $item->subtotal = $item->price * $item->quantity;
            $item->save();
        }

        $this->recalculateCart($cart);

        return $this->loadCart($cart);
    }

    public function removeItem(int $customerId, int $itemId): Cart
    {
        $cart = $this->getCart($customerId);
        $cart->items()->whereKey($itemId)->delete();

        $this->recalculateCart($cart);

        return $this->loadCart($cart);
    }

    public function clearCart(int $customerId): void
    {
        $cart = Cart::where('customer_id', $customerId)->first();

        if (! $cart) {
            return;
        }

        $cart->items()->delete();
        $cart->subtotal = 0;
        $cart->discount_amount = 0;
        $cart->shipping_fee = 0;
        $cart->total = 0;
        $cart->items_count = 0;
        $cart->save();
    }

    public function getItems(int $customerId): Collection
    {
        return $this->getCart($customerId)->items;
    }

    public function totals(Cart $cart): array
    {
        return [
            'subtotal' => (float) $cart->subtotal,
            'discount' => (float) $cart->discount_amount,
            'shipping' => (float) $cart->shipping_fee,
            'total' => (float) $cart->total,
        ];
    }

    protected function recalculateCart(Cart $cart): void
    {
        $cart->loadMissing('items');

        $subtotal = $cart->items->sum(fn (CartItem $item) => (float) $item->price * (int) $item->quantity);
        $itemsCount = $cart->items->sum('quantity');
        $discount = (float) $cart->discount_amount;
        $shipping = (float) $cart->shipping_fee;
        $total = max(0, $subtotal - $discount + $shipping);

        $cart->subtotal = $subtotal;
        $cart->items_count = $itemsCount;
        $cart->total = $total;
        $cart->save();
    }

    protected function loadCart(Cart $cart): Cart
    {
        return $cart->load(['items.product', 'items.variant']);
    }

    protected function buildPricingMeta(Product $product, ?ProductVariant $variant = null): array
    {
        $snapshot = $this->pricingService->buildOrderItemSnapshot($variant ?: $product);

        return [
            'original_price' => $snapshot['original_price'],
            'final_price' => $snapshot['price'],
            'sale_price' => $snapshot['sale_price'],
            'cost_price' => $snapshot['cost_price'],
            'sale_type' => $snapshot['sale_type'],
            'sale_value' => $snapshot['sale_value'],
            'sale_amount' => $snapshot['sale_amount'],
        ];
    }
}
