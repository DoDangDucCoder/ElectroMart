<?php

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\Services\CatalogService;

class UpdateProductStockUseCase
{
    public function __construct(
        private readonly CatalogService $CatalogService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement updateProductStock logic
        return $this->CatalogService->updateProductStock($data);
    }
}
