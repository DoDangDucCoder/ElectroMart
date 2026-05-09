<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'parent_id']);
        $perPage = max(1, min((int) $request->get('per_page', 15), 100));

        $categories = $this->categoryService->paginate($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories->items()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->filled('slug') && $request->filled('name')) {
            $request->merge([
                'slug' => Str::slug((string) $request->input('name')),
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique((new ProductCategory())->getTable(), 'slug')],
            'code' => 'nullable|string|max:100',
            'parent_id' => 'nullable|integer',
            'thumbnail_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $category = $this->categoryService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo danh mục thành công.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(ProductCategory $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    public function update(Request $request, ProductCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100',
            'parent_id' => 'nullable|integer',
            'thumbnail_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $category = $this->categoryService->update($category, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công.',
            'data' => new CategoryResource($category),
        ]);
    }

    public function destroy(ProductCategory $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công.',
        ]);
    }

    public function updateStatus(Request $request, ProductCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $category = $this->categoryService->updateStatus($category, (bool) $validated['is_active']);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái danh mục thành công.',
            'data' => new CategoryResource($category),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
        ]);

        $this->categoryService->reorder($validated['ordered_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Sắp xếp lại danh mục thành công.',
        ]);
    }
}
