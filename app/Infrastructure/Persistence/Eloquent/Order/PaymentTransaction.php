<?php

namespace App\Infrastructure\Persistence\Eloquent\Order;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends BaseModel
{
    protected $table = 'payment_transactions';

    protected $casts = [
        'payload' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
