<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Checkout\Services\CheckoutService;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {
    }

    public function index(): JsonResponse
    {
        $orders = $this->checkoutService->getCustomerOrders(Auth::id());

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|string|in:cash,cod,card,paypal',
            'notes' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        $order = $this->checkoutService->createOrder(
            Auth::id(),
            $request->shipping_address_id,
            $request->payment_method,
            $request->notes,
            $request->coupon_code
        );

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->checkoutService->getOrderById($id, Auth::id());

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function cancel(int $id): JsonResponse
    {
        $order = $this->checkoutService->cancelOrder($id, Auth::id());

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or cannot be cancelled',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($order),
        ]);
    }
}
