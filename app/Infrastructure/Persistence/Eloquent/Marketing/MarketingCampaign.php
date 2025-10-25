<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends BaseModel
{
    protected $table = 'marketing_campaigns';

    protected $casts = [
        'segments' => 'array',
        'scheduled_at' => 'datetime',
        'ends_at' => 'datetime',
        'meta' => 'array',
    ];

    public function channels(): HasMany
    {
        return $this->hasMany(CampaignChannel::class, 'campaign_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class, 'campaign_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(CampaignMetric::class, 'campaign_id');
    }
}
