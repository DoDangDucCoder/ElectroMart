<?php

namespace App\Application\Checkout\Services;

use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly ShippingService $shippingService,
    ) {
    }

    public function confirmOrder(array $payload, ?int $customerId = null): Order
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($payload, $customerId, $items) {
            $shippingFee = (float) ($payload['shipping_fee'] ?? $this->shippingService->getFee());
            $couponData = $this->couponService->current();
            $totals = $this->cartService->totals($couponData, $shippingFee);

            $address = $this->storeCustomerAddress($payload, $customerId);
            $order = $this->createOrderRecord($payload, $customerId, $address?->id, $totals, $couponData);

            $this->createOrderItems($order, $items);
            $this->createOrderShipping($order, $payload, $shippingFee);
            $this->createOrderPayment($order, $payload, $totals['total']);

            $couponModel = $this->couponService->resolveCouponFromSession();
            if ($couponModel) {
                $this->couponService->redeem($couponModel, $customerId, $order->id, $totals['discount']);
            }

            $this->cartService->clear();
            $this->couponService->clear();
            $this->shippingService->clearFee();

            return $order->fresh(['items', 'shipping', 'payments']);
        });
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
            'meta' => [
                'source' => 'checkout_form',
            ],
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
            $order->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_variant_id' => $item['product_attribute_id'] ?? null,
                'product_name' => $item['product_name'],
                'sku' => null,
                'attributes' => $item['product_attribute']
                    ? ['attribute' => $item['product_attribute']]
                    : null,
                'price' => (float) $item['product_price'],
                'sale_price' => null,
                'discount_amount' => 0,
                'quantity' => (int) $item['product_qty'],
                'subtotal' => (float) $item['product_price'] * (int) $item['product_qty'],
                'tax_amount' => 0,
                'meta' => [
                    'session_id' => $item['session_id'] ?? null,
                    'thumbnail' => $item['product_image'] ?? null,
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
            'meta' => null,
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
            'meta' => null,
        ]);
    }
}
