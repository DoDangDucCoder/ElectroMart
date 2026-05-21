<?php

namespace App\Application\Shared\Services;

use App\Support\ProductMediaQuality;
use Illuminate\Cache\TaggableStore;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 86400; // 24 hours
    private const SHORT_TTL = 300; // 5 minutes
    private const REGISTRY_LATEST_PRODUCTS = 'cache_registry.latest_products';
    private const REGISTRY_FEATURED_PRODUCTS = 'cache_registry.featured_products';
    private const REGISTRY_PUBLIC_PRODUCTS = 'cache_registry.public_products';
    private const REGISTRY_CATEGORY_PRODUCTS_PREFIX = 'cache_registry.category_products.';
    private const REGISTRY_BRAND_PRODUCTS_PREFIX = 'cache_registry.brand_products.';
    private const REGISTRY_CATEGORY_PRODUCT_REGISTRIES = 'cache_registry.category_products.index';
    private const REGISTRY_BRAND_PRODUCT_REGISTRIES = 'cache_registry.brand_products.index';

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

    public function tags(array $tags): \Illuminate\Contracts\Cache\Repository
    {
        if ($this->supportsTags()) {
            return Cache::tags($tags);
        }

        return Cache::store();
    }

    // Product-related cache methods
    public function getProductsByCategory(string $categorySlug, array $filters = [], int $perPage = 12): mixed
    {
        $page = $this->currentPage();
        $key = "products_category_{$categorySlug}_" . $this->hashFilters($filters) . "_{$perPage}_page_{$page}";
        $registryKey = $this->categoryProductsRegistryKey($categorySlug);

        $this->trackCacheKey(self::REGISTRY_CATEGORY_PRODUCT_REGISTRIES, $registryKey);

        return $this->rememberIndexed($registryKey, $key, function () use ($categorySlug, $filters, $perPage) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->paginateByCategorySlug($categorySlug, $filters, $perPage);
        }, self::DEFAULT_TTL);
    }

    public function getProductsForApi(array $filters = [], int $perPage = 12): mixed
    {
        $page = $this->currentPage();
        $key = "api_products_" . $this->hashFilters($filters) . "_{$perPage}_page_{$page}";

        return $this->rememberIndexed(self::REGISTRY_PUBLIC_PRODUCTS, $key, function () use ($filters, $perPage) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->paginateProducts($filters, $perPage);
        }, self::SHORT_TTL);
    }

    public function getProductsByBrand(string $brandSlug, array $filters = [], int $perPage = 12): mixed
    {
        $page = $this->currentPage();
        $key = "products_brand_{$brandSlug}_" . $this->hashFilters($filters) . "_{$perPage}_page_{$page}";
        $registryKey = $this->brandProductsRegistryKey($brandSlug);

        $this->trackCacheKey(self::REGISTRY_BRAND_PRODUCT_REGISTRIES, $registryKey);

        return $this->rememberIndexed($registryKey, $key, function () use ($brandSlug, $filters, $perPage) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->paginateByBrandSlug($brandSlug, $filters, $perPage);
        }, self::DEFAULT_TTL);
    }

    public function getLatestProducts(int $limit = 6): mixed
    {
        $key = "latest_products_{$limit}";

        return $this->rememberIndexed(self::REGISTRY_LATEST_PRODUCTS, $key, function () use ($limit) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->latestProducts($limit);
        }, self::SHORT_TTL);
    }

    public function getFeaturedProducts(int $limit = 8): mixed
    {
        $page = $this->currentPage();
        $key = "featured_products_{$limit}_page_{$page}";

        return $this->rememberIndexed(self::REGISTRY_FEATURED_PRODUCTS, $key, function () use ($limit) {
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

    public function getProductById(int $id): mixed
    {
        $key = "product_id_{$id}";

        return $this->remember($key, function () use ($id) {
            return app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)
                ->findById($id);
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

    public function getBrandBySlug(string $slug, bool $onlyActive = true): mixed
    {
        $suffix = $onlyActive ? 'active' : 'all';
        $key = "brand_slug_{$slug}_{$suffix}";

        return $this->remember($key, function () use ($slug, $onlyActive) {
            return app(\App\Domain\Catalog\Repositories\BrandRepositoryInterface::class)
                ->findBySlug($slug, $onlyActive);
        }, self::LONG_TTL);
    }

    // Settings cache
    public function getSettings(): mixed
    {
        $key = 'all_settings';
        
        return $this->remember($key, function () {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            $query = DB::table('settings');

            if (Schema::hasColumn('settings', 'is_public')) {
                $query->where('is_public', true);
            }

            return $query->pluck('value', 'key')->toArray();
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
                'total_products' => Schema::hasTable('products')
                    ? DB::table('products')->where('is_active', true)->count()
                    : 0,
                'total_categories' => Schema::hasTable('product_categories')
                    ? DB::table('product_categories')->where('is_active', true)->count()
                    : 0,
                'total_brands' => Schema::hasTable('brands')
                    ? DB::table('brands')->where('is_active', true)->count()
                    : 0,
                'total_customers' => Schema::hasTable('customers')
                    ? DB::table('customers')->where('is_active', true)->count()
                    : 0,
                'total_orders' => Schema::hasTable('orders')
                    ? DB::table('orders')->count()
                    : 0,
            ];
        }, self::SHORT_TTL);
    }

    // Cache invalidation methods
    public function invalidateProductCache(int $productId, array $context = []): void
    {
        ProductMediaQuality::clearCache();

        $product = app(\App\Domain\Catalog\Repositories\ProductRepositoryInterface::class)->findById($productId);

        $productSlugs = collect($context['product_slugs'] ?? []);
        $categorySlugs = collect($context['category_slugs'] ?? []);
        $brandSlugs = collect($context['brand_slugs'] ?? []);

        if ($product) {
            $productSlugs->push($product->slug);
            $categorySlugs = $categorySlugs->merge($product->categories->pluck('slug'));
            if ($product->primaryCategory?->slug) {
                $categorySlugs->push($product->primaryCategory->slug);
            }
            if ($product->brand?->slug) {
                $brandSlugs->push($product->brand->slug);
            }
        }

        foreach ($productSlugs->filter()->unique()->values() as $slug) {
            $this->forget("product_slug_{$slug}");
        }

        $this->forget("product_id_{$productId}");

        $this->forgetRegisteredKeys(self::REGISTRY_LATEST_PRODUCTS);
        $this->forgetRegisteredKeys(self::REGISTRY_FEATURED_PRODUCTS);
        $this->forgetRegisteredKeys(self::REGISTRY_PUBLIC_PRODUCTS);
        $this->forget('site_statistics');

        foreach ($categorySlugs->filter()->unique()->values() as $slug) {
            $this->forgetRegisteredKeys($this->categoryProductsRegistryKey($slug));
        }

        foreach ($brandSlugs->filter()->unique()->values() as $slug) {
            $this->forgetRegisteredKeys($this->brandProductsRegistryKey($slug));
        }
    }

    public function invalidateCategoryCache(?int $categoryId = null, array $context = []): void
    {
        $category = $categoryId
            ? app(\App\Domain\Catalog\Repositories\CategoryRepositoryInterface::class)->findById($categoryId)
            : null;

        $categorySlugs = collect($context['category_slugs'] ?? []);

        if ($category) {
            $categorySlugs->push($category->slug);
        }

        $this->forget('all_categories');
        $this->forget('category_tree');
        $this->forget('site_statistics');
        $this->forgetRegisteredKeys(self::REGISTRY_PUBLIC_PRODUCTS);

        foreach ($categorySlugs->filter()->unique()->values() as $slug) {
            $this->forget("category_slug_{$slug}");
            $this->forgetRegisteredKeys($this->categoryProductsRegistryKey($slug));
        }

        $this->forgetNestedRegistries(self::REGISTRY_BRAND_PRODUCT_REGISTRIES);
    }

    public function invalidateBrandCache(?int $brandId = null, array $context = []): void
    {
        $brand = $brandId
            ? app(\App\Domain\Catalog\Repositories\BrandRepositoryInterface::class)->findById($brandId)
            : null;

        $brandSlugs = collect($context['brand_slugs'] ?? []);

        if ($brand?->slug) {
            $brandSlugs->push($brand->slug);
        }

        $this->forget('all_brands');
        $this->forget('site_statistics');
        $this->forgetRegisteredKeys(self::REGISTRY_PUBLIC_PRODUCTS);

        foreach ($brandSlugs->filter()->unique()->values() as $slug) {
            $this->forget("brand_slug_{$slug}_active");
            $this->forget("brand_slug_{$slug}_all");
            $this->forgetRegisteredKeys($this->brandProductsRegistryKey($slug));
        }

        $this->forgetNestedRegistries(self::REGISTRY_CATEGORY_PRODUCT_REGISTRIES);
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

    protected function rememberIndexed(string $registryKey, string $key, callable $callback, int $ttl): mixed
    {
        $value = $this->remember($key, $callback, $ttl);
        $this->trackCacheKey($registryKey, $key);

        return $value;
    }

    protected function trackCacheKey(string $registryKey, string $key): void
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

    protected function forgetRegisteredKeys(string $registryKey): void
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

    protected function forgetNestedRegistries(string $registryIndexKey): void
    {
        $registries = Cache::get($registryIndexKey, []);

        if (! is_array($registries)) {
            $registries = [];
        }

        foreach (array_unique($registries) as $registryKey) {
            if (is_string($registryKey) && $registryKey !== '') {
                $this->forgetRegisteredKeys($registryKey);
            }
        }

        Cache::forget($registryIndexKey);
    }

    protected function categoryProductsRegistryKey(string $categorySlug): string
    {
        return self::REGISTRY_CATEGORY_PRODUCTS_PREFIX . $categorySlug;
    }

    protected function brandProductsRegistryKey(string $brandSlug): string
    {
        return self::REGISTRY_BRAND_PRODUCTS_PREFIX . $brandSlug;
    }

    protected function hashFilters(array $filters): string
    {
        $normalized = $filters;
        ksort($normalized);

        return md5(serialize($normalized));
    }

    protected function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

    protected function currentPage(): int
    {
        return max(1, (int) Paginator::resolveCurrentPage('page'));
    }
}
