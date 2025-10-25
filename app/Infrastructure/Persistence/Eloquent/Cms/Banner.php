<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class Banner extends BaseModel
{
    protected $table = 'banners';

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'meta' => 'array',
    ];
}
