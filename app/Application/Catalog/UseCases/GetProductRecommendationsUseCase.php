<?php

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\Services\CatalogService;

class GetProductRecommendationsUseCase
{
    public function __construct(
        private readonly CatalogService $CatalogService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement getProductRecommendations logic
        return $this->CatalogService->getProductRecommendations($data);
    }
}
