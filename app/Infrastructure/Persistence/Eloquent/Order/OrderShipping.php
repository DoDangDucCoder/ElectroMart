<?php

namespace App\Infrastructure\Persistence\Eloquent\Order;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShipping extends BaseModel
{
    protected $table = 'order_shipping';

    protected $casts = [
        'meta' => 'array',
        'shipping_fee' => 'float',
        'insurance_fee' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'city_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'province_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'ward_id');
    }
}
