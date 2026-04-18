<?php

namespace Tests\Feature;

use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    public function test_unset_coupon_returns_clear_feedback_when_no_coupon_is_in_session(): void
    {
        $this->from('/gio-hang')
            ->get('/unset-coupon')
            ->assertRedirect('/gio-hang')
            ->assertSessionHas('error', 'Không có mã khuyến mãi nào để xóa.');
    }
}
