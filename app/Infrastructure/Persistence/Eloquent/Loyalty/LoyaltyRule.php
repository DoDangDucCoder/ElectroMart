<?php

namespace App\Infrastructure\Persistence\Eloquent\Loyalty;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyRule extends BaseModel
{
    protected $table = 'loyalty_rules';

    protected $casts = [
        'conditions' => 'array',
        'meta' => 'array',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'rule_id');
    }
}
