<?php

namespace App\Application\Shared\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        // TODO: Implement filter logic
        return $query;
    }

    protected function applyPriceFilter(Builder $query, array $priceRange): Builder
    {
        if (isset($priceRange['min'])) {
            $query->where('price', '>=', $priceRange['min']);
        }
        
        if (isset($priceRange['max'])) {
            $query->where('price', '<=', $priceRange['max']);
        }
        
        return $query;
    }

    protected function applyCategoryFilter(Builder $query, array $categories): Builder
    {
        if (!empty($categories)) {
            $query->whereIn('category_id', $categories);
        }
        
        return $query;
    }

    protected function applyStatusFilter(Builder $query, string $status): Builder
    {
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query;
    }
}
