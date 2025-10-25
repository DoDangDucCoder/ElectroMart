<?php

namespace App\Infrastructure\Persistence\Eloquent\Loyalty;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends BaseModel
{
    protected $table = 'loyalty_tiers';

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'tier_id');
    }
}
