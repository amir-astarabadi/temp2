<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('datasets.{datasetId}', function (User $user, int $datasetId) {
    return $user->datasets()->select('id')->get()->contains($datasetId);
});

Broadcast::channel('test', function () {
    return true;
});
