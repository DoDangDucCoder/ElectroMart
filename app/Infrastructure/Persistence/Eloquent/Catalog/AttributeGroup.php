<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends BaseModel
{
    protected $table = 'attribute_groups';

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
}
