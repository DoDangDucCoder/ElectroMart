<?php

namespace App\Infrastructure\Persistence\Eloquent\Order;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends BaseModel
{
    protected $table = 'order_items';

    protected $casts = [
        'attributes' => 'array',
        'meta' => 'array',
        'original_price' => 'float',
        'price' => 'float',
        'sale_price' => 'float',
        'cost_price' => 'float',
        'sale_value' => 'float',
        'sale_amount' => 'float',
        'discount_amount' => 'float',
        'subtotal' => 'float',
        'tax_amount' => 'float',
    ];

    protected $appends = [
        'revenue',
        'profit',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getRevenueAttribute(): float
    {
        return round(((float) $this->price) * (int) $this->quantity, 2);
    }

    public function getProfitAttribute(): float
    {
        return round((((float) $this->price) - ((float) $this->cost_price)) * (int) $this->quantity, 2);
    }
}
