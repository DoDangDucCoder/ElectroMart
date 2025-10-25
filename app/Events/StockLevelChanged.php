<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockLevelChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly mixed $data
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'StockLevelChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }
}
