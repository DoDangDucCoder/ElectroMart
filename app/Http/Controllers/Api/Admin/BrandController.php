<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\BrandService;
use App\Http\Resources\BrandResource;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $brandService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active']);
        $perPage = max(1, min((int) $request->get('per_page', 15), 100));

        $brands = $this->brandService->paginate($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands->items()),
            'pagination' => [
                'current_page' => $brands->currentPage(),
                'last_page' => $brands->lastPage(),
                'per_page' => $brands->perPage(),
                'total' => $brands->total(),
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
            'slug' => ['required', 'string', 'max:255', Rule::unique((new Brand())->getTable(), 'slug')],
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
            'meta' => 'nullable|array',
        ]);

        $brand = $this->brandService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo thương hiệu thành công.',
            'data' => new BrandResource($brand),
        ], 201);
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand),
        ]);
    }

    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'nullable|integer',
            'meta' => 'nullable|array',
        ]);

        $brand = $this->brandService->update($brand, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thương hiệu thành công.',
            'data' => new BrandResource($brand),
        ]);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $this->brandService->delete($brand);

        return response()->json([
            'success' => true,
            'message' => 'Xóa thương hiệu thành công.',
        ]);
    }

    public function updateStatus(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $brand = $this->brandService->updateStatus($brand, (bool) $validated['is_active']);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thương hiệu thành công.',
            'data' => new BrandResource($brand),
        ]);
    }
}
