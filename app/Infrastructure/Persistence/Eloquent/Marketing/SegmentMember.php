<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentMember extends BaseModel
{
    protected $table = 'segment_members';

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
