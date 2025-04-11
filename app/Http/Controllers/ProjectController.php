<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Project\ProjectService;
use App\Responses\Response;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}

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

    public function index(ProjectIndexRequest $request)
    {
        $projects = $this->projectService->search(owner: auth()->id(), query: $request->validated());

        return Response::success(
            message: 'Project created successfully.',
            code: HttpResponse::HTTP_OK,
            data: $projects,
        );
    }
}
