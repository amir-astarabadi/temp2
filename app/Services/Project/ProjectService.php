<?php

namespace App\Services\Project;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function create(array $projectData): Project
    {
        $project = new Project();
        $project->name = $projectData['name'];
        $project->description = $projectData['description'];
        $project->owner_id = $projectData['owner_id'];
        $project->save();

        return $project;
    }

    public function search(int $owner, array $query): Collection
    {
        return Project::where('owner_id', $owner)
            ->when($query['name'] ?? null, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', '%' . $query['name'] . '%');
            })
            ->when($query['description'] ?? null, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('description', 'like', '%' . $query['description'] . '%');
            })
            ->select('id', 'name', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
