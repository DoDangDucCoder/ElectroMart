<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends BaseModel
{
    protected $table = 'attribute_values';

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
