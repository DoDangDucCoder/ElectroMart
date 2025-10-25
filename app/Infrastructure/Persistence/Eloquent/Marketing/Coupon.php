<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends BaseModel
{
    protected $table = 'coupons';

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_segments' => 'array',
        'meta' => 'array',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }
}
