<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Guard nothing by default – subclasses can override.
     */
    protected $guarded = [];
}
