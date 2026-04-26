<?php

namespace Tests\Unit;

use App\Domain\Order\Entities\Order;
use App\Domain\Order\Entities\OrderStatus;
use Tests\TestCase;

class OrderDomainAutoloadTest extends TestCase
{
    public function test_order_status_autoloads_independently_from_order_entity(): void
    {
        $this->assertTrue(enum_exists(OrderStatus::class));
        $this->assertTrue(class_exists(Order::class));
    }
}
