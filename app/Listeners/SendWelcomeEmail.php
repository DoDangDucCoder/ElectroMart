<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        
        try {
            // TODO: Implement welcome email sending logic
            Log::info("Welcome email sent to customer: {$customer->email}");
            
            // Example: Mail::to($customer->email)->send(new WelcomeMail($customer));
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: " . $e->getMessage());
        }
    }
}