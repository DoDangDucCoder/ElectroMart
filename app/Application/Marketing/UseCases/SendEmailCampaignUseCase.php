<?php

namespace App\Application\Marketing\UseCases;

use App\Application\Marketing\Services\MarketingService;

class SendEmailCampaignUseCase
{
    public function __construct(
        private readonly MarketingService $MarketingService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement sendEmailCampaign logic
        return $this->MarketingService->sendEmailCampaign($data);
    }
}
