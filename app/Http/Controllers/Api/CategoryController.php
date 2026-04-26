<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Shared\Services\CacheService;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CacheService $cacheService
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->cacheService->getCategories();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'total' => $categories->count(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $category = $this->cacheService->getCategoryBySlug($slug);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    public function tree(): JsonResponse
    {
        $categories = $this->cacheService->remember(
            'category_tree',
            fn() => $this->categoryService->getCategoryTree(),
            3600 // 1 hour cache
        );

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
