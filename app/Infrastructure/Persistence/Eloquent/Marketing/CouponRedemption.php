<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends BaseModel
{
    protected $table = 'coupon_redemptions';

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
