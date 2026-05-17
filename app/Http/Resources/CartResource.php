<?php

namespace App\Http\Resources;

use App\Support\AssetUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            $items = collect($this->resource['items'] ?? []);

            return [
                'id' => $this->resource['id'] ?? null,
                'customer_id' => $this->resource['customer_id'] ?? null,
                'items' => $items->map(function ($item) {
                    $price = (float) ($item['product_price'] ?? 0);
                    $quantity = (int) ($item['product_qty'] ?? 0);

                    return [
                        'id' => $item['session_id'] ?? null,
                        'session_id' => $item['session_id'] ?? null,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'] ?? null,
                        'product_image' => AssetUrl::productVariant($item['product_image'] ?? null, 'thumb'),
                        'price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $price * $quantity,
                    ];
                }),
                'subtotal' => (float) ($this->resource['subtotal'] ?? 0),
                'shipping_fee' => (float) ($this->resource['shipping_fee'] ?? 0),
                'discount_amount' => (float) ($this->resource['discount_amount'] ?? 0),
                'total' => (float) ($this->resource['total'] ?? 0),
                'items_count' => (int) ($this->resource['items_count'] ?? $items->sum('product_qty')),
            ];
        }

        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_slug' => $item->product->slug,
                    'product_image' => AssetUrl::productVariant($item->product->thumbnail_path, 'thumb'),
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
