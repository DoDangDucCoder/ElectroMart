<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\Services\CustomerService;

class RegisterCustomerUseCase
{
    public function __construct(
        private readonly CustomerService $CustomerService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement registerCustomer logic
        return $this->CustomerService->registerCustomer($data);
    }
}
