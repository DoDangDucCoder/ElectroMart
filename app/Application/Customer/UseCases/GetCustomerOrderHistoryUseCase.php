<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\Services\CustomerService;

class GetCustomerOrderHistoryUseCase
{
    public function __construct(
        private readonly CustomerService $CustomerService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement getCustomerOrderHistory logic
        return $this->CustomerService->getCustomerOrderHistory($data);
    }
}
