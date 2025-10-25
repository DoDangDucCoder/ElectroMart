<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->type->getLabel(),
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount,
            'max_discount_amount' => $this->max_discount_amount,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'is_valid' => $this->isValid(),
            'remaining_uses' => $this->usage_limit ? $this->usage_limit - $this->used_count : null,
        ];
    }
}
