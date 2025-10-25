<?php

namespace App\Application\Checkout\UseCases;

use App\Application\Checkout\Services\CheckoutService;

class CalculateShippingUseCase
{
    public function __construct(
        private readonly CheckoutService $CheckoutService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement calculateShipping logic
        return $this->CheckoutService->calculateShipping($data);
    }
}
