<?php

namespace App\Application\Reporting\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductRecommendationImpression;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductSearchQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductDiscoveryReportService
{
    public function getReport(array $filters = [], int $perPage = 20): array
    {
        $searchBase = $this->searchQueryBase($filters);
        $impressionBase = $this->impressionQueryBase($filters);

        return [
            'summary' => [
                'total_searches' => (clone $searchBase)->count(),
                'unique_keywords' => (clone $searchBase)->whereNotNull('keyword')->distinct('keyword')->count('keyword'),
                'recommendation_impressions' => (clone $impressionBase)->count(),
                'recommended_products' => (clone $impressionBase)->distinct('product_id')->count('product_id'),
            ],
            'top_keywords' => $this->topKeywords($filters),
            'top_filter_categories' => $this->topSearchCategories($filters),
            'recommendation_contexts' => $this->recommendationContexts($filters),
            'recommended_products_rows' => $this->recommendedProducts($filters, $perPage),
        ];
    }

    public function getApiReport(array $filters = [], int $perPage = 20): array
    {
        $report = $this->getReport($filters, $perPage);

        return [
            'summary' => $report['summary'],
            'top_keywords' => $report['top_keywords'],
            'top_filter_categories' => $report['top_filter_categories'],
            'recommendation_contexts' => $report['recommendation_contexts'],
            'recommended_products' => $report['recommended_products_rows']->items(),
            'pagination' => [
                'current_page' => $report['recommended_products_rows']->currentPage(),
                'last_page' => $report['recommended_products_rows']->lastPage(),
                'per_page' => $report['recommended_products_rows']->perPage(),
                'total' => $report['recommended_products_rows']->total(),
            ],
        ];
    }

    protected function topKeywords(array $filters): Collection
    {
        return $this->searchQueryBase($filters)
            ->selectRaw('keyword, COUNT(*) as searches_count, AVG(results_count) as avg_results_count')
            ->whereNotNull('keyword')
            ->where('keyword', '!=', '')
            ->groupBy('keyword')
            ->orderByDesc('searches_count')
            ->orderBy('keyword')
            ->limit(12)
            ->get();
    }

    protected function topSearchCategories(array $filters): Collection
    {
        return $this->searchQueryBase($filters)
            ->leftJoin('product_categories', 'product_categories.id', '=', 'product_search_queries.category_id')
            ->selectRaw('product_search_queries.category_id, product_categories.name as category_name, COUNT(*) as searches_count')
            ->whereNotNull('product_search_queries.category_id')
            ->groupBy('product_search_queries.category_id', 'product_categories.name')
            ->orderByDesc('searches_count')
            ->limit(8)
            ->get();
    }

    protected function recommendationContexts(array $filters): Collection
    {
        return $this->impressionQueryBase($filters)
            ->selectRaw('context, COUNT(*) as impressions_count')
            ->groupBy('context')
            ->orderByDesc('impressions_count')
            ->get();
    }

    protected function recommendedProducts(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->impressionQueryBase($filters)
            ->leftJoin('products', 'products.id', '=', 'product_recommendation_impressions.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.primary_category_id')
            ->selectRaw('
                product_recommendation_impressions.product_id,
                products.name as product_name,
                products.slug as product_slug,
                brands.name as brand_name,
                product_categories.name as category_name,
                COUNT(*) as impressions_count,
                MAX(product_recommendation_impressions.impressed_at) as last_impressed_at
            ')
            ->groupBy(
                'product_recommendation_impressions.product_id',
                'products.name',
                'products.slug',
                'brands.name',
                'product_categories.name'
            )
            ->orderByDesc('impressions_count')
            ->orderByDesc('last_impressed_at')
            ->paginate($perPage);
    }

    protected function searchQueryBase(array $filters)
    {
        return ProductSearchQuery::query()
            ->when(! empty($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('searched_at', '>=', $filters['date_from']);
            })
            ->when(! empty($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('searched_at', '<=', $filters['date_to']);
            });
    }

    protected function impressionQueryBase(array $filters)
    {
        return ProductRecommendationImpression::query()
            ->when(! empty($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('impressed_at', '>=', $filters['date_from']);
            })
            ->when(! empty($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('impressed_at', '<=', $filters['date_to']);
            });
    }
}
