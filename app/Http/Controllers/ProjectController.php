<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Project\ProjectResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Project\ProjectService;
use App\Responses\Response;
use App\Models\Project;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}


    public function show(Project $project)
    {
        return Response::success(
            message: 'Project retrieved successfully.',
            code: HttpResponse::HTTP_OK,
            data: ProjectResource::make($project),
        );
    }

    public function index(ProjectIndexRequest $request)
    {
        $projects = $this->projectService->search(owner: auth()->id(), query: $request->validated());

        return Response::success(
            message: 'Project created successfully.',
            code: HttpResponse::HTTP_OK,
            data: ProjectResourceCollection::make($projects),
        );
    }

    public function store(ProjectCreateRequest $request)
    {
        $project = $this->projectService->create($request->validated());

        return Response::success(
            message: 'Project created successfully.',
            code: HttpResponse::HTTP_CREATED,
            data: [
                'id' => $project->getKey(),
                'name' => $project->name,
                'description' => $project->description,
                'created_at' => $project->created_at,
            ],
        );
    }

    public function update(Project $project,ProjectUpdateRequest $request)
    {
        $project = $this->projectService->update($request->validated(), $project);

        return Response::success(
            message: 'Project updated successfully.',
            code: HttpResponse::HTTP_OK,
            data: ProjectResource::make($project),
        );
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return Response::success(
            message: 'Project deleted successfully.',
            code: HttpResponse::HTTP_OK,
        );
    }
}
