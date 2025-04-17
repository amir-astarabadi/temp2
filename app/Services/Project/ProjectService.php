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

    public function update(array $projectData, Project $project): Project
    {
        foreach ($projectData as $projectProperty => $newValue) {
            $project->{$projectProperty} = $newValue;
        }

        $project->save();

        return $project;
    }

    public function search(int $owner, array $query): Collection
    {
        return Project::where('owner_id', $owner)
            ->when($query['query'] ?? null, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', '%' . $query['query'] . '%')
                    ->orWhere('description', 'like', '%' . $query['query'] . '%');
            })
            ->select('id', 'name', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
