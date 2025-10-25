<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        try {
            // TODO: Implement email sending logic
            Log::info("Order confirmation email sent for order: {$order->id}");
            
            // Example: Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation email: " . $e->getMessage());
        }
    }
}
