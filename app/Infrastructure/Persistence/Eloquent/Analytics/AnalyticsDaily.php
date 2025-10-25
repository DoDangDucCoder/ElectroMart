<?php

namespace App\Infrastructure\Persistence\Eloquent\Analytics;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class AnalyticsDaily extends BaseModel
{
    protected $table = 'analytics_daily';

    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
    ];
}
