<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_coupon_index_only_exposes_active_currently_valid_coupons(): void
    {
        $valid = $this->createCoupon([
            'code' => 'VALID-10',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->createCoupon([
            'code' => 'INACTIVE-10',
            'is_active' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->createCoupon([
            'code' => 'EXPIRED-10',
            'is_active' => true,
            'starts_at' => now()->subDays(10),
            'expires_at' => now()->subDay(),
        ]);

        $this->createCoupon([
            'code' => 'FUTURE-10',
            'is_active' => true,
            'starts_at' => now()->addDay(),
            'expires_at' => now()->addDays(10),
        ]);

        $this->getJson('/api/v1/coupons')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('data.0.code', $valid->code);
    }

    public function test_public_coupon_show_hides_inactive_and_expired_coupons(): void
    {
        $valid = $this->createCoupon([
            'code' => 'SHOW-VALID',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->createCoupon([
            'code' => 'SHOW-INACTIVE',
            'is_active' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->createCoupon([
            'code' => 'SHOW-EXPIRED',
            'is_active' => true,
            'starts_at' => now()->subDays(5),
            'expires_at' => now()->subDay(),
        ]);

        $this->getJson('/api/v1/coupons/' . $valid->code)
            ->assertOk()
            ->assertJsonPath('data.code', $valid->code);

        $this->getJson('/api/v1/coupons/SHOW-INACTIVE')
            ->assertNotFound()
            ->assertJsonPath('success', false);

        $this->getJson('/api/v1/coupons/SHOW-EXPIRED')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_coupon_validate_returns_bad_request_for_invalid_or_inactive_code(): void
    {
        $this->createCoupon([
            'code' => 'LOCKED',
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/coupons/validate', [
            'code' => 'LOCKED',
            'total_amount' => 1000000,
        ])->assertStatus(400)
            ->assertJsonPath('success', false);

        $this->postJson('/api/v1/coupons/validate', [
            'code' => 'MISSING',
            'total_amount' => 1000000,
        ])->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    protected function createCoupon(array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'CP-' . uniqid(),
            'name' => 'Test Coupon',
            'type' => 'fixed',
            'value' => 100000,
            'min_order_total' => 0,
            'max_discount' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'usage_per_customer' => null,
            'is_stackable' => false,
            'is_active' => true,
            'starts_at' => null,
            'expires_at' => null,
            'applicable_segments' => null,
            'meta' => null,
        ], $attributes));
    }
}
