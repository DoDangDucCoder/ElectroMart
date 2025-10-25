<?php

namespace App\Application\Customer\UseCases;

use App\Application\Customer\Services\CustomerService;

class ManageCustomerAddressUseCase
{
    public function __construct(
        private readonly CustomerService $CustomerService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement manageCustomerAddress logic
        return $this->CustomerService->manageCustomerAddress($data);
    }
}
