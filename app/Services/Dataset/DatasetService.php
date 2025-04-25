<?php

namespace App\Services\Dataset;

use Illuminate\Database\Eloquent\Collection;
use App\Enums\DatasetStatusEnum;
use App\Models\Dataset;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DatasetService
{
    public function findBy(int|string $value, string $identifier = 'id', bool $withTrashed = false): ?Dataset
    {
        $query = Dataset::where($identifier, '=', $value);
        if ($withTrashed) {
            $query->withTrashed();
        }
        return $query->first();
    }

    public function create(array $datasetData): Dataset
    {
        $latestDatasestOrder = $this->getLatestDatasetOrder($datasetData['user_id'], $datasetData['project_id']);

        $dataset = new Dataset();
        $dataset->name = $datasetData['name'];
        $dataset->description = $datasetData['description'];
        $dataset->user_id = $datasetData['user_id'];
        $dataset->project_id = $datasetData['project_id'];
        $dataset->type = $datasetData['type'];
        $dataset->file_path = $datasetData['file_path'];
        $dataset->status = DatasetStatusEnum::UPLOADING->value;
        $dataset->order = $latestDatasestOrder + 1;

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

    public function pin(Dataset $dataset): bool
    {
        DB::transaction(function () use ($dataset) {
            $this->update($dataset, ['is_pinned' => true, 'order' => 1]);
            $datasets = Dataset::where('id', '!=', $dataset->id)
                ->where('user_id', $dataset->user_id)
                ->where('project_id', $dataset->project_id)
                ->orderBy('order', 'asc')
                ->get();
            $nextOrder = 2;
            foreach ($datasets as $dataset) {
                $this->update($dataset, ['order' => $nextOrder]);
                $nextOrder++;
            }
        });

        return $dataset->is_pinned;
    }

    public function search(int $userId, ?string $needle = null): Collection
    {
        $datasets = Dataset::query()->where('user_id', $userId)
            ->select(['project_id', 'user_id'])
            ->when($needle, function ($datasetQuery) use ($needle) {
                return $datasetQuery->where('name', 'like', '%' . $needle . '%')
                    ->orWhere('description', 'like', '%' . $needle . '%');
            })
            ->get();


        return Project::with(['datasets' => fn($query) => $query->orderBy('order')])
            ->whereIn('id', $datasets->pluck('project_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getLatestDatasetOrder(int $userId, int $projectId): int
    {
        return Dataset::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->select('order')
            ->orderBy('order', 'desc')
            ->limit(1)
            ->first()
            ?->order ?? 0;
    }
}
