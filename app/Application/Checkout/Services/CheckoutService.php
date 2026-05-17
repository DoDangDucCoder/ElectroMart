<?php

namespace App\Application\Checkout\Services;

use App\Infrastructure\Persistence\Eloquent\Checkout\CartItem;
use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamProvince;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CustomerCartService $customerCartService,
        private readonly CouponService $couponService,
        private readonly ShippingService $shippingService,
    ) {}

    public function getCustomerOrders(int $customerId): Collection
    {
        return Order::with(['items', 'shipping', 'payments'])
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->get();
    }

    public function createOrder(
        int $customerId,
        int $addressId,
        string $paymentMethod,
        ?string $notes = null,
        ?string $couponCode = null
    ): Order {
        $cart = $this->customerCartService->getCart($customerId);
        $items = $cart->items;

        if ($items->isEmpty()) {
            throw new RuntimeException('Giỏ hàng đang trống.');
        }

        return DB::transaction(function () use ($customerId, $addressId, $paymentMethod, $notes, $couponCode, $cart, $items) {
            $address = Address::where('customer_id', $customerId)->find($addressId);

            if (! $address) {
                throw new RuntimeException('Địa chỉ giao hàng không hợp lệ.');
            }

            $shippingFee = $this->shippingService->calculateFee(
                $address?->location_city_id,
                $address?->location_province_id,
                $address?->location_ward_id,
                0
            );

            $couponData = null;
            $couponModel = null;

            if ($couponCode) {
                $validation = $this->couponService->validateCoupon($couponCode, (float) $cart->subtotal, $customerId);
                if (! $validation['valid']) {
                    throw new RuntimeException($validation['message']);
                }
                $couponModel = $validation['coupon'];
                $couponData = [
                    'code' => $couponModel->code,
                    'discount_amount' => $validation['discount_amount'],
                ];
            }

            $totals = [
                'subtotal' => (float) $cart->subtotal,
                'discount' => (float) ($couponData['discount_amount'] ?? 0),
                'shipping' => (float) $shippingFee,
                'total' => max(0, (float) $cart->subtotal - (float) ($couponData['discount_amount'] ?? 0) + (float) $shippingFee),
            ];

            $payload = [
                'shipping_name' => $address?->recipient_name,
                'shipping_phone' => $address?->recipient_phone,
                'shipping_email' => $address?->email,
                'shipping_address' => $address?->address_line1 ?? '',
                'shipping_notes' => $address?->address_line2,
                'postal_code' => $address?->postal_code,
                'city_id' => $address?->location_city_id,
                'province_id' => $address?->location_province_id,
                'ward_id' => $address?->location_ward_id,
                'shipping_method' => null,
                'source' => 'api',
                'order_note' => $notes,
                'payment_method' => $paymentMethod,
            ];

            $order = $this->createOrderRecord($payload, $customerId, $address->id, $totals, $couponData);

            $this->createOrderItemsFromCart($order, $items);
            $this->createOrderShipping($order, $payload, $shippingFee);
            $this->createOrderPayment($order, $payload, $totals['total']);

            if ($couponModel) {
                $this->couponService->redeem($couponModel, $customerId, $order->id, $totals['discount']);
            }

            $this->customerCartService->clearCart($customerId);

            return $order->fresh(['items', 'shipping', 'payments']);
        });
    }

    public function getOrderById(int $orderId, int $customerId): ?Order
    {
        return Order::with(['items', 'shipping', 'payments', 'address'])
            ->where('customer_id', $customerId)
            ->where('id', $orderId)
            ->first();
    }

    public function cancelOrder(int $orderId, int $customerId): ?Order
    {
        $order = Order::where('customer_id', $customerId)
            ->where('id', $orderId)
            ->first();

        if (! $order) {
            return null;
        }

        if (in_array($order->status, ['cancelled', 'completed', 'refunded'], true)) {
            throw new RuntimeException('Không thể hủy đơn hàng ở trạng thái hiện tại.');
        }

        $order->status = 'cancelled';
        $order->cancelled_at = now();
        $order->save();

        return $order->fresh(['items', 'shipping', 'payments']);
    }

    public function confirmOrder(array $payload, ?int $customerId = null): Order
    {
        $items = $this->cartService->selectedCheckoutItems();

        if ($items->isEmpty()) {
            throw new RuntimeException('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        return DB::transaction(function () use ($payload, $customerId, $items) {
            $shippingFee = $this->resolveShippingFee($payload);
            $couponData = $this->couponService->validatedCurrent(
                $this->cartService->subtotalFor($items),
                $customerId
            );
            $totals = $this->cartService->totalsFor($items, $couponData, $shippingFee);

            $address = $this->storeCustomerAddress($payload, $customerId);
            $order = $this->createOrderRecord($payload, $customerId, $address?->id, $totals, $couponData);

            $this->createOrderItems($order, $items);
            $this->createOrderShipping($order, $payload, $shippingFee);
            $this->createOrderPayment($order, $payload, $totals['total']);

            $couponModel = $this->couponService->resolveCouponFromSession();
            if ($couponModel) {
                $this->couponService->redeem($couponModel, $customerId, $order->id, $totals['discount']);
            }

            $this->cartService->removeBySessionIds($items->pluck('session_id')->all());
            $this->cartService->clearSelectedCheckoutItemIds();
            $this->couponService->clear();
            $this->shippingService->clearFee();

            return $order->fresh(['items', 'shipping', 'payments']);
        });
    }

    protected function resolveShippingFee(array $payload): float
    {
        $vietnamProvinceCode = $payload['vietnam_province_code'] ?? null;
        $vietnamWardCode = $payload['vietnam_ward_code'] ?? null;

        if ($vietnamProvinceCode && $vietnamWardCode) {
            $fee = $this->shippingService->calculateVietnam2025Fee($vietnamProvinceCode, $vietnamWardCode, -1);

            if ($fee >= 0) {
                return $fee;
            }
        }

        $cityId = (int) ($payload['city_id'] ?? 0);
        $provinceId = (int) ($payload['province_id'] ?? 0);
        $wardId = (int) ($payload['ward_id'] ?? 0);

        if ($cityId > 0 && $provinceId > 0 && $wardId > 0) {
            return $this->shippingService->calculateFee($cityId, $provinceId, $wardId);
        }

        return (float) ($payload['shipping_fee'] ?? $this->shippingService->getFee());
    }

    protected function storeCustomerAddress(array $payload, ?int $customerId): ?Address
    {
        if (! $customerId) {
            return null;
        }

        /** @var Customer|null $customer */
        $customer = Customer::find($customerId);

        if (! $customer) {
            return null;
        }

        return $customer->addresses()->create([
            'label' => $payload['address_label'] ?? 'Checkout',
            'recipient_name' => $payload['shipping_name'] ?? $customer->name,
            'recipient_phone' => $payload['shipping_phone'] ?? $customer->phone,
            'email' => $payload['shipping_email'] ?? $customer->email,
            'address_line1' => $payload['shipping_address'] ?? '',
            'address_line2' => $payload['shipping_notes'] ?? null,
            'country' => 'Vietnam',
            'location_city_id' => $payload['city_id'] ?? null,
            'location_province_id' => $payload['province_id'] ?? null,
            'location_ward_id' => $payload['ward_id'] ?? null,
            'is_default' => false,
            'meta' => array_filter([
                'source' => 'checkout_form',
                'vietnam_2025' => $this->vietnamLocationMeta($payload),
            ]),
        ]);
    }

    protected function createOrderRecord(array $payload, ?int $customerId, ?int $addressId, array $totals, ?array $couponData): Order
    {
        $orderCode = Str::upper(Str::random(10));

        /** @var Order $order */
        $order = Order::create([
            'code' => $orderCode,
            'customer_id' => $customerId,
            'customer_address_id' => $addressId,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'currency' => 'VND',
            'subtotal' => $totals['subtotal'],
            'discount_total' => $totals['discount'],
            'shipping_total' => $totals['shipping'],
            'tax_total' => 0,
            'total' => $totals['total'],
            'coupon_code' => $couponData['code'] ?? null,
            'coupon_discount' => $totals['discount'],
            'source' => $payload['source'] ?? 'web',
            'note' => $payload['order_note'] ?? null,
            'placed_at' => now(),
            'meta' => [
                'shipping_method' => $payload['shipping_method'] ?? null,
            ],
        ]);

        return $order;
    }

    protected function createOrderItems(Order $order, $items): void
    {
        /** @var \Illuminate\Support\Collection $items */
        $items->each(function (array $item) use ($order) {
            $unitPrice = (float) ($item['product_price'] ?? 0);
            $quantity = (int) ($item['product_qty'] ?? 1);
            $unitSaleAmount = (float) ($item['product_sale_amount'] ?? 0);

            $order->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_variant_id' => $item['product_attribute_id'] ?? null,
                'product_name' => $item['product_name'],
                'sku' => null,
                'attributes' => $item['product_attribute']
                    ? ['attribute' => $item['product_attribute']]
                    : null,
                'original_price' => (float) ($item['product_original_price'] ?? $unitPrice),
                'price' => $unitPrice,
                'sale_price' => $item['product_sale_price'] ?? ($unitSaleAmount > 0 ? $unitPrice : null),
                'cost_price' => (float) ($item['product_cost_price'] ?? 0),
                'sale_type' => $item['product_sale_type'] ?? null,
                'sale_value' => $item['product_sale_value'] ?? null,
                'sale_amount' => $unitSaleAmount,
                'discount_amount' => round($unitSaleAmount * $quantity, 2),
                'quantity' => $quantity,
                'subtotal' => (float) ($item['product_price'] ?? 0) * $quantity,
                'tax_amount' => 0,
                'meta' => [
                    'session_id' => $item['session_id'] ?? null,
                    'thumbnail' => $item['product_image'] ?? null,
                ],
            ]);
        });
    }

    protected function createOrderItemsFromCart(Order $order, Collection $items): void
    {
        $items->each(function (CartItem $item) use ($order) {
            $meta = (array) ($item->meta ?? []);
            $unitPrice = (float) $item->price;
            $quantity = (int) $item->quantity;
            $unitSaleAmount = (float) ($meta['sale_amount'] ?? 0);

            $order->items()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product?->name ?? 'Unknown',
                'sku' => $item->variant?->sku,
                'attributes' => $item->variant?->attributes,
                'original_price' => (float) ($meta['original_price'] ?? $unitPrice),
                'price' => $unitPrice,
                'sale_price' => $meta['sale_price'] ?? ($unitSaleAmount > 0 ? $unitPrice : null),
                'cost_price' => (float) ($meta['cost_price'] ?? 0),
                'sale_type' => $meta['sale_type'] ?? null,
                'sale_value' => $meta['sale_value'] ?? null,
                'sale_amount' => $unitSaleAmount,
                'discount_amount' => round($unitSaleAmount * $quantity, 2),
                'quantity' => $quantity,
                'subtotal' => (float) $item->subtotal,
                'tax_amount' => 0,
                'meta' => [
                    'cart_item_id' => $item->id,
                ],
            ]);
        });
    }

    protected function createOrderShipping(Order $order, array $payload, float $shippingFee): void
    {
        $order->shipping()->create([
            'recipient_name' => $payload['shipping_name'] ?? null,
            'recipient_phone' => $payload['shipping_phone'] ?? null,
            'email' => $payload['shipping_email'] ?? null,
            'country' => 'Vietnam',
            'city_id' => $payload['city_id'] ?? null,
            'province_id' => $payload['province_id'] ?? null,
            'ward_id' => $payload['ward_id'] ?? null,
            'postal_code' => $payload['postal_code'] ?? null,
            'address_line1' => $payload['shipping_address'] ?? '',
            'address_line2' => $payload['shipping_notes'] ?? null,
            'shipping_method' => $payload['shipping_method'] ?? null,
            'tracking_number' => null,
            'shipping_fee' => $shippingFee,
            'insurance_fee' => 0,
            'meta' => array_filter([
                'vietnam_2025' => $this->vietnamLocationMeta($payload),
            ]),
        ]);
    }

    protected function createOrderPayment(Order $order, array $payload, float $amount): void
    {
        $provider = $payload['payment_method'] ?? 'cod';

        $order->payments()->create([
            'provider' => $provider,
            'status' => $provider === 'cod' ? 'pending' : 'processing',
            'currency' => 'VND',
            'amount' => $amount,
            'fee' => 0,
            'paid_at' => null,
            'meta' => array_filter([
                'vietnam_2025' => $this->vietnamLocationMeta($payload),
            ]),
        ]);
    }

    protected function vietnamLocationMeta(array $payload): ?array
    {
        $provinceCode = $payload['vietnam_province_code'] ?? null;
        $wardCode = $payload['vietnam_ward_code'] ?? null;

        if (! $provinceCode || ! $wardCode) {
            return null;
        }

        $province = VietnamProvince::query()->where('code', $provinceCode)->first();
        $ward = VietnamWard::query()
            ->where('code', $wardCode)
            ->where('province_code', $provinceCode)
            ->first();

        return [
            'province_code' => $provinceCode,
            'province_name' => $province?->name,
            'ward_code' => $wardCode,
            'ward_name' => $ward?->name,
        ];
    }
}
