<?php

namespace App\Application\Checkout\Services;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Models\ProductAttribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class CartService
{
    public const SESSION_KEY = 'cart';

    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    /**
     * @return Collection<int, array<string,mixed>>
     */
    public function items(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []));
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
            throw new RuntimeException('Product not found.');
        }

        $attributeData = $this->resolveAttribute($productId, $attributeId);

        $cart = $this->items();

        $existingKey = $cart->search(function (array $item) use ($productId, $attributeId) {
            return (int) $item['product_id'] === $productId
                && (int) ($item['product_attribute_id'] ?? 0) === (int) ($attributeId ?? 0);
        });

        if ($existingKey !== false) {
            $cart[$existingKey]['product_qty'] += $quantity;
            $cart[$existingKey]['product_qty'] = max(1, $cart[$existingKey]['product_qty']);
            $cart[$existingKey]['product_quantity'] = $attributeData['stock'] ?? $product->product_quantity;
            if (! empty($attributeData['price'])) {
                $cart[$existingKey]['product_price'] = $attributeData['price'];
            }
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

        $cart->push([
            'session_id' => $sessionId,
            'product_id' => $productId,
            'product_name' => $product->product_name,
            'product_image' => $attributeData['image'] ?? $product->product_image,
            'product_price' => $attributeData['price'] ?? $product->product_price,
            'product_qty' => $quantity,
            'product_quantity' => $attributeData['stock'] ?? $product->product_quantity,
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
        $cart = $this->items()->map(function (array $item) use ($sessionId, $quantity) {
            if ($item['session_id'] === $sessionId) {
                $item['product_qty'] = max(1, $quantity);
            }

            return $item;
        });

        $this->persist($cart);
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

        /** @var ProductAttribute|null $attribute */
        $attribute = ProductAttribute::with('attribute')
            ->where('product_id', $productId)
            ->where('id', $attributeId)
            ->first();

        if (! $attribute) {
            throw new RuntimeException('Product attribute not found.');
        }

        return [
            'price' => (float) $attribute->price,
            'stock' => (int) $attribute->quantity,
            'image' => $attribute->image ?: null,
            'label' => $attribute->name ?: optional($attribute->attribute)->attribute_name,
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
