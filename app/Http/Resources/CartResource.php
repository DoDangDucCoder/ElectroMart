<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_slug' => $item->product->slug,
                    'product_image' => $item->product->thumbnail_path ? asset($item->product->thumbnail_path) : null,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity,
                ];
            }),
            'subtotal' => $this->subtotal,
            'shipping_fee' => $this->shipping_fee,
            'discount_amount' => $this->discount_amount,
            'total' => $this->total,
            'items_count' => $this->items->sum('quantity'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
