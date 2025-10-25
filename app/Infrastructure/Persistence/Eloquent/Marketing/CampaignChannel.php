<?php

namespace App\Infrastructure\Persistence\Eloquent\Marketing;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignChannel extends BaseModel
{
    protected $table = 'campaign_channels';

    protected $casts = [
        'settings' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class, 'campaign_channel_id');
    }
}
