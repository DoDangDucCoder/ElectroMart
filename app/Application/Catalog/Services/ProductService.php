<?php

namespace App\Application\Catalog\Services;

use App\Domain\Catalog\Repositories\ProductRepositoryInterface;
use App\Application\Shared\Services\CacheService;
use App\Application\Shared\Services\ProductSearchService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ProductPricingService $pricing,
        private readonly ProductSearchService $search,
        private readonly ProductRecommendationService $recommendations,
        private readonly ProductViewTrackingService $productViews,
        private readonly ProductDiscoveryTrackingService $discoveryTracking,
        private readonly CacheService $cache
    ) {
    }

    public function getLatestProducts(int $limit = 6): Collection
    {
        return $this->cache->getLatestProducts($limit);
    }

    public function findProductById(int $id): ?Product
    {
        return $this->cache->getProductById($id);
    }

    public function getProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->products->paginateProducts($filters, $perPage);
    }

    public function getProductsByCategorySlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->cache->getProductsByCategory($this->normalizeStorefrontCategorySlug($slug), $filters, $perPage);
    }

    public function getProductsByBrandSlug(string $slug, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->cache->getProductsByBrand($slug, $filters, $perPage);
    }

    public function findProductBySlug(string $slug): ?Product
    {
        return $this->cache->getProductBySlug($slug);
    }

    public function incrementProductViews(Product $product): void
    {
        $this->products->incrementViews($product);
    }

    public function recordProductView(
        Product $product,
        ?int $customerId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        $this->productViews->track($product, $customerId, $ipAddress, $userAgent);
    }

    public function getRelatedProducts(Product $product, int $limit = 6): Collection
    {
        return $this->products->relatedProducts($product, $limit);
    }

    public function getProductsByTag(string $tag, int $limit = 12): Collection
    {
        return $this->products->productsByTag($tag, $limit);
    }

    public function getProductsByCategoryIds(array $ids, int $limit = 12): Collection
    {
        return $this->products->productsByCategoryIds($ids, $limit);
    }

    public function getCategoryPriceRange(string $slug): array
    {
        return $this->products->priceRangeByCategorySlug($this->normalizeStorefrontCategorySlug($slug));
    }

    protected function normalizeStorefrontCategorySlug(string $slug): string
    {
        return [
            'may-tinh' => 'may-tinh-bang',
            'tablet' => 'may-tinh-bang',
            'audio' => 'am-thanh',
            'smartwatch' => 'dong-ho-thong-minh',
            'gaming' => 'laptop-gaming',
        ][$slug] ?? $slug;
    }

    public function loadMoreProducts(?int $afterId, int $limit = 6): Collection
    {
        return $this->products->latestAfterId($afterId, $limit);
    }

    public function getRandomProducts(int $perPage = 6): LengthAwarePaginator
    {
        return $this->products->randomActivePaginate($perPage);
    }

    public function searchProducts(string $keyword, ?int $limit = null): Collection
    {
        return $this->search->searchLimited([
            'keyword' => $keyword,
            'sort' => 'popular',
        ], $limit ?? 12);
    }

    public function searchCatalog(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->search->searchCatalog($filters, $perPage);
    }

    public function searchSuggestions(string $keyword, int $limit = 8): Collection
    {
        return $this->search->getSuggestions($keyword)->take($limit)->values();
    }

    public function getRecommendedProducts(
        ?int $customerId = null,
        ?string $ipAddress = null,
        ?int $excludeProductId = null,
        int $limit = 6,
        string $context = 'storefront'
    ): array {
        $key = 'storefront_recommendations_'.md5(json_encode([
            'customer_id' => $customerId,
            'ip_address' => $customerId ? null : $ipAddress,
            'exclude_product_id' => $excludeProductId,
            'limit' => $limit,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $recommendationData = $this->cache->remember($key, function () use ($customerId, $ipAddress, $excludeProductId, $limit) {
            return $this->recommendations->recommendForStorefront($customerId, $ipAddress, $excludeProductId, $limit);
        }, 300);

        $this->discoveryTracking->trackRecommendationImpressions(
            $recommendationData['products'],
            $customerId,
            $ipAddress,
            $context,
            $recommendationData['sources']
        );

        return $recommendationData;
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['only_active'] = $filters['only_active'] ?? false;

        return $this->products->paginateProducts($filters, $perPage);
    }

    public function createProduct(array $data, ?UploadedFile $image = null, ?UploadedFile $document = null): Product
    {
        return DB::transaction(function () use ($data, $image, $document) {
            $pricingPayload = $this->pricing->derivePricingPayload(
                (float) ($data['price'] ?? 0),
                $data['sale_type'] ?? null,
                $data['sale_value'] ?? null,
                $data['sale_price'] ?? null
            );

            $meta = $this->prepareMeta($data, []);

            if ($document) {
                $documentPath = $this->storeUploadedFile($document, 'uploads/document');
                $meta['document_file'] = basename($documentPath);
                $meta['document_path'] = $documentPath;
            }

            $imagePath = $image ? $this->storeUploadedFile($image, 'uploads/product') : null;

            $payload = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'sku' => $data['sku'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'unit' => $data['unit'] ?? null,
                'description' => $data['description'] ?? null,
                'content' => $data['content'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'primary_category_id' => $data['primary_category_id'] ?? null,
                'price' => $data['price'],
                'sale_type' => $pricingPayload['sale_type'],
                'sale_value' => $pricingPayload['sale_value'],
                'sale_price' => $pricingPayload['sale_price'],
                'cost_price' => $data['cost_price'] ?? null,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'stock_alert_threshold' => $data['stock_alert_threshold'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'display_order' => $data['display_order'] ?? 0,
                'thumbnail_path' => $imagePath,
                'meta' => $this->cleanMeta($meta),
                'meta_title' => $data['meta_title'] ?? $data['name'],
                'meta_description' => $data['meta_description'] ?? ($data['description'] ?? null),
            ];

            /** @var Product $product */
            $product = $this->products->create($payload);

            $this->syncCategories($product, $data['category_ids'] ?? []);

            if ($imagePath) {
                $product->media()->create([
                    'file_path' => $imagePath,
                    'disk' => 'public',
                    'collection' => 'gallery',
                    'display_order' => 0,
                    'meta' => null,
                ]);

                $this->replicateToLegacyGallery($imagePath);
            }

            $product = $product->fresh(['brand', 'categories', 'media']);
            $this->cache->invalidateProductCache($product->id);

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null, ?UploadedFile $document = null): Product
    {
        $cacheContext = $this->captureCacheContext($product);

        $updated = DB::transaction(function () use ($product, $data, $image, $document) {
            $resolvedPrice = (float) ($data['price'] ?? $product->price);
            $pricingPayload = $this->pricing->derivePricingPayload(
                $resolvedPrice,
                $data['sale_type'] ?? $product->sale_type,
                array_key_exists('sale_value', $data) ? $data['sale_value'] : $product->sale_value,
                array_key_exists('sale_price', $data) ? $data['sale_price'] : null
            );

            $meta = $this->prepareMeta($data, $product->meta ?? []);

            if ($document) {
                if (! empty($meta['document_path'])) {
                    $this->deleteFileIfExists($meta['document_path']);
                }

                $documentPath = $this->storeUploadedFile($document, 'uploads/document');
                $meta['document_file'] = basename($documentPath);
                $meta['document_path'] = $documentPath;
            } elseif (! empty($data['remove_document'])) {
                if (! empty($meta['document_path'])) {
                    $this->deleteFileIfExists($meta['document_path']);
                }

                unset($meta['document_file'], $meta['document_path']);
            }

            $payload = [
                'name' => $data['name'] ?? $product->name,
                'slug' => $data['slug'] ?? $product->slug,
                'sku' => $data['sku'] ?? $product->sku,
                'barcode' => $data['barcode'] ?? $product->barcode,
                'unit' => $data['unit'] ?? $product->unit,
                'description' => $data['description'] ?? $product->description,
                'content' => $data['content'] ?? $product->content,
                'brand_id' => $data['brand_id'] ?? $product->brand_id,
                'primary_category_id' => $data['primary_category_id'] ?? $product->primary_category_id,
                'price' => $resolvedPrice,
                'sale_type' => $pricingPayload['sale_type'],
                'sale_value' => $pricingPayload['sale_value'],
                'sale_price' => $pricingPayload['sale_price'],
                'cost_price' => $data['cost_price'] ?? $product->cost_price,
                'stock_quantity' => $data['stock_quantity'] ?? $product->stock_quantity,
                'stock_alert_threshold' => $data['stock_alert_threshold'] ?? $product->stock_alert_threshold,
                'is_active' => $data['is_active'] ?? $product->is_active,
                'is_featured' => $data['is_featured'] ?? $product->is_featured,
                'display_order' => $data['display_order'] ?? $product->display_order,
                'meta' => $this->cleanMeta($meta),
                'meta_title' => $data['meta_title'] ?? $product->meta_title,
                'meta_description' => $data['meta_description'] ?? $product->meta_description,
            ];

            if ($image) {
                if (! empty($product->thumbnail_path)) {
                    $this->deleteFileIfExists($product->thumbnail_path);
                }

                $imagePath = $this->storeUploadedFile($image, 'uploads/product');
                $payload['thumbnail_path'] = $imagePath;

                $product->media()->create([
                    'file_path' => $imagePath,
                    'disk' => 'public',
                    'collection' => 'gallery',
                    'display_order' => 0,
                    'meta' => null,
                ]);

                $this->replicateToLegacyGallery($imagePath);
            }

            $updated = $this->products->update($product, $payload);

            if (! empty($data['category_ids'])) {
                $this->syncCategories($updated, $data['category_ids']);
            }

            return $updated->fresh(['brand', 'categories', 'media']);
        });

        $this->cache->invalidateProductCache($updated->id, $cacheContext);

        return $updated;
    }

    public function setProductStatus(Product $product, bool $isActive): Product
    {
        $updated = $this->products->setStatus($product, $isActive);
        $this->cache->invalidateProductCache($updated->id);

        return $updated;
    }

    public function deleteProduct(Product $product): void
    {
        $cacheContext = $this->captureCacheContext($product);

        DB::transaction(function () use ($product) {
            $meta = $product->meta ?? [];

            if (! empty($product->thumbnail_path)) {
                $this->deleteFileIfExists($product->thumbnail_path);
            }

            foreach ($product->media as $media) {
                $this->deleteFileIfExists($media->file_path);
            }

            if (! empty($meta['document_path'])) {
                $this->deleteFileIfExists($meta['document_path']);
            }

            $product->media()->delete();
            $product->categories()->detach();

            $this->products->delete($product);
        });

        $this->cache->invalidateProductCache($product->id, $cacheContext);
    }

    public function removeProductDocument(Product $product): Product
    {
        $meta = $product->meta ?? [];

        if (! empty($meta['document_path'])) {
            $this->deleteFileIfExists($meta['document_path']);
        }

        unset($meta['document_path'], $meta['document_file']);

        return $this->products->update($product, [
            'meta' => $this->cleanMeta($meta),
        ]);
    }

    protected function prepareMeta(array $data, array $existing): array
    {
        $meta = $existing;

        if (array_key_exists('tags', $data)) {
            $meta['tags'] = $data['tags'];
        }

        return $meta;
    }

    protected function cleanMeta(array $meta): array
    {
        return array_filter($meta, static function ($value) {
            return $value !== null && $value !== '';
        });
    }

    protected function syncCategories(Product $product, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds));

        if (! empty($ids)) {
            $product->categories()->sync($ids);
            $primaryId = $ids[0];

            if ($product->primary_category_id !== $primaryId) {
                $product->primary_category_id = $primaryId;
                $product->save();
            }
        }
    }

    protected function storeUploadedFile(UploadedFile $file, string $directory): string
    {
        $targetDirectory = trim($directory, '/');
        $fullDirectory = public_path($targetDirectory);

        File::ensureDirectoryExists($fullDirectory);

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitized = Str::slug(Str::limit($originalName, 40, ''));
        $extension = $file->getClientOriginalExtension();
        $filename = $sanitized !== '' ? $sanitized : Str::random(8);
        $filename .= '-'.Str::lower(Str::random(6)).'.'.$extension;

        $file->move($fullDirectory, $filename);

        return $targetDirectory.'/'.$filename;
    }

    protected function deleteFileIfExists(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $galleryCopy = public_path('uploads/gallery/' . basename($relativePath));

        if (File::exists($galleryCopy)) {
            File::delete($galleryCopy);
        }
    }

    protected function replicateToLegacyGallery(string $imagePath): void
    {
        $galleryDirectory = public_path('uploads/gallery');
        File::ensureDirectoryExists($galleryDirectory);

        $source = public_path($imagePath);
        $destination = $galleryDirectory.'/'.basename($imagePath);

        if (File::exists($source)) {
            File::copy($source, $destination);
        }
    }

    protected function captureCacheContext(Product $product): array
    {
        $product->loadMissing(['brand', 'categories']);

        return [
            'product_slugs' => array_filter([$product->slug]),
            'category_slugs' => $product->categories->pluck('slug')->filter()->values()->all(),
            'brand_slugs' => array_filter([$product->brand?->slug]),
        ];
    }
}
