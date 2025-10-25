<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends BaseModel
{
    protected $table = 'product_variants';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductVariantAttributeValue::class);
    }
}
