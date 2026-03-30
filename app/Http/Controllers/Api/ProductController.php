<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\ProductDiscoveryTrackingService;
use App\Application\Catalog\Services\ProductService;
use App\Application\Shared\Services\CacheService;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly CacheService $cacheService,
        private readonly ProductDiscoveryTrackingService $discoveryTracking
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'brand', 'min_price', 'max_price', 'search', 'sort']);
        $perPage = min($request->get('per_page', 12), 50); // Max 50 items per page

        $products = $this->cacheService->remember(
            'api_products_' . md5(serialize($filters)) . "_{$perPage}",
            fn() => $this->productService->getProducts($filters, $perPage),
            300 // 5 minutes cache
        );

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'filters' => $filters,
        ]);
    }

    public function show(string $slug, Request $request): JsonResponse
    {
        $product = $this->cacheService->getProductBySlug($slug);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $this->productService->recordProductView(
            $product,
            optional($request->user('sanctum'))->id,
            $request->ip(),
            $request->userAgent()
        );

        $recommendationData = $this->productService->getRecommendedProducts(
            optional($request->user('sanctum'))->id,
            $request->ip(),
            $product->id,
            6,
            'api_product_show'
        );

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'related_products' => ProductResource::collection(
                $this->productService->getRelatedProducts($product, 6)
            ),
            'recommended_products' => ProductResource::collection($recommendationData['products']),
        ]);
    }

    public function latest(): JsonResponse
    {
        $products = $this->cacheService->getLatestProducts(12);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    public function featured(): JsonResponse
    {
        $products = $this->cacheService->getFeaturedProducts(8);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('q', ''));
        $limit = min($request->get('limit', 20), 50);
        $filters = [
            'keyword' => $keyword !== '' ? $keyword : null,
            'product_code' => $request->filled('sku') ? trim((string) $request->get('sku')) : null,
            'category_id' => $request->filled('category_id') ? (int) $request->get('category_id') : null,
            'price_min' => $request->filled('min_price') ? (float) $request->get('min_price') : null,
            'price_max' => $request->filled('max_price') ? (float) $request->get('max_price') : null,
            'sort' => $request->get('sort', 'popular'),
        ];
        $products = $this->productService->searchCatalog($filters, $limit);

        $this->discoveryTracking->trackSearch(
            $filters,
            $products->total(),
            optional($request->user('sanctum'))->id,
            $request->ip(),
            'api'
        );

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'keyword' => $keyword,
            'filters' => $filters,
            'total' => $products->total(),
        ]);
    }

    public function recommendations(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 12), 24);
        $excludeProductId = $request->filled('exclude_product_id') ? (int) $request->get('exclude_product_id') : null;
        $recommendationData = $this->productService->getRecommendedProducts(
            optional($request->user('sanctum'))->id,
            $request->ip(),
            $excludeProductId,
            $limit,
            'api_recommendations'
        );

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($recommendationData['products']),
            'sources' => $recommendationData['sources'],
            'description' => $recommendationData['description'],
        ]);
    }

    public function byCategory(string $categorySlug, Request $request): JsonResponse
    {
        $filters = $request->only(['brand', 'min_price', 'max_price', 'sort']);
        $perPage = min($request->get('per_page', 12), 50);

        $products = $this->cacheService->getProductsByCategory($categorySlug, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'category_slug' => $categorySlug,
            'filters' => $filters,
        ]);
    }

    public function byBrand(string $brandSlug, Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'min_price', 'max_price', 'sort']);
        $perPage = min($request->get('per_page', 12), 50);

        $products = $this->productService->getProductsByBrandSlug($brandSlug, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'brand_slug' => $brandSlug,
            'filters' => $filters,
        ]);
    }
}
