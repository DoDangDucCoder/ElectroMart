<?php

namespace App\Events;

use App\Domain\Catalog\Entities\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Product $product,
        public readonly ?int $customerId = null,
        public readonly ?string $ipAddress = null
    ) {
    }
}
