<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 86400; // 24 hours
    private const SHORT_TTL = 300; // 5 minutes

    public function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, $callback);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public function flush(): bool
    {
        return Cache::flush();
    }

    public function tags(array $tags): \Illuminate\Cache\TaggedCache
    {
        return Cache::tags($tags);
    }

    // Product-related cache methods
    public function getProductsByCategory(string $categorySlug, array $filters = [], int $perPage = 12): mixed
    {
        $key = "products_category_{$categorySlug}_" . md5(serialize($filters)) . "_{$perPage}";
        
        return $this->remember($key, function () use ($categorySlug, $filters, $perPage) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->paginateByCategorySlug($categorySlug, $filters, $perPage);
        }, self::DEFAULT_TTL);
    }

    public function getLatestProducts(int $limit = 6): mixed
    {
        $key = "latest_products_{$limit}";
        
        return $this->remember($key, function () use ($limit) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->latestProducts($limit);
        }, self::SHORT_TTL);
    }

    public function getFeaturedProducts(int $limit = 8): mixed
    {
        $key = "featured_products_{$limit}";
        
        return $this->remember($key, function () use ($limit) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->paginateProducts(['is_featured' => true], $limit);
        }, self::DEFAULT_TTL);
    }

    public function getProductBySlug(string $slug): mixed
    {
        $key = "product_slug_{$slug}";
        
        return $this->remember($key, function () use ($slug) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->findBySlug($slug);
        }, self::LONG_TTL);
    }

    // Category-related cache methods
    public function getCategories(): mixed
    {
        $key = 'all_categories';
        
        return $this->remember($key, function () {
            return app(\App\Domain\Catalog\Repositories\CategoryRepositoryInterface::class)
                ->all(['is_active' => true]);
        }, self::LONG_TTL);
    }

    public function getCategoryBySlug(string $slug): mixed
    {
        $key = "category_slug_{$slug}";
        
        return $this->remember($key, function () use ($slug) {
            return app(\App\Domain\Catalog\Repositories\CategoryRepositoryInterface::class)
                ->findBySlug($slug, true);
        }, self::LONG_TTL);
    }

    // Brand-related cache methods
    public function getBrands(): mixed
    {
        $key = 'all_brands';
        
        return $this->remember($key, function () {
            return app(\App\Domain\Catalog\Repositories\BrandRepositoryInterface::class)
                ->all(['is_active' => true]);
        }, self::LONG_TTL);
    }

    // Settings cache
    public function getSettings(): mixed
    {
        $key = 'all_settings';
        
        return $this->remember($key, function () {
            return DB::table('settings')
                ->where('is_public', true)
                ->pluck('value', 'key')
                ->toArray();
        }, self::LONG_TTL);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->getSettings();
        return $settings[$key] ?? $default;
    }

    // Statistics cache
    public function getStatistics(): mixed
    {
        $key = 'site_statistics';
        
        return $this->remember($key, function () {
            return [
                'total_products' => DB::table('products')->where('is_active', true)->count(),
                'total_categories' => DB::table('product_categories')->where('is_active', true)->count(),
                'total_brands' => DB::table('brands')->where('is_active', true)->count(),
                'total_customers' => DB::table('customers')->where('is_active', true)->count(),
                'total_orders' => DB::table('orders')->count(),
            ];
        }, self::SHORT_TTL);
    }

    // Cache invalidation methods
    public function invalidateProductCache(int $productId): void
    {
        $product = app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)->findById($productId);
        
        if ($product) {
            $this->forget("product_slug_{$product->slug}");
            $this->forget("latest_products_6");
            $this->forget("featured_products_8");
            
            // Invalidate category caches
            foreach ($product->categories as $category) {
                $this->forget("products_category_{$category->slug}_*");
            }
        }
    }

    public function invalidateCategoryCache(int $categoryId): void
    {
        $category = app(\App\Domain\Catalog\Repositories\CategoryRepositoryInterface::class)->findById($categoryId);
        
        if ($category) {
            $this->forget("category_slug_{$category->slug}");
            $this->forget("all_categories");
            $this->forget("products_category_{$category->slug}_*");
        }
    }

    public function invalidateBrandCache(int $brandId): void
    {
        $brand = app(\App\Domain\Catalog\Repositories\BrandRepositoryInterface::class)->findById($brandId);
        
        if ($brand) {
            $this->forget("all_brands");
            $this->forget("products_brand_{$brand->slug}_*");
        }
    }

    // Cache warming methods
    public function warmUpCache(): void
    {
        Log::info('Starting cache warm-up');
        
        try {
            // Warm up frequently accessed data
            $this->getCategories();
            $this->getBrands();
            $this->getLatestProducts();
            $this->getFeaturedProducts();
            $this->getSettings();
            $this->getStatistics();
            
            Log::info('Cache warm-up completed successfully');
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed: ' . $e->getMessage());
        }
    }

    // Cache health check
    public function getCacheStats(): array
    {
        $stats = [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];

        if (config('cache.default') === 'redis') {
            try {
                $redis = app('redis');
                $info = $redis->info();
                $stats['redis'] = [
                    'used_memory' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 'N/A',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A',
                ];
            } catch (\Exception $e) {
                $stats['redis_error'] = $e->getMessage();
            }
        }

        return $stats;
    }
}
