<?php

namespace App\Application\Order\UseCases;

use App\Application\Order\Services\OrderService;

class UpdateOrderStatusUseCase
{
    public function __construct(
        private readonly OrderService $OrderService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement updateOrderStatus logic
        return $this->OrderService->updateOrderStatus($data);
    }
}
