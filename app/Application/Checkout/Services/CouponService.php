<?php

namespace App\Application\Checkout\Services;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class CouponService
{
    protected const SESSION_KEY = 'coupon';

    public function __construct(
        private readonly CouponRepositoryInterface $coupons
    ) {
    }

    public function current(): ?array
    {
        return $this->validatedCurrent();
    }

    public function validatedCurrent(?float $subtotal = null, ?int $customerId = null): ?array
    {
        $data = Session::get(self::SESSION_KEY);

        if (! is_array($data) || empty($data)) {
            return null;
        }

        $code = $data['code'] ?? null;

        if (! is_string($code) || trim($code) === '') {
            $this->clear();

            return null;
        }

        if ($subtotal === null) {
            return $data;
        }

        $reservation = $this->currentReservation();
        if (! $reservation || (int) $reservation->coupon_id !== (int) ($data['id'] ?? 0)) {
            $this->clear(true, 'missing_reservation');

            return null;
        }

        if ($reservation->reserved_until && $reservation->reserved_until->isPast()) {
            $this->clear(true, 'expired');

            return null;
        }

        $validation = $this->validateCoupon($code, $subtotal, $customerId, true);

        if (! ($validation['valid'] ?? false)) {
            $this->clear(true, 'invalid_after_reservation');

            return null;
        }

        $coupon = $validation['coupon'];
        $payload = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => (float) $validation['discount_amount'],
            'max_discount' => (float) ($coupon->max_discount ?? 0),
            'redemption_id' => $reservation->id,
            'reserved_until' => $reservation->reserved_until?->toIso8601String(),
        ];

        Session::put(self::SESSION_KEY, $payload);
        Session::save();

        return $payload;
    }

    public function paginateCoupons(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->coupons->paginate($filters, $perPage);
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->coupons->findByCode($code);
    }

    public function findActiveByCode(string $code): ?Coupon
    {
        return $this->coupons->findActiveByCode($code);
    }

    public function validateCoupon(string $code, float $totalAmount, ?int $customerId = null, bool $skipUsageLimit = false): array
    {
        $coupon = $this->coupons->findActiveByCode($code);

        if (! $coupon) {
            return [
                'valid' => false,
                'message' => 'Không tìm thấy mã giảm giá hoặc mã chưa được kích hoạt.',
            ];
        }

        if ($coupon->min_order_total !== null && $totalAmount < (float) $coupon->min_order_total) {
            return [
                'valid' => false,
                'message' => 'Tổng đơn hàng chưa đạt điều kiện tối thiểu.',
            ];
        }

        try {
            $this->assertUsageAvailable($coupon, $customerId, $skipUsageLimit);
        } catch (RuntimeException $exception) {
            return [
                'valid' => false,
                'message' => $exception->getMessage(),
            ];
        }

        $discount = $this->calculateDiscount($coupon, $totalAmount);
        $finalAmount = max(0, $totalAmount - $discount);

        return [
            'valid' => true,
            'message' => 'Mã giảm giá hợp lệ.',
            'coupon' => $coupon,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
        ];
    }

    public function clear(bool $releaseReservation = false, string $reason = 'session_cleared'): void
    {
        if ($releaseReservation) {
            $reservation = $this->currentReservation();
            if ($reservation) {
                $this->coupons->releaseReservation($reservation, $reason);
            }
        }

        Session::forget(self::SESSION_KEY);
    }

    public function apply(string $code, float $subtotal, ?int $customerId = null): array
    {
        $coupon = $this->coupons->findActiveByCode($code);

        if (! $coupon) {
            throw new RuntimeException('Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        if ($coupon->min_order_total !== null && $subtotal < (float) $coupon->min_order_total) {
            throw new RuntimeException('Tổng đơn hàng chưa đạt điều kiện tối thiểu.');
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);
        $currentReservation = $this->currentReservation();
        if ($currentReservation && $currentReservation->reserved_until && $currentReservation->reserved_until->isPast()) {
            $this->coupons->releaseReservation($currentReservation, 'expired');
            $currentReservation = null;
        }

        $isSameReservation = $currentReservation && (int) $currentReservation->coupon_id === (int) $coupon->id;
        $this->assertUsageAvailable($coupon, $customerId, (bool) $isSameReservation);

        if ($isSameReservation) {
            $reservedUntil = $currentReservation->reserved_until;
            $redemption = $currentReservation;
        } else {
            if ($currentReservation) {
                $this->coupons->releaseReservation($currentReservation, 'replaced');
            }

            $reservedUntil = Carbon::now()->addMinutes(max(1, (int) config('coupon.reservation_minutes', 30)));
            $redemption = $this->coupons->reserveUsage(
                $coupon,
                $customerId,
                $discount,
                Session::getId(),
                $reservedUntil
            );
        }

        $payload = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => $discount,
            'max_discount' => (float) ($coupon->max_discount ?? 0),
            'redemption_id' => $redemption->id,
            'reserved_until' => $reservedUntil?->toIso8601String(),
        ];

        Session::put(self::SESSION_KEY, $payload);
        Session::save();

        return $payload;
    }

    public function resolveCouponFromSession(): ?Coupon
    {
        $data = Session::get(self::SESSION_KEY);

        if (! $data) {
            return null;
        }

        if (isset($data['id'])) {
            return $this->coupons->findById((int) $data['id']);
        }

        if (! empty($data['code'])) {
            return $this->coupons->findActiveByCode($data['code']);
        }

        return null;
    }

    public function redeem(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void
    {
        $reservation = $this->currentReservation();

        if ($reservation && (int) $reservation->coupon_id === (int) $coupon->id) {
            $this->coupons->redeemReservation($reservation, $customerId, $orderId, $discount);

            return;
        }

        $redemption = $this->coupons->reserveUsage(
            $coupon,
            $customerId,
            $discount,
            Session::getId(),
            Carbon::now()->addMinutes(max(1, (int) config('coupon.reservation_minutes', 30)))
        );
        $this->coupons->redeemReservation($redemption, $customerId, $orderId, $discount);
    }

    protected function currentReservation(): ?CouponRedemption
    {
        $data = Session::get(self::SESSION_KEY);
        $redemptionId = is_array($data) ? (int) ($data['redemption_id'] ?? 0) : 0;

        if ($redemptionId <= 0 || Session::getId() === '') {
            return null;
        }

        return $this->coupons->findActiveReservation($redemptionId, Session::getId());
    }

    protected function assertUsageAvailable(Coupon $coupon, ?int $customerId, bool $skipUsageLimit = false): void
    {
        if (! $skipUsageLimit && $coupon->usage_limit !== null && $coupon->usage_limit <= 0) {
            throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
        }

        if (! $skipUsageLimit && $coupon->usage_per_customer !== null) {
            $customerUsage = $this->coupons->countRedemptionsForCustomer($coupon, $customerId);
            if ($customerUsage >= $coupon->usage_per_customer) {
                throw new RuntimeException('Bạn đã sử dụng mã giảm giá này tối đa số lần cho phép.');
            }
        }
    }

    protected function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        return match ($coupon->type) {
            'percent' => $this->calculatePercentDiscount($coupon, $subtotal),
            'fixed' => min((float) $coupon->value, $subtotal),
            'shipping' => (float) $coupon->value,
            default => 0,
        };
    }

    protected function calculatePercentDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return $discount;
    }
}
