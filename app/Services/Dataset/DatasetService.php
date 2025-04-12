<?php

namespace App\Services\Dataset;

use App\Models\Dataset;

class DatasetService
{
    public function create(array $datasetData): Dataset
    {
        $dataset = new Dataset();
        $dataset->name = $datasetData['name'];
        $dataset->description = $datasetData['description'];
        $dataset->owner_id = $datasetData['owner_id'];
        $dataset->project_id = $datasetData['project_id'];
        $dataset->type = $datasetData['type'];
        $dataset->file_path = $datasetData['file_path'];
        $dataset->status = 'uploading';

        if(Dataset::where([
            'owner_id' => $datasetData['owner_id'],
            'project_id' => $datasetData['project_id'],
            'name' => $datasetData['name']
        ])->exists()){
            $name = $datasetData['name'];
            $dataset->name = 'new ' . $name;
        }

        $dataset->save();

        return $dataset;
    }

    public function update(int|Dataset $dataset, array $datasetData): Dataset
    {
        if(is_integer($dataset)){
            $dataset = Dataset::findOrFail($dataset);
        }
        foreach($datasetData as $datasetProperty => $newValue){
            $dataset->{$datasetProperty} = $newValue;
        }
        $dataset->save();
        return $dataset;
    }
}
