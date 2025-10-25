<?php

namespace App\Domain\Catalog\DTO;

use Illuminate\Support\Arr;

class CategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug = null,
        public readonly ?string $code = null,
        public readonly ?int $parentId = null,
        public readonly ?string $description = null,
        public readonly ?string $thumbnailPath = null,
        public readonly bool $isActive = true,
        public readonly int $displayOrder = 0,
        public readonly ?string $metaTitle = null,
        public readonly ?string $metaDescription = null,
        public readonly ?array $meta = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? null,
            code: $data['code'] ?? null,
            parentId: Arr::get($data, 'parent_id'),
            description: $data['description'] ?? null,
            thumbnailPath: $data['thumbnail_path'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            displayOrder: (int) ($data['display_order'] ?? 0),
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            meta: $data['meta'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'parent_id' => $this->parentId,
            'description' => $this->description,
            'thumbnail_path' => $this->thumbnailPath,
            'is_active' => $this->isActive,
            'display_order' => $this->displayOrder,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta' => $this->meta,
        ];
    }
}
