<?php

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\Services\CatalogService;

class ManageProductVariantsUseCase
{
    public function __construct(
        private readonly CatalogService $CatalogService
    ) {
    }

    public function execute(array $data): mixed
    {
        // TODO: Implement manageProductVariants logic
        return $this->CatalogService->manageProductVariants($data);
    }
}
