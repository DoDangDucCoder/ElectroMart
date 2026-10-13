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
            'type_label' => $this->formatTypeLabel($this->type),
            'value' => $this->value,
            'min_order_amount' => $this->min_order_total,
            'max_discount_amount' => $this->max_discount,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'is_valid' => $this->isValidCoupon(),
            'remaining_uses' => $this->usage_limit,
        ];
    }

    protected function formatTypeLabel(?string $type): string
    {
        return match ($type) {
            'fixed' => 'Fixed',
            'percent' => 'Percent',
            'shipping' => 'Vận chuyển',
            default => '',
        };
    }

    protected function isValidCoupon(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->lt($now)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_limit <= 0) {
            return false;
        }

        return true;
    }
}
