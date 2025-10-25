<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentFilter extends BaseModel
{
    protected $table = 'segment_filters';

    protected $casts = [
        'value_json' => 'array',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }
}
