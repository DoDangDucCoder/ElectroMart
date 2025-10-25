<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class Video extends BaseModel
{
    protected $table = 'videos';

    protected $casts = [
        'meta' => 'array',
    ];
}
