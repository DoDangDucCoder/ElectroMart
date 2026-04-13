<?php

namespace App\Application\Reporting\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductRecommendationImpression;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductSearchQuery;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ProductDiscoveryReportService
{
    public function getReport(array $filters = [], int $perPage = 20): array
    {
        $filters = $this->normalizeDateFilters($filters);
        $searchTableReady = $this->hasSearchQueryTable();
        $impressionTableReady = $this->hasImpressionTable();

        return [
            'summary' => [
                'total_searches' => $searchTableReady ? (clone $this->searchQueryBase($filters))->count() : 0,
                'unique_keywords' => $searchTableReady
                    ? (clone $this->searchQueryBase($filters))->whereNotNull('keyword')->distinct('keyword')->count('keyword')
                    : 0,
                'recommendation_impressions' => $impressionTableReady ? (clone $this->impressionQueryBase($filters))->count() : 0,
                'recommended_products' => $impressionTableReady
                    ? (clone $this->impressionQueryBase($filters))->distinct('product_id')->count('product_id')
                    : 0,
            ],
            'top_keywords' => $searchTableReady ? $this->topKeywords($filters) : collect(),
            'top_filter_categories' => $searchTableReady ? $this->topSearchCategories($filters) : collect(),
            'recommendation_contexts' => $impressionTableReady ? $this->recommendationContexts($filters) : collect(),
            'recommended_products_rows' => $impressionTableReady
                ? $this->recommendedProducts($filters, $perPage)
                : $this->emptyPaginator($perPage),
            'discovery_tables_ready' => [
                'search_queries' => $searchTableReady,
                'recommendation_impressions' => $impressionTableReady,
            ],
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
        $query = $this->searchQueryBase($filters)
            ->whereNotNull('product_search_queries.category_id');

        if (Schema::hasTable((new ProductCategory())->getTable())) {
            return $query
                ->leftJoin('product_categories', 'product_categories.id', '=', 'product_search_queries.category_id')
                ->selectRaw('product_search_queries.category_id, product_categories.name as category_name, COUNT(*) as searches_count')
                ->groupBy('product_search_queries.category_id', 'product_categories.name')
                ->orderByDesc('searches_count')
                ->limit(8)
                ->get();
        }

        return $query
            ->selectRaw('product_search_queries.category_id, NULL as category_name, COUNT(*) as searches_count')
            ->groupBy('product_search_queries.category_id')
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
        $query = $this->impressionQueryBase($filters);
        $brandsTableReady = Schema::hasTable('brands');
        $categoriesTableReady = Schema::hasTable('product_categories');

        if (! Schema::hasTable('products')) {
            return $query
                ->selectRaw('
                    product_recommendation_impressions.product_id,
                    NULL as product_name,
                    NULL as product_slug,
                    NULL as brand_name,
                    NULL as category_name,
                    COUNT(*) as impressions_count,
                    MAX(product_recommendation_impressions.impressed_at) as last_impressed_at
                ')
                ->groupBy('product_recommendation_impressions.product_id')
                ->orderByDesc('impressions_count')
                ->orderByDesc('last_impressed_at')
                ->paginate($perPage);
        }

        $query->leftJoin('products', 'products.id', '=', 'product_recommendation_impressions.product_id');

        if ($brandsTableReady) {
            $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id');
        }

        if ($categoriesTableReady) {
            $query->leftJoin('product_categories', 'product_categories.id', '=', 'products.primary_category_id');
        }

        $groupByColumns = [
            'product_recommendation_impressions.product_id',
            'products.name',
            'products.slug',
        ];

        if ($brandsTableReady) {
            $groupByColumns[] = 'brands.name';
        }

        if ($categoriesTableReady) {
            $groupByColumns[] = 'product_categories.name';
        }

        return $query
            ->selectRaw('
                product_recommendation_impressions.product_id,
                products.name as product_name,
                products.slug as product_slug,
                ' . ($brandsTableReady ? 'brands.name' : 'NULL') . ' as brand_name,
                ' . ($categoriesTableReady ? 'product_categories.name' : 'NULL') . ' as category_name,
                COUNT(*) as impressions_count,
                MAX(product_recommendation_impressions.impressed_at) as last_impressed_at
            ')
            ->groupBy(...$groupByColumns)
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

    protected function hasSearchQueryTable(): bool
    {
        return Schema::hasTable((new ProductSearchQuery())->getTable());
    }

    protected function hasImpressionTable(): bool
    {
        return Schema::hasTable((new ProductRecommendationImpression())->getTable());
    }

    protected function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        return new Paginator(
            items: [],
            total: 0,
            perPage: $perPage,
            currentPage: 1,
            options: ['path' => request()->url()]
        );
    }

    protected function normalizeDateFilters(array $filters): array
    {
        foreach (['date_from', 'date_to'] as $key) {
            if (empty($filters[$key])) {
                continue;
            }

            try {
                $filters[$key] = Carbon::parse($filters[$key])->toDateString();
            } catch (\Throwable) {
                unset($filters[$key]);
            }
        }

        if (
            ! empty($filters['date_from'])
            && ! empty($filters['date_to'])
            && $filters['date_from'] > $filters['date_to']
        ) {
            [$filters['date_from'], $filters['date_to']] = [$filters['date_to'], $filters['date_from']];
        }

        return $filters;
    }
}
