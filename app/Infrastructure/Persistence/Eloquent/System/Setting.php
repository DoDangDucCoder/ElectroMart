<?php

namespace App\Infrastructure\Persistence\Eloquent\System;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class Setting extends BaseModel
{
    protected $table = 'settings';

    protected $casts = [
        'meta' => 'array',
    ];
}
