<?php

namespace App\Observers;

use App\Events\BroadcastDatasetStatusUpdateEvent;
use Illuminate\Support\Facades\Log;
use App\Models\Dataset;
use App\Notifications\DatasetStatusChangedNotification;

class DatasetObserver
{
    public function saved(Dataset $dataset): void
    {
        if($dataset->isDirty('status')){
            $dataset->user->notify(new DatasetStatusChangedNotification($dataset->getKey()));
            BroadcastDatasetStatusUpdateEvent::dispatch($dataset->id);        
        }
    }
}
