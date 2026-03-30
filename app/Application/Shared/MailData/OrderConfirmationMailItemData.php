<?php

namespace App\Application\Shared\MailData;

class OrderConfirmationMailItemData
{
    public function __construct(
        public readonly string $productName,
        public readonly int $productPrice,
        public readonly int $quantity
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productName: (string) ($data['product_name'] ?? 'Sản phẩm'),
            productPrice: (int) ($data['product_price'] ?? 0),
            quantity: (int) ($data['product_qty'] ?? 0),
        );
    }

    public function lineTotal(): int
    {
        return $this->productPrice * $this->quantity;
    }
}
