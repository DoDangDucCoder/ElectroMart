<?php

namespace App\Domain\Catalog\DTO;

use App\Domain\Shared\ValueObjects\Money;
use Illuminate\Support\Arr;

class ProductData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly ?string $content = null,
        public readonly ?int $brandId = null,
        public readonly ?int $primaryCategoryId = null,
        public readonly ?Money $price = null,
        public readonly ?Money $salePrice = null,
        public readonly ?Money $costPrice = null,
        public readonly int $stockQuantity = 0,
        public readonly int $stockAlertThreshold = 0,
        public readonly bool $isActive = true,
        public readonly bool $isFeatured = false,
        public readonly int $displayOrder = 0,
        public readonly ?string $thumbnailPath = null,
        public readonly ?string $metaTitle = null,
        public readonly ?string $metaDescription = null,
        public readonly ?array $meta = null,
        public readonly ?array $categoryIds = null,
        public readonly ?array $tags = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $price = isset($data['price']) ? new Money((float) $data['price']) : null;
        $salePrice = isset($data['sale_price']) ? new Money((float) $data['sale_price']) : null;
        $costPrice = isset($data['cost_price']) ? new Money((float) $data['cost_price']) : null;

        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            content: $data['content'] ?? null,
            brandId: Arr::get($data, 'brand_id'),
            primaryCategoryId: Arr::get($data, 'primary_category_id'),
            price: $price,
            salePrice: $salePrice,
            costPrice: $costPrice,
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            stockAlertThreshold: (int) ($data['stock_alert_threshold'] ?? 0),
            isActive: (bool) ($data['is_active'] ?? true),
            isFeatured: (bool) ($data['is_featured'] ?? false),
            displayOrder: (int) ($data['display_order'] ?? 0),
            thumbnailPath: $data['thumbnail_path'] ?? null,
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            meta: $data['meta'] ?? null,
            categoryIds: $data['category_ids'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'brand_id' => $this->brandId,
            'primary_category_id' => $this->primaryCategoryId,
            'price' => $this->price?->getAmount(),
            'sale_price' => $this->salePrice?->getAmount(),
            'cost_price' => $this->costPrice?->getAmount(),
            'stock_quantity' => $this->stockQuantity,
            'stock_alert_threshold' => $this->stockAlertThreshold,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
            'display_order' => $this->displayOrder,
            'thumbnail_path' => $this->thumbnailPath,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta' => $this->meta,
            'category_ids' => $this->categoryIds,
            'tags' => $this->tags,
        ];
    }
}
