<?php

namespace App\Services\Dataset;

use App\Enums\DatasetStatusEnum;
use App\Models\Dataset;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class DatasetService
{
    public function create(array $datasetData): Dataset
    {
        $dataset = new Dataset();
        $dataset->name = $datasetData['name'];
        $dataset->description = $datasetData['description'];
        $dataset->user_id = $datasetData['user_id'];
        $dataset->project_id = $datasetData['project_id'];
        $dataset->type = $datasetData['type'];
        $dataset->file_path = $datasetData['file_path'];
        $dataset->status = DatasetStatusEnum::UPLOADING->value;

        if (Dataset::where([
            'user_id' => $datasetData['user_id'],
            'project_id' => $datasetData['project_id'],
            'name' => $datasetData['name']
        ])->exists()) {
            $name = $datasetData['name'];
            $dataset->name = now()->timestamp . '_' . $name;
        }

        $dataset->save();

        return $dataset;
    }

    public function update(int|Dataset $dataset, array $datasetData): Dataset
    {
        if (is_integer($dataset)) {
            $dataset = Dataset::findOrFail($dataset);
        }
        foreach ($datasetData as $datasetProperty => $newValue) {
            $dataset->{$datasetProperty} = $newValue;
        }
        $dataset->save();
        return $dataset;
    }

    public function search(int $owner, ?string $needle = null): Collection
    {
        $datasets = Dataset::query()
            ->select(['project_id', 'user_id'])
            ->when($needle, function ($datasetQuery) use ($needle) {
                return $datasetQuery->where('name', 'like', '%' . $needle . '%')
                    ->orWhere('description', 'like', '%' . $needle . '%');
            })
            ->get();
        

        return Project::with('datasets')
            ->whereIn('id', $datasets->pluck('project_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
