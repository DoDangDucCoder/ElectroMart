<?php

namespace App\Http\Resources\Catalog;

use App\Http\Resources\BaseResource;

class BrandResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'logo_path' => $this->logo_path,
            'is_active' => (bool) $this->is_active,
            'display_order' => $this->display_order,
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
