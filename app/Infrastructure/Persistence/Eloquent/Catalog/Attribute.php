<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends BaseModel
{
    protected $table = 'attributes';

    protected $casts = [
        'config' => 'array',
        'is_filterable' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class, 'attribute_group_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->config['description'] ?? null;
    }
}
