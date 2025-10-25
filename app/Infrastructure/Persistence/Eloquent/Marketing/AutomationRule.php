<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationRule extends BaseModel
{
    protected $table = 'automation_rules';

    protected $casts = [
        'trigger_conditions' => 'array',
        'actions' => 'array',
        'meta' => 'array',
        'last_run_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class, 'automation_rule_id');
    }
}
