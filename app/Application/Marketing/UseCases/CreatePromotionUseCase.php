<?php

namespace App\Application\Marketing\UseCases;

use App\Application\Marketing\Services\MarketingService;

class CreatePromotionUseCase
{
    public function __construct(
        private readonly MarketingService $MarketingService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement createPromotion logic
        return $this->MarketingService->createPromotion($data);
    }
}
