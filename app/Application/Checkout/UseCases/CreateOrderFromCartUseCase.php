<?php

namespace App\Application\Checkout\UseCases;

use App\Application\Checkout\Services\CheckoutService;

class CreateOrderFromCartUseCase
{
    public function __construct(
        private readonly CheckoutService $CheckoutService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement createOrderFromCart logic
        return $this->CheckoutService->createOrderFromCart($data);
    }
}
