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
        $perPage = max(1, min((int) $request->get('per_page', 15), 50));
        $filters = array_merge(
            $request->only(['type']),
            [
                'is_active' => true,
                'currently_valid' => true,
            ]
        );

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
        $coupon = $this->couponService->findActiveByCode($code);

        if (! $coupon || ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mã giảm giá.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CouponResource($coupon),
        ]);
    }

    public function validate_coupon(Request $request): JsonResponse

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
            'message' => 'Mã giảm giá hợp lệ.',
            'data' => [
                'coupon' => new CouponResource($validation['coupon']),
                'discount_amount' => $validation['discount_amount'],
                'final_amount' => $validation['final_amount'],
            ],
        ]);
    }
}
