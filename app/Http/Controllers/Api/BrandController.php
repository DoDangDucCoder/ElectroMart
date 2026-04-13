<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Shared\Services\CacheService;
use App\Http\Resources\BrandResource;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function __construct(
        private readonly CacheService $cacheService
    ) {
    }

    public function index(): JsonResponse
    {
        $brands = $this->cacheService->getBrands();

        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands),
            'total' => $brands->count(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $brand = $this->cacheService->getBrandBySlug($slug);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand),
        ]);
    }
}
