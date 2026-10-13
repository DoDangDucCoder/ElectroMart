<?php

namespace App\Console\Commands;

use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use Illuminate\Console\Command;

class ReleaseExpiredCouponReservationsCommand extends Command
{
    protected $signature = 'coupons:release-expired-reservations';

    protected $description = 'Release expired coupon reservations and return their usage slots.';

    public function handle(CouponRepositoryInterface $coupons): int
    {
        $released = $coupons->releaseExpiredReservations();

        $this->info("Released {$released} expired coupon reservation(s).");

        return self::SUCCESS;
    }
}
