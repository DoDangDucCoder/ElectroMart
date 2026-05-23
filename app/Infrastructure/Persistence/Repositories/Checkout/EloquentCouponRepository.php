<?php

namespace App\Infrastructure\Persistence\Repositories\Checkout;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use Carbon\Carbon;
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
                    ->orWhereColumn('used_count', '<', 'usage_limit');
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
            // Không giới hạn — increment thường là đủ
            $coupon->increment('used_count');
            return;
        }

        // Atomic update: chỉ tăng nếu CHƯA vượt giới hạn (chống race condition)
        $affected = DB::table($coupon->getTable())
            ->where('id', $coupon->id)
            ->whereColumn('used_count', '<', 'usage_limit')
            ->update(['used_count' => DB::raw('used_count + 1')]);

        if ($affected === 0) {
            throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
        }
    }

    public function countRedemptionsForCustomer(Coupon $coupon, ?int $customerId): int
    {
        if (! $customerId) {
            return 0;
        }

        return CouponRedemption::where('coupon_id', $coupon->id)
            ->where('customer_id', $customerId)
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
