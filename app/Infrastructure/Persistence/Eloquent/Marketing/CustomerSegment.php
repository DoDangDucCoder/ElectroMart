<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerSegment extends BaseModel
{
    protected $table = 'customer_segments';

    protected $casts = [
        'meta' => 'array',
        'last_refreshed_at' => 'datetime',
    ];

    public function filters(): HasMany
    {
        return $this->hasMany(SegmentFilter::class, 'segment_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(SegmentMember::class, 'segment_id');
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'segment_members');
    }
}
