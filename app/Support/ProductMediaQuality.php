<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductMediaQuality
{
    private const FALLBACK_PRODUCT_IMAGE = 'uploads/product/no-image.svg';

    private static array $auditCache = [];

    private static array $completeIdsCache = [];

    public static function completeProductIds(int $minGalleryImages = 4): array
    {
        $minGalleryImages = max(1, $minGalleryImages);

        if (! isset(self::$completeIdsCache[$minGalleryImages])) {
            self::$completeIdsCache[$minGalleryImages] = self::audit($minGalleryImages)['rows']
                ->filter(fn (array $row) => (bool) $row['is_visible'])
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return self::$completeIdsCache[$minGalleryImages];
    }

    public static function audit(int $minGalleryImages = 4): array
    {
        $minGalleryImages = max(1, $minGalleryImages);

        if (isset(self::$auditCache[$minGalleryImages])) {
            return self::$auditCache[$minGalleryImages];
        }

        if (! Schema::hasTable('products') || ! Schema::hasTable('product_media')) {
            return self::$auditCache[$minGalleryImages] = [
                'rows' => collect(),
                'summary' => self::emptySummary(),
                'errors' => [['message' => 'Missing products or product_media table.']],
            ];
        }

        $products = DB::table('products')
            ->select(['id', 'name', 'slug', 'thumbnail_path', 'is_active'])
            ->orderBy('id')
            ->get();

        $mediaByProduct = DB::table('product_media')
            ->select(['product_id', 'file_path', 'collection'])
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->groupBy('product_id');

        $rows = $products->map(function ($product) use ($mediaByProduct, $minGalleryImages) {
            $mainPath = self::normalizePath($product->thumbnail_path);
            $hasMainImage = self::isValidUploadFile($mainPath);
            $mediaRows = $mediaByProduct->get($product->id, collect());

            $galleryRows = $mediaRows->filter(function ($media) {
                $collection = strtolower(trim((string) $media->collection));

                return ! in_array($collection, ['main', 'thumbnail'], true);
            });

            $existingGallery = $galleryRows
                ->map(fn ($media) => self::normalizePath($media->file_path))
                ->filter(fn (?string $path) => self::isValidUploadFile($path))
                ->unique()
                ->values();

            $brokenPaths = collect([$mainPath])
                ->merge($mediaRows->pluck('file_path')->map(fn ($path) => self::normalizePath($path)))
                ->filter()
                ->unique()
                ->reject(fn (string $path) => self::isValidUploadFile($path))
                ->values();

            $galleryCount = $existingGallery->count();
            $isVisible = $hasMainImage && $galleryCount >= $minGalleryImages;

            return [
                'product_id' => (int) $product->id,
                'slug' => (string) $product->slug,
                'name' => (string) $product->name,
                'is_active' => (bool) $product->is_active,
                'main_image' => $mainPath,
                'has_main_image' => $hasMainImage,
                'existing_gallery_images' => $galleryCount,
                'has_4_gallery_images' => $galleryCount >= 4,
                'has_5_gallery_images' => $galleryCount >= 5,
                'broken_image_files' => $brokenPaths->implode('|'),
                'is_visible' => $isVisible,
                'hidden_reason' => $isVisible ? '' : self::hiddenReason($hasMainImage, $galleryCount, $minGalleryImages, $brokenPaths->isNotEmpty()),
            ];
        });

        $summary = [
            'total_products' => $rows->count(),
            'products_with_main_image' => $rows->where('has_main_image', true)->count(),
            'products_with_4_gallery_images' => $rows->where('has_4_gallery_images', true)->count(),
            'products_with_5_gallery_images' => $rows->where('has_5_gallery_images', true)->count(),
            'products_missing_main_image' => $rows->where('has_main_image', false)->count(),
            'products_missing_gallery' => $rows->filter(fn (array $row) => $row['existing_gallery_images'] < $minGalleryImages)->count(),
            'products_with_broken_image_file' => $rows->filter(fn (array $row) => $row['broken_image_files'] !== '')->count(),
            'products_visible_after_filter' => $rows->where('is_visible', true)->count(),
            'products_hidden_by_media_filter' => $rows->where('is_visible', false)->count(),
            'min_gallery_images_required' => $minGalleryImages,
        ];

        $errors = $rows
            ->filter(fn (array $row) => $row['hidden_reason'] !== '' || $row['broken_image_files'] !== '')
            ->values()
            ->all();

        return self::$auditCache[$minGalleryImages] = [
            'rows' => $rows,
            'summary' => $summary,
            'errors' => $errors,
        ];
    }

    private static function emptySummary(): array
    {
        return [
            'total_products' => 0,
            'products_with_main_image' => 0,
            'products_with_4_gallery_images' => 0,
            'products_with_5_gallery_images' => 0,
            'products_missing_main_image' => 0,
            'products_missing_gallery' => 0,
            'products_with_broken_image_file' => 0,
            'products_visible_after_filter' => 0,
            'products_hidden_by_media_filter' => 0,
            'min_gallery_images_required' => 4,
        ];
    }

    private static function normalizePath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || AssetUrl::isAbsoluteUrl($path)) {
            return null;
        }

        return str_replace('\\', '/', ltrim($path, '/\\'));
    }

    private static function isValidUploadFile(?string $path): bool
    {
        if ($path === null || $path === self::FALLBACK_PRODUCT_IMAGE || ! str_starts_with($path, 'uploads/')) {
            return false;
        }

        return is_file(public_path($path));
    }

    private static function hiddenReason(bool $hasMainImage, int $galleryCount, int $minGalleryImages, bool $hasBrokenFiles): string
    {
        $reasons = [];

        if (! $hasMainImage) {
            $reasons[] = 'missing_main_image';
        }

        if ($galleryCount < $minGalleryImages) {
            $reasons[] = 'missing_gallery';
        }

        if ($hasBrokenFiles) {
            $reasons[] = 'broken_image_file';
        }

        return implode('|', $reasons);
    }
}
