<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductCategory extends BaseModel
{
    protected $table = 'product_categories';

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'category_id',
        'category_name',
        'slug_category_product',
        'category_parent',
        'category_desc',
        'category_status',
        'category_order',
        'meta_keywords',
    ];

    public function getCategoryIdAttribute(): int
    {
        return $this->id;
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->name;
    }

    public function getSlugCategoryProductAttribute(): ?string
    {
        return $this->slug;
    }

    public function getCategoryParentAttribute(): ?int
    {
        return $this->parent_id;
    }

    public function getCategoryDescAttribute(): ?string
    {
        return $this->description;
    }

    public function getCategoryStatusAttribute(): int
    {
        return $this->is_active ? 0 : 1;
    }

    public function getCategoryOrderAttribute(): int
    {
        return $this->display_order ?? 0;
    }

    public function getMetaKeywordsAttribute(): ?string
    {
        return $this->meta['meta_keywords'] ?? null;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category_product', 'product_category_id', 'product_id');
    }
}
