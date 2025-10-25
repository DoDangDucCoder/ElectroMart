<?php

namespace App\Domain\Catalog\Entities;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Slug;

class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Slug $slug,
        public readonly ?string $description = null,
        public readonly ?string $content = null,
        public readonly ?int $brandId = null,
        public readonly ?int $primaryCategoryId = null,
        public readonly Money $price,
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
        public readonly int $views = 0,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function isInStock(): bool
    {
        return $this->stockQuantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stockQuantity <= $this->stockAlertThreshold;
    }

    public function getFinalPrice(): Money
    {
        return $this->salePrice ?? $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->salePrice !== null && $this->salePrice->isLessThan($this->price);
    }

    public function getDiscountPercentage(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        $discount = $this->price->subtract($this->salePrice);
        return (int) round(($discount->getAmount() / $this->price->getAmount()) * 100);
    }

    public function getTags(): array
    {
        return $this->meta['tags'] ?? [];
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->getTags(), true);
    }

    public function getMetaValue(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    public function isPublished(): bool
    {
        return $this->isActive;
    }

    public function canBePurchased(): bool
    {
        return $this->isActive && $this->isInStock();
    }
}
