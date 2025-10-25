<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Wrap resource under consistent top-level key.
     */
    public static $wrap = 'data';

    /**
     * Provide default meta structure.
     */
    public function with($request): array
    {
        return [
            'meta' => [
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
