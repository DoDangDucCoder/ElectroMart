<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EloquentCouponRepository implements CouponRepositoryInterface
{
    public function findActiveByCode(string $code): ?Coupon
    {
        $now = Carbon::now();

        return Coupon::where('code', $code)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->first();
    }

    public function findByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }

    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Coupon::query();
        $now = Carbon::now();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['currently_valid'])) {
            $query->where(function ($builder) use ($now) {
                $builder->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })->where(function ($builder) use ($now) {
                $builder->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })->where(function ($builder) {
                $builder->whereNull('usage_limit')
                    ->orWhere('usage_limit', '>', 0);
            });
        }

        if (! empty($filters['type'])) {
            $query->whereIn('type', (array) $filters['type']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function incrementUsage(Coupon $coupon): void
    {
        if ($coupon->usage_limit === null) {
            $coupon->increment('used_count');
            return;
        }

        $affected = DB::table($coupon->getTable())
            ->where('id', $coupon->id)
            ->where('usage_limit', '>', 0)
            ->update([
                'usage_limit' => DB::raw('usage_limit - 1'),
                'used_count' => DB::raw('used_count + 1'),
            ]);

        if ($affected === 0) {
            throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
        }
    }

    public function reserveUsage(Coupon $coupon, ?int $customerId, float $discount, string $sessionId, CarbonInterface $reservedUntil): CouponRedemption
    {
        return DB::transaction(function () use ($coupon, $customerId, $discount, $sessionId, $reservedUntil): CouponRedemption {
            if ($coupon->usage_limit !== null) {
                $affected = DB::table($coupon->getTable())
                    ->where('id', $coupon->id)
                    ->where('usage_limit', '>', 0)
                    ->update([
                        'usage_limit' => DB::raw('usage_limit - 1'),
                        'used_count' => DB::raw('used_count + 1'),
                    ]);

                if ($affected === 0) {
                    throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
                }
            } else {
                DB::table($coupon->getTable())
                    ->where('id', $coupon->id)
                    ->update(['used_count' => DB::raw('used_count + 1')]);
            }

            return CouponRedemption::create([
                'coupon_id' => $coupon->id,
                'customer_id' => $customerId,
                'order_id' => null,
                'discount_amount' => $discount,
                'status' => 'reserved',
                'reserved_session_id' => $sessionId,
                'reserved_until' => $reservedUntil,
            ]);
        });
    }

    public function findActiveReservation(int $redemptionId, string $sessionId): ?CouponRedemption
    {
        return CouponRedemption::query()
            ->with('coupon')
            ->whereKey($redemptionId)
            ->where('reserved_session_id', $sessionId)
            ->where('status', 'reserved')
            ->first();
    }

    public function redeemReservation(CouponRedemption $redemption, ?int $customerId, ?int $orderId, float $discount): void
    {
        $redemption->forceFill([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_amount' => $discount,
            'status' => 'redeemed',
            'redeemed_at' => Carbon::now(),
        ])->save();
    }

    public function releaseReservation(CouponRedemption $redemption, string $reason): bool
    {
        return DB::transaction(function () use ($redemption, $reason): bool {
            $affected = CouponRedemption::query()
                ->whereKey($redemption->id)
                ->where('status', 'reserved')
                ->update([
                    'status' => 'released',
                    'released_at' => Carbon::now(),
                    'release_reason' => $reason,
                ]);

            if ($affected === 0) {
                return false;
            }

            $coupon = $redemption->coupon ?: Coupon::find($redemption->coupon_id);
            if ($coupon) {
                $updates = ['used_count' => DB::raw('CASE WHEN used_count > 0 THEN used_count - 1 ELSE 0 END')];

                if ($coupon->usage_limit !== null) {
                    $updates['usage_limit'] = DB::raw('usage_limit + 1');
                }

                DB::table($coupon->getTable())
                    ->where('id', $coupon->id)
                    ->update($updates);
            }

            return true;
        });
    }

    public function releaseExpiredReservations(?CarbonInterface $now = null): int
    {
        $now ??= Carbon::now();
        $released = 0;

        CouponRedemption::query()
            ->with('coupon')
            ->where('status', 'reserved')
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<=', $now)
            ->orderBy('id')
            ->chunkById(100, function ($redemptions) use (&$released): void {
                foreach ($redemptions as $redemption) {
                    if ($this->releaseReservation($redemption, 'expired')) {
                        $released++;
                    }
                }
            });

        return $released;
    }

    public function countRedemptionsForCustomer(Coupon $coupon, ?int $customerId): int
    {
        if (! $customerId) {
            return 0;
        }

        return CouponRedemption::where('coupon_id', $coupon->id)
            ->where('customer_id', $customerId)
            ->whereIn('status', ['reserved', 'redeemed'])
            ->count();
    }

    public function recordRedemption(Coupon $coupon, ?int $customerId, ?int $orderId, float $discount): void
    {
        CouponRedemption::create([
            'coupon_id' => $coupon->id,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_amount' => $discount,
        ]);
    }
}
