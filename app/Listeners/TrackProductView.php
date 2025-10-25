<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackProductView implements ShouldQueue
{
    use InteractsWithQueue;

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
        } catch (\Exception $e) {
            Log::error("Failed to track product view: " . $e->getMessage());
        }
    }
}