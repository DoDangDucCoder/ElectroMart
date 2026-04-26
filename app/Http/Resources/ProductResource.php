<?php

namespace App\Http\Resources;

use App\Support\AssetUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'original_price' => $this->price,
            'sale_type' => $this->sale_type,
            'sale_value' => $this->sale_value,
            'sale_amount' => $this->sale_amount,
            'sale_price' => $this->sale_price,
            'cost_price' => $this->cost_price,
            'final_price' => $this->final_price,
            'profit_per_unit' => $this->profit_per_unit,
            'sale_explanation' => $this->sale_explanation,
            'discount_percentage' => $this->sale_amount > 0 && $this->price > 0
                ? round((($this->price - $this->final_price) / $this->price) * 100)
                : null,
            'stock_quantity' => $this->stock_quantity,
            'is_in_stock' => $this->stock_quantity > 0,
            'is_low_stock' => $this->stock_quantity <= $this->stock_alert_threshold,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'thumbnail_url' => AssetUrl::product($this->thumbnail_path),
            'description' => $this->description,
            'content' => $this->content,
            'views_count' => $this->views_count,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'tags' => $this->meta['tags'] ?? [],
            'specifications' => $this->meta['specifications'] ?? [],
            'brand' => $this->whenLoaded('brand', fn() => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
                'logo_url' => AssetUrl::resolve($this->brand->logo_path, null),
            ]),
            'primary_category' => $this->whenLoaded('primaryCategory', fn() => [
                'id' => $this->primaryCategory->id,
                'name' => $this->primaryCategory->name,
                'slug' => $this->primaryCategory->slug,
            ]),
            'categories' => $this->whenLoaded('categories', fn() => 
                $this->categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ])
            ),
            'media' => $this->whenLoaded('media', fn() => 
                $this->media->map(fn($media) => [
                    'id' => $media->id,
                    'file_url' => AssetUrl::product($media->file_path),
                    'collection' => $media->collection,
                    'display_order' => $media->display_order,
                ])
            ),
            'reviews' => $this->whenLoaded('reviews', fn() => 
                $this->reviews->map(fn($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'content' => $review->content,
                    'customer_name' => $review->customer_name,
                    'is_approved' => $review->is_approved,
                    'created_at' => $review->created_at,
                ])
            ),
            'average_rating' => $this->whenLoaded('reviews', fn() => 
                $this->reviews->where('is_approved', true)->avg('rating')
            ),
            'reviews_count' => $this->whenLoaded('reviews', fn() => 
                $this->reviews->where('is_approved', true)->count()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
