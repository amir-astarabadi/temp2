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
        $project->user_id = $projectData['user_id'];
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

    public function search(int $userId, ?string $needle=null): Collection
    {
        return Project::where('user_id', $userId)
            ->when($needle ?? null, function ($queryBuilder) use ($needle) {
                return $queryBuilder->where('name', 'like', '%' . $needle . '%')
                    ->orWhere('description', 'like', '%' . $needle . '%');
            })
            ->select('id', 'name', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
