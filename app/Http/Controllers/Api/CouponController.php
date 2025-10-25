<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Checkout\Services\CouponService;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 50);
        $filters = $request->only(['is_active', 'type']);

        $coupons = $this->couponService->paginateCoupons($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => CouponResource::collection($coupons->items()),
            'pagination' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'per_page' => $coupons->perPage(),
                'total' => $coupons->total(),
            ],
        ]);
    }

    public function show(string $code): JsonResponse
    {
        $coupon = $this->couponService->findByCode($code);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CouponResource($coupon),
        ]);
    }

    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $validation = $this->couponService->validateCoupon(
            $request->code,
            $request->total_amount
        );

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'coupon' => new CouponResource($validation['coupon']),
                'discount_amount' => $validation['discount_amount'],
                'final_amount' => $validation['final_amount'],
            ],
        ]);
    }
}
