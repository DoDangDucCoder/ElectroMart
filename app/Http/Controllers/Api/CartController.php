<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Checkout\Services\CartService;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::id());

        return response()->json([
            'success' => true,
            'data' => new CartResource($cart),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->cartService->addItem(
            Auth::id(),
            $request->product_id,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'data' => new CartResource($cart),
        ], 201);
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $cart = $this->cartService->updateItem(
            Auth::id(),
            $itemId,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => new CartResource($cart),
        ]);
    }

    public function remove(int $itemId): JsonResponse
    {
        $cart = $this->cartService->removeItem(Auth::id(), $itemId);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => new CartResource($cart),
        ]);
    }

    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
