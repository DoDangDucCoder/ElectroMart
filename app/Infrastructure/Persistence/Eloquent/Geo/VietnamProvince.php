<?php

namespace App\Infrastructure\Persistence\Eloquent\Geo;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VietnamProvince extends BaseModel
{
    protected $table = 'vietnam_provinces';

    public function wards(): HasMany
    {
        return $this->hasMany(VietnamWard::class, 'province_code', 'code');
    }
}
