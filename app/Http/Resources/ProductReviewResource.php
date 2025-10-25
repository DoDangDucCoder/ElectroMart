<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'customer_id' => $this->customer_id,
            'rating' => $this->rating,
            'title' => $this->title,
            'content' => $this->content,
            'is_approved' => $this->is_approved,
            'helpful_count' => $this->helpful_count,
            'customer' => $this->whenLoaded('customer', fn() => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'avatar_url' => $this->customer->avatar_path ? asset($this->customer->avatar_path) : null,
            ]),
            'product' => $this->whenLoaded('product', fn() => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
