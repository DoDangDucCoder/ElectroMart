<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail_path ? asset($this->thumbnail_path) : null,
            'is_active' => $this->is_active,
            'display_order' => $this->display_order,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'parent_id' => $this->parent_id,
            'is_root' => $this->parent_id === null,
            'path' => $this->slug,
            'children' => $this->whenLoaded('children', fn() => 
                CategoryResource::collection($this->children)
            ),
            'parent' => $this->whenLoaded('parent', fn() => 
                new CategoryResource($this->parent)
            ),
            'products_count' => $this->whenLoaded('products', fn() => 
                $this->products->count()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
