<?php

namespace App\Infrastructure\Persistence\Eloquent\Notification;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationToken extends BaseModel
{
    protected $table = 'notification_tokens';

    protected $casts = [
        'meta' => 'array',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
