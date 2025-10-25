<?php

namespace App\Application\Order\UseCases;

use App\Application\Order\Services\OrderService;

class CalculateOrderTotalUseCase
{
    public function __construct(
        private readonly OrderService $OrderService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement calculateOrderTotal logic
        return $this->OrderService->calculateOrderTotal($data);
    }
}
