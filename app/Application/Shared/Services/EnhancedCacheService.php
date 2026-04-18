<?php

namespace App\Application\Shared\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnhancedCacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 86400; // 24 hours
    private const SHORT_TTL = 300; // 5 minutes
    private const VERY_SHORT_TTL = 60; // 1 minute

    // Cache drivers
    private const DRIVER_REDIS = 'redis';
    private const DRIVER_FILE = 'file';
    private const DRIVER_DATABASE = 'database';
    private const REGISTRY_PRODUCTS = 'enhanced_cache_registry.products';
    private const REGISTRY_CATEGORIES = 'enhanced_cache_registry.categories';
    private const REGISTRY_ORDERS = 'enhanced_cache_registry.orders';

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

    public function tags(array $tags): CacheRepository
    {
        if ($this->supportsTags()) {
            return Cache::tags($tags);
        }

        return Cache::store();
    }

    // Multi-level caching
    public function getMultiLevel(string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        // Level 1: Memory cache
        if ($this->hasMemoryCache($key)) {
            return $this->getMemoryCache($key);
        }

        // Level 2: Redis cache
        if ($this->hasRedisCache($key)) {
            $data = $this->getRedisCache($key);
            $this->setMemoryCache($key, $data);
            return $data;
        }

        // Level 3: Database/File cache
        if ($this->hasFileCache($key)) {
            $data = $this->getFileCache($key);
            $this->setRedisCache($key, $data, $ttl);
            $this->setMemoryCache($key, $data);
            return $data;
        }

        // Level 4: Execute callback
        $data = $callback();
        $this->setFileCache($key, $data, $ttl);
        $this->setRedisCache($key, $data, $ttl);
        $this->setMemoryCache($key, $data);

        return $data;
    }

    // Product-related cache methods
    public function getProductsByCategory(string $categorySlug, array $filters = [], int $perPage = 12): mixed
    {
        $key = "products_category_{$categorySlug}_" . md5(serialize($filters)) . "_{$perPage}";

        return $this->rememberIndexed(self::REGISTRY_PRODUCTS, $key, function () use ($categorySlug, $filters, $perPage) {
            // Mock implementation - replace with actual repository method
            return DB::table('products')
                ->join('product_categories', 'products.primary_category_id', '=', 'product_categories.id')
                ->where('product_categories.slug', $categorySlug)
                ->where('products.is_active', true)
                ->limit($perPage)
                ->get();
        }, self::DEFAULT_TTL);
    }

    public function getLatestProducts(int $limit = 6): mixed
    {
        $key = "products_latest_{$limit}";

        return $this->rememberIndexed(self::REGISTRY_PRODUCTS, $key, function () use ($limit) {
            // Mock implementation - replace with actual repository method
            return DB::table('products')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, self::SHORT_TTL);
    }

    public function getFeaturedProducts(int $limit = 6): mixed
    {
        $key = "products_featured_{$limit}";

        return $this->rememberIndexed(self::REGISTRY_PRODUCTS, $key, function () use ($limit) {
            // Mock implementation - replace with actual repository method
            return DB::table('products')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, self::DEFAULT_TTL);
    }

    public function getProductBySlug(string $slug): mixed
    {
        $key = "product_slug_{$slug}";

        return $this->rememberIndexed(self::REGISTRY_PRODUCTS, $key, function () use ($slug) {
            // Mock implementation - replace with actual repository method
            return DB::table('products')
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();
        }, self::LONG_TTL);
    }

    // Category-related cache methods
    public function getCategoriesTree(): mixed
    {
        $key = "categories_tree";

        return $this->rememberIndexed(self::REGISTRY_CATEGORIES, $key, function () {
            // Mock implementation - replace with actual repository method
            return DB::table('product_categories')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }, self::LONG_TTL);
    }

    public function getActiveCategories(): mixed
    {
        $key = "categories_active";

        return $this->rememberIndexed(self::REGISTRY_CATEGORIES, $key, function () {
            // Mock implementation - replace with actual repository method
            return DB::table('product_categories')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }, self::DEFAULT_TTL);
    }

    // Brand-related cache methods
    public function getActiveBrands(): mixed
    {
        $key = "brands_active";
        
        return $this->getMultiLevel($key, function () {
            // Mock implementation - replace with actual repository method
            return DB::table('brands')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }, self::DEFAULT_TTL);
    }

    // Order-related cache methods
    public function getCustomerOrders(int $customerId, int $perPage = 10): mixed
    {
        $key = "customer_orders_{$customerId}_{$perPage}";

        return $this->rememberIndexed(self::REGISTRY_ORDERS, $key, function () use ($customerId, $perPage) {
            // Mock implementation - replace with actual repository method
            return DB::table('orders')
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->limit($perPage)
                ->get();
        }, self::SHORT_TTL);
    }

    // Statistics cache methods
    public function getSalesStatistics(string $period = 'month'): mixed
    {
        $key = "statistics_sales_{$period}";
        
        return $this->getMultiLevel($key, function () use ($period) {
            // Mock implementation - replace with actual service
            return [
                'period' => $period,
                'total_sales' => 0,
                'total_orders' => 0,
                'average_order_value' => 0
            ];
        }, self::VERY_SHORT_TTL);
    }

    public function getProductStatistics(): mixed
    {
        $key = "statistics_products";
        
        return $this->getMultiLevel($key, function () {
            // Mock implementation - replace with actual service
            return [
                'total_products' => 0,
                'active_products' => 0,
                'low_stock_products' => 0
            ];
        }, self::VERY_SHORT_TTL);
    }

    // Cache warming methods
    public function warmUpCache(): void
    {
        Log::info('Starting cache warming...');

        try {
            // Warm up product caches
            $this->warmUpProductCache();
            
            // Warm up category caches
            $this->warmUpCategoryCache();
            
            // Warm up brand caches
            $this->warmUpBrandCache();
            
            // Warm up statistics caches
            $this->warmUpStatisticsCache();
            
            Log::info('Cache warming completed successfully');
        } catch (\Exception $e) {
            Log::error('Cache warming failed', ['error' => $e->getMessage()]);
        }
    }

    private function warmUpProductCache(): void
    {
        // Warm up latest products
        $this->getLatestProducts(6);
        $this->getLatestProducts(12);
        
        // Warm up featured products
        $this->getFeaturedProducts(6);
        $this->getFeaturedProducts(12);
        
        // Warm up products by category
        $categories = $this->getActiveCategories();
        if ($categories) {
            foreach ($categories as $category) {
                $this->getProductsByCategory($category->slug, [], 12);
            }
        }
    }

    private function warmUpCategoryCache(): void
    {
        $this->getCategoriesTree();
        $this->getActiveCategories();
    }

    private function warmUpBrandCache(): void
    {
        $this->getActiveBrands();
    }

    private function warmUpStatisticsCache(): void
    {
        $this->getSalesStatistics('day');
        $this->getSalesStatistics('week');
        $this->getSalesStatistics('month');
        $this->getProductStatistics();
    }

    // Cache invalidation methods
    public function invalidateProductCache(int $productId = null): void
    {
        $tags = ['products'];
        if ($productId) {
            $tags[] = "product_{$productId}";
        }

        $this->flushTaggedCache($tags, 'Product cache invalidated', ['product_id' => $productId]);
    }

    public function invalidateCategoryCache(int $categoryId = null): void
    {
        $tags = ['categories'];
        if ($categoryId) {
            $tags[] = "category_{$categoryId}";
        }

        $this->flushTaggedCache($tags, 'Category cache invalidated', ['category_id' => $categoryId]);
    }

    public function invalidateOrderCache(int $customerId = null): void
    {
        $tags = ['orders'];
        if ($customerId) {
            $tags[] = "customer_{$customerId}";
        }

        $this->flushTaggedCache($tags, 'Order cache invalidated', ['customer_id' => $customerId]);
    }

    // Memory cache methods
    private array $memoryCache = [];

    private function hasMemoryCache(string $key): bool
    {
        return isset($this->memoryCache[$key]) && 
               $this->memoryCache[$key]['expires_at'] > time();
    }

    private function getMemoryCache(string $key)
    {
        return $this->memoryCache[$key]['data'] ?? null;
    }

    private function setMemoryCache(string $key, $data): void
    {
        $this->memoryCache[$key] = [
            'data' => $data,
            'expires_at' => time() + self::VERY_SHORT_TTL
        ];
    }

    // Redis cache methods
    private function hasRedisCache(string $key): bool
    {
        try {
            if (!class_exists('Redis')) {
                return false;
            }
            return \Illuminate\Support\Facades\Redis::exists($key) > 0;
        } catch (\Exception $e) {
            Log::warning('Redis cache check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function getRedisCache(string $key)
    {
        try {
            if (!class_exists('Redis')) {
                return null;
            }
            $data = \Illuminate\Support\Facades\Redis::get($key);
            return $data ? unserialize($data) : null;
        } catch (\Exception $e) {
            Log::warning('Redis cache get failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function setRedisCache(string $key, $data, int $ttl): void
    {
        try {
            if (!class_exists('Redis')) {
                return;
            }
            \Illuminate\Support\Facades\Redis::setex($key, $ttl, serialize($data));
        } catch (\Exception $e) {
            Log::warning('Redis cache set failed', ['error' => $e->getMessage()]);
        }
    }

    // File cache methods
    private function hasFileCache(string $key): bool
    {
        return Cache::store('file')->has($key);
    }

    private function getFileCache(string $key)
    {
        return Cache::store('file')->get($key);
    }

    private function setFileCache(string $key, $data, int $ttl): void
    {
        Cache::store('file')->put($key, $data, $ttl);
    }

    // Cache statistics
    public function getCacheStatistics(): array
    {
        $stats = [
            'memory_cache' => count($this->memoryCache),
            'redis_connected' => $this->isRedisConnected(),
            'file_cache_size' => $this->getFileCacheSize(),
        ];

        if ($this->isRedisConnected()) {
            try {
                $stats['redis_info'] = \Illuminate\Support\Facades\Redis::info();
            } catch (\Exception $e) {
                $stats['redis_error'] = $e->getMessage();
            }
        }

        return $stats;
    }

    private function isRedisConnected(): bool
    {
        try {
            if (!class_exists('Redis')) {
                return false;
            }
            \Illuminate\Support\Facades\Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getFileCacheSize(): int
    {
        $cachePath = storage_path('framework/cache');
        if (!is_dir($cachePath)) {
            return 0;
        }

        $size = 0;
        $files = glob($cachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
            }
        }

        return $size;
    }

    // Cache cleanup
    public function cleanupExpiredCache(): void
    {
        // Clean memory cache
        $now = time();
        foreach ($this->memoryCache as $key => $data) {
            if ($data['expires_at'] <= $now) {
                unset($this->memoryCache[$key]);
            }
        }

        Log::info('Expired cache cleaned up');
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

    private function flushTaggedCache(array $tags, string $message, array $context = []): void
    {
        if ($this->supportsTags()) {
            Cache::tags($tags)->flush();
            Log::info($message, $context + ['tags' => $tags]);

            return;
        }

        foreach ($this->fallbackRegistriesForTags($tags) as $registryKey) {
            $this->forgetRegisteredKeys($registryKey);
        }

        Log::info($message, $context + [
            'tags' => $tags,
            'fallback' => 'scoped_registry_invalidation',
        ]);
    }

    private function rememberIndexed(string $registryKey, string $key, callable $callback, int $ttl): mixed
    {
        $value = $this->getMultiLevel($key, $callback, $ttl);
        $this->trackCacheKey($registryKey, $key);

        return $value;
    }

    private function trackCacheKey(string $registryKey, string $key): void
    {
        $keys = Cache::get($registryKey, []);

        if (! is_array($keys)) {
            $keys = [];
        }

        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            Cache::forever($registryKey, array_values($keys));
        }
    }

    private function forgetRegisteredKeys(string $registryKey): void
    {
        $keys = Cache::get($registryKey, []);

        if (! is_array($keys)) {
            $keys = [];
        }

        foreach (array_unique($keys) as $key) {
            Cache::forget($key);
        }

        Cache::forget($registryKey);
    }

    private function fallbackRegistriesForTags(array $tags): array
    {
        $registries = [];

        foreach ($tags as $tag) {
            if ($tag === 'products' || str_starts_with($tag, 'product_')) {
                $registries[] = self::REGISTRY_PRODUCTS;
            }

            if ($tag === 'categories' || str_starts_with($tag, 'category_')) {
                $registries[] = self::REGISTRY_CATEGORIES;
            }

            if ($tag === 'orders' || str_starts_with($tag, 'customer_')) {
                $registries[] = self::REGISTRY_ORDERS;
            }
        }

        return array_values(array_unique($registries));
    }
}
