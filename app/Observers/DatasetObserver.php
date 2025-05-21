<?php

namespace App\Observers;

use App\Events\BroadcastDatasetStatusUpdateEvent;
use App\Models\Dataset;

class DatasetObserver
{
    public function saved(Dataset $dataset): void
    {
        if($dataset->isDirty('status')){
            BroadcastDatasetStatusUpdateEvent::dispatch($dataset->id);        
        }
    }
}
