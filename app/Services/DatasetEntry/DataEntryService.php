<?php

namespace App\Services\DatasetEntry;

use App\Models\DataEntry;

class DataEntryService
{
    public function delete(int $datasetId)
    {
        return DataEntry::where('dataset_id', '=', $datasetId)->delete();
    }

    public function insert(array $datasetData)
    {
        DataEntry::insert($datasetData);
    }
}
