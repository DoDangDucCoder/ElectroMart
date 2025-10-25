<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class Document extends BaseModel
{
    protected $table = 'documents';

    protected $casts = [
        'meta' => 'array',
    ];
}
