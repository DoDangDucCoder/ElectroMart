<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends BaseModel
{
    protected $table = 'brands';

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'brand_id',
        'brand_name',
        'brand_slug',
        'brand_desc',
        'brand_status',
    ];

    public function getBrandIdAttribute(): int
    {
        return $this->id;
    }

    public function getBrandNameAttribute(): ?string
    {
        return $this->name;
    }

    public function getBrandSlugAttribute(): ?string
    {
        return $this->slug;
    }

    public function getBrandDescAttribute(): ?string
    {
        return $this->description;
    }

    public function getBrandStatusAttribute(): int
    {
        return $this->is_active ? 0 : 1;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
