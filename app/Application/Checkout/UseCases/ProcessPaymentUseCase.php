<?php

namespace App\Application\Checkout\UseCases;

use App\Application\Checkout\Services\CheckoutService;

class ProcessPaymentUseCase
{
    public function __construct(
        private readonly CheckoutService $CheckoutService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement processPayment logic
        return $this->CheckoutService->processPayment($data);
    }
}
