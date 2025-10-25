<?php

namespace App\Application\Shared\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductSearchService
{
    public function search(string $query, array $filters = []): Collection
    {
        // TODO: Implement search logic
        return collect();
    }

    public function filter(Builder $query, array $filters): Builder
    {
        // TODO: Implement filter logic
        return $query;
    }

    public function getSuggestions(string $query): Collection
    {
        // TODO: Implement suggestions logic
        return collect();
    }
}
