<?php

namespace App\Events;

use App\Models\Dataset;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastDatasetStatusUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private int $datasetId)
    {
        //
    }

    public function broadcastAs(): string
    {
        return 'dataset.status.update';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('datasets.' . $this->datasetId),
        ];
    }

    public function broadcastWith(): array
    {
        $dataset = Dataset::find($this->datasetId);

        if (!$dataset) {
            return [
                'message' => "dataset $this->datasetId has been deleted.",
            ];
        }

        return [
            'id' => $this->datasetId,
            'project_id' => $dataset->project_id,
            'name' => $dataset->name,
            'status' => $dataset->status,
            'message' => "dataset $dataset->title status moved to $dataset->status.",
        ];
    }
}
