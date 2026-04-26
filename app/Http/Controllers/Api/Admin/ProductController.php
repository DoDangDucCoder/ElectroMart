<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\ProductService;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Api\Admin\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category', 'brand', 'status', 'featured']);
        $perPage = max(1, min((int) $request->get('per_page', 15), 100));

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

        return response()->json([
            'success' => true,
            'message' => 'Tạo sản phẩm thành công.',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm.',
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
                'message' => 'Không tìm thấy sản phẩm.',
            ], 404);
        }

        $product = $this->productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('image'),
            $request->file('document')
        );

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công.',
            'data' => new ProductResource($product),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm.',
            ], 404);
        }

        $this->productService->deleteProduct($product);

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công.',
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm.',
            ], 404);
        }

        $product = $this->productService->setProductStatus($product, !$product->is_active);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái sản phẩm thành công.',
            'data' => new ProductResource($product),
        ]);
    }
}
