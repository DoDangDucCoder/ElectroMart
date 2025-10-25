<?php

namespace App\Application\Marketing\UseCases;

use App\Application\Marketing\Services\MarketingService;

class TrackCustomerBehaviorUseCase
{
    public function __construct(
        private readonly MarketingService $MarketingService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement trackCustomerBehavior logic
        return $this->MarketingService->trackCustomerBehavior($data);
    }
}
