<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrackProductView implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public int $timeout = 30;

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(ProductViewed $event): void
    {
        try {
            DB::table('product_views')->insert([
                'product_id' => $event->product->id,
                'customer_id' => $event->customerId,
                'ip_address' => $event->ipAddress,
                'viewed_at' => now(),
            ]);
            
            Log::info("Product view tracked for product: {$event->product->id}");
        } catch (Throwable $exception) {
            Log::error('Failed to track product view.', [
                'product_id' => $event->product->id ?? null,
                'customer_id' => $event->customerId,
                'ip_address' => $event->ipAddress,
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(ProductViewed $event, Throwable $exception): void
    {
        Log::error('Product view tracking job failed permanently.', [
            'product_id' => $event->product->id ?? null,
            'customer_id' => $event->customerId,
            'ip_address' => $event->ipAddress,
            'exception' => $exception->getMessage(),
        ]);
    }
}
