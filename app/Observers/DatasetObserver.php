<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Models\Dataset;
use App\Notifications\DatasetStatusChangedNotification;

class DatasetObserver
{
    public function saved(Dataset $dataset): void
    {
        if($dataset->isDirty('status')){
            $dataset->user->notify(new DatasetStatusChangedNotification($dataset->getKey()));
        }
    }
}
