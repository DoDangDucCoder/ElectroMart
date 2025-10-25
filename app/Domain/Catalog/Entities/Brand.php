<?php

namespace App\Domain\Catalog\Entities;

use App\Domain\Shared\ValueObjects\Slug;

class Brand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Slug $slug,
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        public readonly ?string $logoPath = null,
        public readonly ?string $website = null,
        public readonly bool $isActive = true,
        public readonly int $displayOrder = 0,
        public readonly ?string $metaTitle = null,
        public readonly ?string $metaDescription = null,
        public readonly ?array $meta = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getMetaValue(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    public function isPublished(): bool
    {
        return $this->isActive;
    }

    public function hasWebsite(): bool
    {
        return !empty($this->website);
    }

    public function hasLogo(): bool
    {
        return !empty($this->logoPath);
    }
}
