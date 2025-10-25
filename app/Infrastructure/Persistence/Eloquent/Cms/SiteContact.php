<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class SiteContact extends BaseModel
{
    protected $table = 'site_contacts';

    protected $casts = [
        'social_links' => 'array',
        'meta' => 'array',
    ];
}
