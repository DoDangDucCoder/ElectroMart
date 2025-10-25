<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Application\Catalog\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ReorderCategoryRequest;
use App\Http\Requests\Catalog\StoreCategoryRequest;
use App\Http\Requests\Catalog\UpdateCategoryRequest;
use App\Http\Resources\Catalog\CategoryResource;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['parent_id', 'search', 'is_active']);

        $categories = $this->service->paginate($filters, $perPage);

        return CategoryResource::collection($categories)->response();
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = $this->service->create($request->validated());

        return new CategoryResource($category);
    }

    public function show(ProductCategory $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, ProductCategory $category): CategoryResource
    {
        $updated = $this->service->update($category, $request->validated());

        return new CategoryResource($updated);
    }

    public function destroy(ProductCategory $category): JsonResponse
    {
        $this->service->delete($category);

        return response()->json(['message' => 'Category deleted']);
    }

    public function updateStatus(Request $request, ProductCategory $category): CategoryResource
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $updated = $this->service->updateStatus($category, (bool) $request->boolean('is_active'));

        return new CategoryResource($updated);
    }

    public function reorder(ReorderCategoryRequest $request): JsonResponse
    {
        $this->service->reorder($request->validated()['ordered_ids']);

        return response()->json(['message' => 'Categories reordered']);
    }
}
