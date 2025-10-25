<?php

namespace App\Application\Checkout\UseCases;

use App\Application\Checkout\Services\CheckoutService;

class ValidateCouponUseCase
{
    public function __construct(
        private readonly CheckoutService $CheckoutService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement validateCoupon logic
        return $this->CheckoutService->validateCoupon($data);
    }
}
