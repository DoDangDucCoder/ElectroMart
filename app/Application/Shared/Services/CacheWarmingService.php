<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Application\Shared\Services\EnhancedCacheService;

class CacheWarmingService
{
    private EnhancedCacheService $cacheService;
    private array $warmingStrategies = [];

    public function __construct(EnhancedCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->initializeWarmingStrategies();
    }

    private function initializeWarmingStrategies(): void
    {
        $this->warmingStrategies = [
            'critical' => [
                'description' => 'Critical data that must be cached immediately',
                'priority' => 1,
                'methods' => [
                    'warmUpCriticalData',
                ],
            ],
            'frequent' => [
                'description' => 'Frequently accessed data',
                'priority' => 2,
                'methods' => [
                    'warmUpFrequentData',
                ],
            ],
            'popular' => [
                'description' => 'Popular content and trending data',
                'priority' => 3,
                'methods' => [
                    'warmUpPopularData',
                ],
            ],
            'statistics' => [
                'description' => 'Statistics and analytics data',
                'priority' => 4,
                'methods' => [
                    'warmUpStatisticsData',
                ],
            ],
            'background' => [
                'description' => 'Background data that can be warmed up later',
                'priority' => 5,
                'methods' => [
                    'warmUpBackgroundData',
                ],
            ],
        ];
    }

    public function warmUpAll(): void
    {
        Log::info('Starting comprehensive cache warming...');

        foreach ($this->warmingStrategies as $strategy => $config) {
            $this->warmUpStrategy($strategy);
        }

        Log::info('Comprehensive cache warming completed');
    }

    public function warmUpStrategy(string $strategy): void
    {
        if (!isset($this->warmingStrategies[$strategy])) {
            Log::warning("Unknown warming strategy: {$strategy}");
            return;
        }

        $config = $this->warmingStrategies[$strategy];
        Log::info("Warming up strategy: {$strategy} - {$config['description']}");

        foreach ($config['methods'] as $method) {
            if (method_exists($this, $method)) {
                try {
                    $this->$method();
                } catch (\Exception $e) {
                    Log::error("Failed to execute warming method {$method}", [
                        'strategy' => $strategy,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    private function warmUpCriticalData(): void
    {
        Log::info('Warming up critical data...');

        // Essential navigation data
        $this->cacheService->getCategoriesTree();
        $this->cacheService->getActiveCategories();
        $this->cacheService->getActiveBrands();

        // Homepage data
        $this->cacheService->getLatestProducts(6);
        $this->cacheService->getFeaturedProducts(6);

        Log::info('Critical data warming completed');
    }

    private function warmUpFrequentData(): void
    {
        Log::info('Warming up frequent data...');

        // Product listings
        $this->cacheService->getLatestProducts(12);
        $this->cacheService->getLatestProducts(24);
        $this->cacheService->getFeaturedProducts(12);
        $this->cacheService->getFeaturedProducts(24);

        // Category-based products
        $categories = $this->getActiveCategories();
        foreach ($categories as $category) {
            $this->cacheService->getProductsByCategory($category->slug, [], 12);
            $this->cacheService->getProductsByCategory($category->slug, [], 24);
        }

        Log::info('Frequent data warming completed');
    }

    private function warmUpPopularData(): void
    {
        Log::info('Warming up popular data...');

        // Popular products by views
        $this->warmUpPopularProducts();

        // Trending categories
        $this->warmUpTrendingCategories();

        // Recent blog posts
        $this->warmUpRecentBlogPosts();

        Log::info('Popular data warming completed');
    }

    private function warmUpStatisticsData(): void
    {
        Log::info('Warming up statistics data...');

        // Sales statistics
        $this->cacheService->getSalesStatistics('day');
        $this->cacheService->getSalesStatistics('week');
        $this->cacheService->getSalesStatistics('month');
        $this->cacheService->getSalesStatistics('year');

        // Product statistics
        $this->cacheService->getProductStatistics();

        // Customer statistics
        $this->warmUpCustomerStatistics();

        Log::info('Statistics data warming completed');
    }

    private function warmUpBackgroundData(): void
    {
        Log::info('Warming up background data...');

        // Archive data
        $this->warmUpArchiveData();

        // Search suggestions
        $this->warmUpSearchSuggestions();

        // Related products
        $this->warmUpRelatedProducts();

        Log::info('Background data warming completed');
    }

    private function warmUpPopularProducts(): void
    {
        try {
            // Get most viewed products
            $popularProducts = DB::table('products')
                ->where('is_active', true)
                ->orderBy('views_count', 'desc')
                ->limit(20)
                ->get();

            foreach ($popularProducts as $product) {
                $this->cacheService->getProductBySlug($product->slug);
            }
        } catch (\Exception $e) {
            Log::error('Failed to warm up popular products', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpTrendingCategories(): void
    {
        try {
            // Get categories with most products
            $trendingCategories = DB::table('product_categories')
                ->join('products', 'product_categories.id', '=', 'products.primary_category_id')
                ->where('product_categories.is_active', true)
                ->where('products.is_active', true)
                ->select('product_categories.*')
                ->groupBy('product_categories.id')
                ->orderByRaw('COUNT(products.id) DESC')
                ->limit(10)
                ->get();

            foreach ($trendingCategories as $category) {
                $this->cacheService->getProductsByCategory($category->slug, [], 12);
            }
        } catch (\Exception $e) {
            Log::error('Failed to warm up trending categories', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpRecentBlogPosts(): void
    {
        try {
            // Get recent blog posts
            $recentPosts = DB::table('blog_posts')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($recentPosts as $post) {
                // Cache individual blog post
                $this->cacheService->remember("blog_post_{$post->slug}", function () use ($post) {
                    return $post;
                }, 3600);
            }
        } catch (\Exception $e) {
            Log::error('Failed to warm up recent blog posts', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpCustomerStatistics(): void
    {
        try {
            // Customer statistics
            $customerStats = [
                'total_customers' => DB::table('customers')->count(),
                'active_customers' => DB::table('customers')->where('is_active', true)->count(),
                'new_customers_this_month' => DB::table('customers')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
            ];

            $this->cacheService->remember('customer_statistics', function () use ($customerStats) {
                return $customerStats;
            }, 1800); // 30 minutes
        } catch (\Exception $e) {
            Log::error('Failed to warm up customer statistics', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpArchiveData(): void
    {
        try {
            // Archive data for older content
            $archiveData = [
                'oldest_products' => DB::table('products')
                    ->where('is_active', true)
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->get(),
                'oldest_orders' => DB::table('orders')
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->get(),
            ];

            $this->cacheService->remember('archive_data', function () use ($archiveData) {
                return $archiveData;
            }, 7200); // 2 hours
        } catch (\Exception $e) {
            Log::error('Failed to warm up archive data', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpSearchSuggestions(): void
    {
        try {
            // Popular search terms
            $searchSuggestions = [
                'product_names' => DB::table('products')
                    ->where('is_active', true)
                    ->pluck('name')
                    ->take(50)
                    ->toArray(),
                'category_names' => DB::table('product_categories')
                    ->where('is_active', true)
                    ->pluck('name')
                    ->take(20)
                    ->toArray(),
                'brand_names' => DB::table('brands')
                    ->where('is_active', true)
                    ->pluck('name')
                    ->take(20)
                    ->toArray(),
            ];

            $this->cacheService->remember('search_suggestions', function () use ($searchSuggestions) {
                return $searchSuggestions;
            }, 3600);
        } catch (\Exception $e) {
            Log::error('Failed to warm up search suggestions', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpRelatedProducts(): void
    {
        try {
            // Get products with related products
            $products = DB::table('products')
                ->where('is_active', true)
                ->limit(20)
                ->get();

            foreach ($products as $product) {
                // Cache related products for each product
                $this->cacheService->remember("related_products_{$product->id}", function () use ($product) {
                    return DB::table('products')
                        ->where('primary_category_id', $product->primary_category_id)
                        ->where('id', '!=', $product->id)
                        ->where('is_active', true)
                        ->limit(6)
                        ->get();
                }, 1800);
            }
        } catch (\Exception $e) {
            Log::error('Failed to warm up related products', ['error' => $e->getMessage()]);
        }
    }

    private function getActiveCategories()
    {
        try {
            return DB::table('product_categories')
                ->where('is_active', true)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get active categories', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    public function warmUpOnSchedule(): void
    {
        Log::info('Starting scheduled cache warming...');

        // Warm up critical and frequent data on schedule
        $this->warmUpStrategy('critical');
        $this->warmUpStrategy('frequent');

        Log::info('Scheduled cache warming completed');
    }

    public function warmUpOnDemand(string $type, array $params = []): void
    {
        Log::info("Starting on-demand cache warming for type: {$type}");

        switch ($type) {
            case 'product':
                $this->warmUpProductData($params);
                break;
            case 'category':
                $this->warmUpCategoryData($params);
                break;
            case 'customer':
                $this->warmUpCustomerData($params);
                break;
            default:
                Log::warning("Unknown on-demand warming type: {$type}");
        }

        Log::info("On-demand cache warming completed for type: {$type}");
    }

    private function warmUpProductData(array $params): void
    {
        if (isset($params['product_id'])) {
            $product = DB::table('products')->find($params['product_id']);
            if ($product) {
                $this->cacheService->getProductBySlug($product->slug);
            }
        }
    }

    private function warmUpCategoryData(array $params): void
    {
        if (isset($params['category_slug'])) {
            $this->cacheService->getProductsByCategory($params['category_slug'], [], 12);
        }
    }

    private function warmUpCustomerData(array $params): void
    {
        if (isset($params['customer_id'])) {
            $this->cacheService->getCustomerOrders($params['customer_id'], 10);
        }
    }

    public function getWarmingStatistics(): array
    {
        return [
            'strategies' => array_keys($this->warmingStrategies),
            'total_strategies' => count($this->warmingStrategies),
            'cache_service_stats' => $this->cacheService->getCacheStatistics(),
        ];
    }
}
