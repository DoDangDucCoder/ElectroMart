<?php

namespace App\Application\Marketing\UseCases;

use App\Application\Marketing\Services\MarketingService;

class GenerateReportsUseCase
{
    public function __construct(
        private readonly MarketingService $MarketingService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement generateReports logic
        return $this->MarketingService->generateReports($data);
    }
}
