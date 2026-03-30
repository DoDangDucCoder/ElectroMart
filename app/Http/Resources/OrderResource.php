<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'status' => $this->status,
            'status_label' => $this->formatStatusLabel($this->status),
            'subtotal' => $this->subtotal,
            'shipping_total' => $this->shipping_total,
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'total' => $this->total,
            'note' => $this->note,
            'coupon_code' => $this->coupon_code,
            'placed_at' => $this->placed_at,
            'confirmed_at' => $this->confirmed_at,
            'shipped_at' => $this->shipped_at,
            'completed_at' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at,
            'customer' => $this->whenLoaded('customer', fn() => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
            ]),
            'address' => $this->whenLoaded('address', fn() => [
                'id' => $this->address->id,
                'recipient_name' => $this->address->recipient_name,
                'recipient_phone' => $this->address->recipient_phone,
                'email' => $this->address->email,
                'address_line1' => $this->address->address_line1,
                'address_line2' => $this->address->address_line2,
                'postal_code' => $this->address->postal_code,
                'country' => $this->address->country,
            ]),
            'items' => $this->whenLoaded('items', fn() => 
                $this->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'original_price' => $item->original_price,
                    'price' => $item->price,
                    'sale_price' => $item->sale_price,
                    'cost_price' => $item->cost_price,
                    'sale_type' => $item->sale_type,
                    'sale_value' => $item->sale_value,
                    'sale_amount' => $item->sale_amount,
                    'discount_amount' => $item->discount_amount,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'revenue' => $item->revenue,
                    'profit' => $item->profit,
                ])
            ),
            'shipping' => $this->whenLoaded('shipping', fn() => [
                'id' => $this->shipping->id,
                'method' => $this->shipping->shipping_method,
                'tracking_number' => $this->shipping->tracking_number,
                'shipping_fee' => $this->shipping->shipping_fee,
                'insurance_fee' => $this->shipping->insurance_fee,
            ]),
            'payments' => $this->whenLoaded('payments', fn() => 
                $this->payments->map(fn($payment) => [
                    'id' => $payment->id,
                    'provider' => $payment->provider,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'reference' => $payment->reference,
                    'paid_at' => $payment->paid_at,
                ])
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function formatStatusLabel(?string $status): string
    {
        if (! $status) {
            return '';
        }

        return ucwords(str_replace('_', ' ', $status));
    }
}
