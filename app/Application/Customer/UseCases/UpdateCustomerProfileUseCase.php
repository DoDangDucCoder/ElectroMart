<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\Services\CustomerService;

class UpdateCustomerProfileUseCase
{
    public function __construct(
        private readonly CustomerService $CustomerService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement updateCustomerProfile logic
        return $this->CustomerService->updateCustomerProfile($data);
    }
}
