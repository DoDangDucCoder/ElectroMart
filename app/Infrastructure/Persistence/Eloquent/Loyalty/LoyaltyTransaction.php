<?php

namespace App\Infrastructure\Persistence\Eloquent\Loyalty;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends BaseModel
{
    protected $table = 'loyalty_transactions';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(LoyaltyRule::class, 'rule_id');
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'tier_id');
    }
}
