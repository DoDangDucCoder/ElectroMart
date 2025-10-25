<?php

namespace App\Infrastructure\Persistence\Eloquent\Customer;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends BaseModel
{
    protected $table = 'customer_addresses';

    protected $casts = [
        'meta' => 'array',
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_city_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_province_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_ward_id');
    }
}
