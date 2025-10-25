<?php

namespace App\Application\Order\UseCases;

use App\Application\Order\Services\OrderService;

class ProcessOrderUseCase
{
    public function __construct(
        private readonly OrderService $OrderService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement processOrder logic
        return $this->OrderService->processOrder($data);
    }
}
