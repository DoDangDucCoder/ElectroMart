<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;

class ContactMessage extends BaseModel
{
    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];
}
