<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Application\Catalog\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreBrandRequest;
use App\Http\Requests\Catalog\UpdateBrandRequest;
use App\Http\Resources\Catalog\BrandResource;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        $brands = $this->service->paginate($filters, $perPage);

        return BrandResource::collection($brands)->response();
    }

    public function store(StoreBrandRequest $request): BrandResource
    {
        $brand = $this->service->create($request->validated());

        return new BrandResource($brand);
    }

    public function show(Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, Brand $brand): BrandResource
    {
        $updated = $this->service->update($brand, $request->validated());

        return new BrandResource($updated);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $this->service->delete($brand);

        return response()->json(['message' => 'Đã xóa thương hiệu.']);
    }

    public function updateStatus(Request $request, Brand $brand): BrandResource
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $updated = $this->service->updateStatus($brand, $request->boolean('is_active'));

        return new BrandResource($updated);
    }
}
