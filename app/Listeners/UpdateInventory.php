<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateInventory implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        try {
            foreach ($order->orderDetails as $detail) {
                DB::table('products')
                    ->where('id', $detail->product_id)
                    ->decrement('quantity', $detail->quantity);
            }
            
            Log::info("Inventory updated for order: {$order->id}");
        } catch (\Exception $e) {
            Log::error("Failed to update inventory: " . $e->getMessage());
        }
    }
}