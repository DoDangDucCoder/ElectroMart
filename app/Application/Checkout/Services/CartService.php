<?php

namespace App\Application\Checkout\Services;

use App\Application\Catalog\Services\ProductPricingService;
use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Models\ProductAttribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class CartService
{
    public const SESSION_KEY = 'cart';

    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductPricingService $pricingService,
    ) {
    }

    /**
     * @return Collection<int, array<string,mixed>>
     */
    public function items(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []));
    }

    public function getCart(?int $customerId = null): array
    {
        $items = $this->items()->values();
        $totals = $this->totals();

        return [
            'customer_id' => $customerId,
            'items' => $items->all(),
            'subtotal' => $totals['subtotal'],
            'discount_amount' => $totals['discount'],
            'shipping_fee' => 0.0,
            'total' => $totals['total'],
            'items_count' => $items->sum('product_qty'),
        ];
    }

    public function count(): int
    {
        return $this->items()->sum('product_qty');
    }

    public function subtotal(): float
    {
        return $this->items()->sum(function (array $item) {
            return (float) $item['product_price'] * (int) $item['product_qty'];
        });
    }

    public function addItem(int $productId, int $quantity = 1, ?int $attributeId = null): array
    {
        $product = $this->productService->findProductById($productId);

        if (! $product) {
            throw new RuntimeException('Không tìm thấy sản phẩm.');
        }

        $attributeData = $this->resolveAttribute($productId, $attributeId);
        $availableStock = (int) ($attributeData['stock'] ?? $product->product_quantity ?? 0);

        $cart = $this->items();

        $pricingData = $this->buildSessionPricingData($product, $attributeData);

        $existingKey = $cart->search(function (array $item) use ($productId, $attributeId) {
            return (int) $item['product_id'] === $productId
                && (int) ($item['product_attribute_id'] ?? 0) === (int) ($attributeId ?? 0);
        });

        if ($existingKey !== false) {
            $nextQuantity = (int) ($cart[$existingKey]['product_qty'] ?? 0) + $quantity;
            $this->ensureAvailableQuantity($availableStock, $nextQuantity, $attributeData['label'] ?? $product->product_name);

            $cart[$existingKey]['product_qty'] += $quantity;
            $cart[$existingKey]['product_qty'] = max(1, $cart[$existingKey]['product_qty']);
            $cart[$existingKey]['product_quantity'] = $availableStock;
            $cart[$existingKey]['product_price'] = $pricingData['product_price'];
            $cart[$existingKey]['product_original_price'] = $pricingData['product_original_price'];
            $cart[$existingKey]['product_cost_price'] = $pricingData['product_cost_price'];
            $cart[$existingKey]['product_sale_price'] = $pricingData['product_sale_price'];
            $cart[$existingKey]['product_sale_type'] = $pricingData['product_sale_type'];
            $cart[$existingKey]['product_sale_value'] = $pricingData['product_sale_value'];
            $cart[$existingKey]['product_sale_amount'] = $pricingData['product_sale_amount'];
            if (! empty($attributeData['image'])) {
                $cart[$existingKey]['product_image'] = $attributeData['image'];
            }
            if (! empty($attributeData['label'])) {
                $cart[$existingKey]['product_attribute'] = $attributeData['label'];
                $cart[$existingKey]['attribute_label'] = $attributeData['label'];
            }
            $this->persist($cart);

            return $cart[$existingKey];
        }

        $sessionId = Str::random(10);
        $this->ensureAvailableQuantity($availableStock, $quantity, $attributeData['label'] ?? $product->product_name);

        $cart->push([
            'session_id' => $sessionId,
            'product_id' => $productId,
            'product_name' => $product->product_name,
            'product_image' => $attributeData['image'] ?? $product->product_image,
            'product_price' => $pricingData['product_price'],
            'product_original_price' => $pricingData['product_original_price'],
            'product_cost_price' => $pricingData['product_cost_price'],
            'product_sale_price' => $pricingData['product_sale_price'],
            'product_sale_type' => $pricingData['product_sale_type'],
            'product_sale_value' => $pricingData['product_sale_value'],
            'product_sale_amount' => $pricingData['product_sale_amount'],
            'product_qty' => $quantity,
            'product_quantity' => $availableStock,
            'product_attribute_id' => $attributeId,
            'product_attribute' => $attributeData['label'] ?? null,
            'attribute_label' => $attributeData['label'] ?? null,
        ]);

        $this->persist($cart);

        return $cart->last();
    }

    public function removeBySessionId(string $sessionId): void
    {
        $cart = $this->items()->reject(fn (array $item) => $item['session_id'] === $sessionId);
        $this->persist($cart);
    }

    public function removeByProductId(int $productId): void
    {
        $cart = $this->items()->reject(fn (array $item) => (int) ($item['product_id'] ?? 0) === $productId);
        $this->persist($cart);
    }

    public function updateQuantity(string $sessionId, int $quantity): void
    {
        $cart = $this->items();
        $index = $cart->search(fn (array $item) => $item['session_id'] === $sessionId);

        if ($index === false) {
            throw new RuntimeException('Không tìm thấy sản phẩm cần cập nhật.');
        }

        $item = $cart[$index];
        $nextQuantity = max(1, $quantity);
        $availableStock = (int) ($item['product_quantity'] ?? 0);
        $itemLabel = $item['attribute_label']
            ?? $item['product_attribute']
            ?? $item['product_name']
            ?? null;

        $this->ensureAvailableQuantity($availableStock, $nextQuantity, $itemLabel);

        $item['product_qty'] = $nextQuantity;
        $cart->put($index, $item);

        $this->persist($cart);
    }

    public function updateItem(?int $customerId, string $sessionId, int $quantity): array
    {
        if ($quantity <= 0) {
            $this->removeBySessionId($sessionId);
        } else {
            $this->updateQuantity($sessionId, $quantity);
        }

        return $this->getCart($customerId);
    }

    public function removeItem(?int $customerId, string $sessionId): array
    {
        $this->removeBySessionId($sessionId);

        return $this->getCart($customerId);
    }

    public function clearCart(?int $customerId): void
    {
        $this->clear();
    }

    /**
     * @param  array<string,int>  $quantities
     */
    public function updateQuantities(array $quantities): array
    {
        $messages = [];
        $cart = $this->items();

        foreach ($quantities as $sessionId => $qty) {
            $index = $cart->search(fn (array $item) => $item['session_id'] === $sessionId);

            if ($index === false) {
                continue;
            }

            $available = $cart[$index]['product_quantity'] ?? 0;
            if ($qty <= 0) {
                $messages[] = sprintf('Removed %s from cart.', $cart[$index]['product_name'] ?? '');
                $cart = $cart->reject(fn (array $item) => $item['session_id'] === $sessionId)->values();
                continue;
            }

            if ($available > 0 && $qty > $available) {
                $messages[] = sprintf(
                    'Quantity "%s" exceeds stock (%d).',
                    $cart[$index]['product_name'] ?? '',
                    $available
                );
                $item = $cart[$index];
                $item['product_qty'] = $available;
                $cart->put($index, $item);
                continue;
            }

            $item = $cart[$index];
            $item['product_qty'] = $qty;
            $cart->put($index, $item);
            $messages[] = sprintf('Updated "%s" successfully.', $cart[$index]['product_name'] ?? '');
        }

        $this->persist($cart);

        return $messages;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    protected function persist(Collection $cart): void
    {
        Session::put(self::SESSION_KEY, $cart->values()->all());
        Session::save();
    }

    /**
     * @return array{price?:float,stock?:int,image?:string,label?:string}
     */
    protected function resolveAttribute(int $productId, ?int $attributeId): array
    {
        if (! $attributeId) {
            return [];
        }

        if (! Schema::hasTable((new ProductAttribute())->getTable())) {
            throw new RuntimeException('Tùy chọn sản phẩm hiện không khả dụng.');
        }

        /** @var ProductAttribute|null $attribute */
        $attribute = ProductAttribute::with('attribute')
            ->where('product_id', $productId)
            ->where('id', $attributeId)
            ->first();

        if (! $attribute) {
            throw new RuntimeException('Không tìm thấy phiên bản sản phẩm.');
        }

        return [
            'price' => (float) $attribute->price,
            'stock' => (int) $attribute->quantity,
            'image' => $attribute->image ?: null,
            'label' => $attribute->name ?: optional($attribute->attribute)->attribute_name,
        ];
    }

    protected function ensureAvailableQuantity(int $availableStock, int $requestedQuantity, ?string $label = null): void
    {
        $itemLabel = trim((string) $label);

        if ($availableStock <= 0) {
            throw new RuntimeException($itemLabel !== ''
                ? sprintf('%s hiện đã hết hàng.', $itemLabel)
                : 'Sản phẩm hiện đã hết hàng.');
        }

        if ($requestedQuantity > $availableStock) {
            throw new RuntimeException($itemLabel !== ''
                ? sprintf('%s chỉ còn %d sản phẩm trong kho.', $itemLabel, $availableStock)
                : sprintf('Sản phẩm chỉ còn %d sản phẩm trong kho.', $availableStock));
        }
    }

    /**
     * @param  array<string,mixed>  $attributeData
     * @return array<string,mixed>
     */
    protected function buildSessionPricingData(Product $product, array $attributeData = []): array
    {
        if (isset($attributeData['price']) && $attributeData['price'] !== null) {
            $attributePrice = (float) $attributeData['price'];

            return [
                'product_price' => $attributePrice,
                'product_original_price' => $attributePrice,
                'product_cost_price' => 0.0,
                'product_sale_price' => null,
                'product_sale_type' => null,
                'product_sale_value' => null,
                'product_sale_amount' => 0.0,
            ];
        }

        $snapshot = $this->pricingService->buildOrderItemSnapshot($product);

        return [
            'product_price' => $snapshot['price'],
            'product_original_price' => $snapshot['original_price'],
            'product_cost_price' => $snapshot['cost_price'],
            'product_sale_price' => $snapshot['sale_price'],
            'product_sale_type' => $snapshot['sale_type'],
            'product_sale_value' => $snapshot['sale_value'],
            'product_sale_amount' => $snapshot['sale_amount'],
        ];
    }

    public function findItem(string $sessionId): ?array
    {
        return $this->items()->firstWhere('session_id', $sessionId);
    }

    public function totalsWithCoupon(?array $coupon = null): array
    {
        $subtotal = $this->subtotal();
        $discount = 0.0;

        if ($coupon) {
            $discount = (float) Arr::get($coupon, 'discount_amount', 0);
        }

        $total = max(0, $subtotal - $discount);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    public function totals(?array $coupon = null, float $shippingFee = 0.0): array
    {
        $totals = $this->totalsWithCoupon($coupon);
        $shipping = max(0, $shippingFee);
        $grandTotal = max(0, $totals['total'] + $shipping);

        return [
            'subtotal' => $totals['subtotal'],
            'discount' => $totals['discount'],
            'shipping' => $shipping,
            'total' => $grandTotal,
        ];
    }
}
