<?php

namespace App\Infrastructure\Persistence\Eloquent\Analytics;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Marketing\MarketingCampaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends BaseModel
{
    protected $table = 'analytics_events';

    protected $casts = [
        'payload' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }
}
