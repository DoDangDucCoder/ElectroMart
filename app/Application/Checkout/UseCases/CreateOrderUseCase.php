<?php

namespace App\Application\Checkout\UseCases;

use App\Domain\Order\Entities\Order;
use App\Domain\Order\Entities\OrderStatus;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Shared\ValueObjects\Money;
use Illuminate\Support\Str;

class CreateOrderUseCase
{
    public function execute(
        Customer $customer,
        array $items,
        Money $subtotal,
        Money $shippingFee,
        Money $discountAmount,
        ?int $couponId = null,
        ?int $customerAddressId = null,
        ?string $notes = null
    ): Order {
        $totalAmount = $subtotal->add($shippingFee)->subtract($discountAmount);
        
        $orderNumber = $this->generateOrderNumber();
        
        $orderData = [
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
            'subtotal' => $subtotal->getAmount(),
            'shipping_fee' => $shippingFee->getAmount(),
            'discount_amount' => $discountAmount->getAmount(),
            'total_amount' => $totalAmount->getAmount(),
            'notes' => $notes,
            'coupon_id' => $couponId,
            'customer_address_id' => $customerAddressId,
            'placed_at' => now(),
        ];

        // This would typically use an OrderRepository
        // For now, we'll return a mock Order entity
        return new Order(
            id: 0, // Would be set by repository
            orderNumber: $orderNumber,
            customerId: $customer->id,
            status: OrderStatus::PENDING,
            subtotal: $subtotal,
            shippingFee: $shippingFee,
            discountAmount: $discountAmount,
            totalAmount: $totalAmount,
            notes: $notes,
            couponId: $couponId,
            customerAddressId: $customerAddressId,
            placedAt: new \DateTimeImmutable(),
        );
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('Ymd');
        $random = Str::upper(Str::random(6));
        
        return $prefix . $timestamp . $random;
    }
}
