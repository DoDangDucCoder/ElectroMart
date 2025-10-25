<?php

namespace App\Application\Shared\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $orderId,
        public readonly string $orderNumber,
        public readonly int $customerId,
        public readonly float $totalAmount
    ) {
    }
}
