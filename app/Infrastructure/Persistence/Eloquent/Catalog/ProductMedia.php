<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends BaseModel
{
    protected $table = 'product_media';
    protected $appends = [
        'gallery_image',
        'url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getGalleryImageAttribute(): ?string
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
}
