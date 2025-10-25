<?php

namespace App\Application\Order\UseCases;

use App\Application\Order\Services\OrderService;

class CancelOrderUseCase
{
    public function __construct(
        private readonly OrderService $OrderService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement cancelOrder logic
        return $this->OrderService->cancelOrder($data);
    }
}
