<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Catalog\Services\ProductReviewService;
use App\Http\Resources\ProductReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function __construct(
        private readonly ProductReviewService $reviewService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        $perPage = min($request->get('per_page', 10), 50);

        if (!$productId) {
            return response()->json([
                'success' => false,
                'message' => 'Product ID is required',
            ], 400);
        }

        $reviews = $this->reviewService->getProductReviews($productId, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ]);

        $review = $this->reviewService->createReview([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => new ProductReviewResource($review),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductReviewResource($review),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|max:1000',
        ]);

        $review = $this->reviewService->findById($id);

        if (!$review || $review->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or unauthorized',
            ], 404);
        }

        $review = $this->reviewService->updateReview($review, $request->only(['rating', 'title', 'content']));

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => new ProductReviewResource($review),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review || $review->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or unauthorized',
            ], 404);
        }

        $this->reviewService->deleteReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}
