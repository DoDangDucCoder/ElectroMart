<?php

namespace App\Application\Shared\MailData;

class OrderConfirmationMailData
{
    /**
     * @param  list<OrderConfirmationMailItemData>  $items
     */
    public function __construct(
        public readonly string $brandName,
        public readonly string $orderCode,
        public readonly string $couponCode,
        public readonly int $feeShip,
        public readonly string $customerName,
        public readonly string $shippingName,
        public readonly string $shippingEmail,
        public readonly string $shippingPhone,
        public readonly string $shippingAddress,
        public readonly string $shippingNotes,
        public readonly string $paymentLabel,
        public readonly string $historyUrl,
        public readonly string $shopUrl,
        public readonly string $supportPhone,
        public readonly array $items
    ) {}

    public static function fromLegacyPayload(
        array $shipping,
        array $cartItems,
        array $code,
        array $overrides = []
    ): self {
        $shippingMethod = (string) ($shipping['shipping_method'] ?? '1');

        return new self(
            brandName: (string) ($overrides['brandName'] ?? 'DangDuc'),
            orderCode: (string) ($code['order_code'] ?? 'N/A'),
            couponCode: (string) ($code['coupon_code'] ?? 'Không áp dụng'),
            feeShip: (int) ($overrides['feeShip'] ?? $shipping['fee_ship'] ?? $shipping['fee'] ?? 0),
            customerName: (string) ($shipping['customer_name'] ?? $shipping['shipping_name'] ?? 'Quý khách'),
            shippingName: (string) ($shipping['shipping_name'] ?? 'Không có'),
            shippingEmail: (string) ($shipping['shipping_email'] ?? 'Không có'),
            shippingPhone: (string) ($shipping['shipping_phone'] ?? 'Không có'),
            shippingAddress: (string) ($shipping['shipping_address'] ?? 'Không có'),
            shippingNotes: (string) ($shipping['shipping_notes'] ?? 'Không có ghi chú bổ sung.'),
            paymentLabel: $shippingMethod === '0' ? 'Chuyển khoản ATM' : 'Thanh toán tiền mặt',
            historyUrl: (string) ($overrides['historyUrl'] ?? url('/account/orders')),
            shopUrl: (string) ($overrides['shopUrl'] ?? url('/')),
            supportPhone: (string) ($overrides['supportPhone'] ?? '1900 5689'),
            items: array_values(array_map(
                static fn (array $item): OrderConfirmationMailItemData => OrderConfirmationMailItemData::fromArray($item),
                $cartItems
            )),
        );
    }

    public function subTotal(): int
    {
        return array_reduce(
            $this->items,
            static fn (int $carry, OrderConfirmationMailItemData $item): int => $carry + $item->lineTotal(),
            0
        );
    }

    public function grandTotal(): int
    {
        return $this->subTotal() + $this->feeShip;
    }
}
