<?php

namespace App\Application\Catalog\UseCases;

use App\Domain\Catalog\Repositories\ProductRepositoryInterface;
use App\Domain\Catalog\DTO\ProductData;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(ProductData $productData, ?UploadedFile $image = null, ?UploadedFile $document = null): Product
    {
        return DB::transaction(function () use ($productData, $image, $document) {
            $meta = $this->prepareMeta($productData);

            if ($document) {
                $documentPath = $this->storeUploadedFile($document, 'uploads/document');
                $meta['document_file'] = basename($documentPath);
                $meta['document_path'] = $documentPath;
            }

            $imagePath = $image ? $this->storeUploadedFile($image, 'uploads/product') : null;

            $payload = [
                'name' => $productData->name,
                'slug' => $productData->slug ?? Str::slug($productData->name),
                'description' => $productData->description,
                'content' => $productData->content,
                'brand_id' => $productData->brandId,
                'primary_category_id' => $productData->primaryCategoryId,
                'price' => $productData->price?->getAmount(),
                'sale_price' => $productData->salePrice?->getAmount(),
                'cost_price' => $productData->costPrice?->getAmount(),
                'stock_quantity' => $productData->stockQuantity,
                'stock_alert_threshold' => $productData->stockAlertThreshold,
                'is_active' => $productData->isActive,
                'display_order' => $productData->displayOrder,
                'thumbnail_path' => $imagePath,
                'meta' => $this->cleanMeta($meta),
                'meta_title' => $productData->metaTitle,
                'meta_description' => $productData->metaDescription,
            ];

            $product = $this->productRepository->create($payload);

            if ($productData->categoryIds) {
                $this->syncCategories($product, $productData->categoryIds);
            }

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

            return $product->fresh(['brand', 'categories', 'media']);
        });
    }

    private function prepareMeta(ProductData $productData): array
    {
        $meta = [];

        if ($productData->tags) {
            $meta['tags'] = $productData->tags;
        }

        return $meta;
    }

    private function cleanMeta(array $meta): array
    {
        return array_filter($meta, static function ($value) {
            return $value !== null && $value !== '';
        });
    }

    private function syncCategories(Product $product, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds));

        if (!empty($ids)) {
            $product->categories()->sync($ids);
            $primaryId = $ids[0];

            if ($product->primary_category_id !== $primaryId) {
                $product->primary_category_id = $primaryId;
                $product->save();
            }
        }
    }

    private function storeUploadedFile(UploadedFile $file, string $directory): string
    {
        $targetDirectory = trim($directory, '/');
        $fullDirectory = public_path($targetDirectory);

        File::ensureDirectoryExists($fullDirectory);

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitized = Str::slug(Str::limit($originalName, 40, ''));
        $extension = $file->getClientOriginalExtension();
        $filename = $sanitized !== '' ? $sanitized : Str::random(8);
        $filename .= '-' . Str::lower(Str::random(6)) . '.' . $extension;

        $file->move($fullDirectory, $filename);

        return $targetDirectory . '/' . $filename;
    }

    private function replicateToLegacyGallery(string $imagePath): void
    {
        $galleryDirectory = public_path('uploads/gallery');
        File::ensureDirectoryExists($galleryDirectory);

        $source = public_path($imagePath);
        $destination = $galleryDirectory . '/' . basename($imagePath);

        if (File::exists($source)) {
            File::copy($source, $destination);
        }
    }
}
