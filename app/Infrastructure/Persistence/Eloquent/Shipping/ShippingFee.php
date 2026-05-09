<?php

namespace App\Infrastructure\Persistence\Eloquent\Shipping;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamProvince;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingFee extends BaseModel
{
    protected $table = 'shipping_fees';

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

    public function vietnamProvince(): BelongsTo
    {
        return $this->belongsTo(VietnamProvince::class, 'vietnam_province_code', 'code');
    }

    public function vietnamWard(): BelongsTo
    {
        return $this->belongsTo(VietnamWard::class, 'vietnam_ward_code', 'code');
    }
}
