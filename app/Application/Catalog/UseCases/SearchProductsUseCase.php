<?php

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\Services\CatalogService;

class SearchProductsUseCase
{
    public function __construct(
        private readonly CatalogService $CatalogService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement searchProducts logic
        return $this->CatalogService->searchProducts($data);
    }
}
