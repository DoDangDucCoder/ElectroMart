<?php

namespace App\Infrastructure\Persistence\Eloquent\Geo;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VietnamWard extends BaseModel
{
    protected $table = 'vietnam_wards';

    public function province(): BelongsTo
    {
        return $this->belongsTo(VietnamProvince::class, 'province_code', 'code');
    }
}
