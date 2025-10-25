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
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_label' => $this->status->getLabel(),
            'subtotal' => $this->subtotal,
            'shipping_fee' => $this->shipping_fee,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'notes' => $this->notes,
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
                'name' => $this->address->name,
                'phone' => $this->address->phone,
                'address_line_1' => $this->address->address_line_1,
                'address_line_2' => $this->address->address_line_2,
                'city' => $this->address->city,
                'state' => $this->address->state,
                'postal_code' => $this->address->postal_code,
                'country' => $this->address->country,
            ]),
            'items' => $this->whenLoaded('items', fn() => 
                $this->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_slug' => $item->product_slug,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ])
            ),
            'shipping' => $this->whenLoaded('shipping', fn() => [
                'id' => $this->shipping->id,
                'method' => $this->shipping->method,
                'tracking_number' => $this->shipping->tracking_number,
                'estimated_delivery' => $this->shipping->estimated_delivery,
                'actual_delivery' => $this->shipping->actual_delivery,
            ]),
            'payments' => $this->whenLoaded('payments', fn() => 
                $this->payments->map(fn($payment) => [
                    'id' => $payment->id,
                    'method' => $payment->method,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'paid_at' => $payment->paid_at,
                ])
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
