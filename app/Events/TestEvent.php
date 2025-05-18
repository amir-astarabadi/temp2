<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private int $datasetId)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('test'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => '1',
            'name' => 'name',
            'status' => 'status',
            'message' => "dataset 1 has been inserted.",
        ];
    }
}
