<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Application\Catalog\Services\ProductPricingService;
use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends BaseModel
{
    protected $table = 'products';

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_hot' => 'boolean',
        'price' => 'float',
        'sale_price' => 'float',
        'cost_price' => 'float',
        'sale_value' => 'float',
    ];

    protected $appends = [
        'product_id',
        'product_name',
        'product_slug',
        'product_desc',
        'product_content',
        'product_price',
        'product_quantity',
        'product_status',
        'product_image',
        'product_tags',
        'product_views',
        'price_cost',
        'product_file',
        'category_name',
        'brand_name',
        'sale_amount',
        'final_price',
        'profit_per_unit',
        'sale_explanation',
    ];

    public function getProductIdAttribute(): ?int
    {
        return $this->id;
    }

    public function getProductNameAttribute(): ?string
    {
        return $this->name;
    }

    public function getProductSlugAttribute(): ?string
    {
        return $this->slug;
    }

    public function getProductDescAttribute(): ?string
    {
        return $this->description;
    }

    public function getProductContentAttribute(): ?string
    {
        return $this->content;
    }

    public function getProductPriceAttribute(): float
    {
        return $this->final_price;
    }

    public function getProductQuantityAttribute(): int
    {
        return $this->stock_quantity ?? 0;
    }

    public function getProductStatusAttribute(): int
    {
        return $this->is_active ? 0 : 1;
    }

    public function getProductImageAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return $this->thumbnail_path;
        }

        $media = $this->media()->first();

        return $media?->file_path;
    }

    public function getProductTagsAttribute(): ?string
    {
        $tags = $this->meta['tags'] ?? null;

        if (is_array($tags)) {
            return collect($tags)
                ->filter(fn ($tag) => is_scalar($tag))
                ->map(fn ($tag) => trim((string) $tag))
                ->filter()
                ->implode(',');
        }

        return is_scalar($tags) ? (string) $tags : null;
    }

    public function getProductViewsAttribute(): int
    {
        return $this->views_count ?? 0;
    }

    public function getPriceCostAttribute(): ?float
    {
        return $this->cost_price;
    }

    public function getSaleAmountAttribute(): float
    {
        return (float) $this->pricingPayload()['sale_amount'];
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) $this->pricingPayload()['final_price'];
    }

    public function getProfitPerUnitAttribute(): float
    {
        return $this->pricing()->calculateProfit($this->final_price, $this->cost_price);
    }

    public function getSaleExplanationAttribute(): ?string
    {
        $pricingPayload = $this->pricingPayload();

        return $this->pricing()->buildSaleExplanation(
            (float) ($this->price ?? 0),
            $pricingPayload['sale_type'],
            $pricingPayload['sale_value']
        );
    }

    public function getProductFileAttribute(): ?string
    {
        return $this->meta['document_file'] ?? null;
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->primaryCategory?->category_name;
    }

    public function getBrandNameAttribute(): ?string
    {
        return $this->brand?->brand_name;
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'primary_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_product', 'product_id', 'product_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductMedia::class)->where('collection', 'thumbnail');
    }

    protected function pricing(): ProductPricingService
    {
        return app(ProductPricingService::class);
    }

    protected function getArrayableAppends(): array
    {
        if (! array_key_exists($this->getKeyName(), $this->getAttributes())) {
            return [];
        }

        return parent::getArrayableAppends();
    }

    protected function pricingPayload(): array
    {
        return $this->pricing()->derivePricingPayload(
            (float) ($this->price ?? 0),
            $this->sale_type,
            $this->sale_value,
            $this->sale_price
        );
    }
}
