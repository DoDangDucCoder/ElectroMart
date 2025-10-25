<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\ProductService;
use App\Application\Shared\Services\CacheService;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Api\Admin\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly CacheService $cacheService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category', 'brand', 'status', 'featured']);
        $perPage = min($request->get('per_page', 15), 100);

        $products = $this->productService->paginateForAdmin($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->validated(),
            $request->file('image'),
            $request->file('document')
        );

        // Invalidate cache
        $this->cacheService->invalidateProductCache($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product = $this->productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('image'),
            $request->file('document')
        );

        // Invalidate cache
        $this->cacheService->invalidateProductCache($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $this->productService->deleteProduct($product);

        // Invalidate cache
        $this->cacheService->invalidateProductCache($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product = $this->productService->setProductStatus($product, !$product->is_active);

        // Invalidate cache
        $this->cacheService->invalidateProductCache($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'data' => new ProductResource($product),
        ]);
    }
}
