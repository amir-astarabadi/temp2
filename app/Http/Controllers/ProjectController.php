<?php

namespace App\Http\Controllers;

use App\Http\Resources\Project\ProjectResourceCollection;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Resources\Dataset\DatasetResourceCollection;
use App\Http\Resources\Project\ProjectResource;
use App\Jobs\DeleteDatasetFromMinio;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Dataset\DatasetService;
use App\Services\Project\ProjectService;
use App\Responses\Response;
use App\Models\Project;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService, private DatasetService $datasetService) {}


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
        $projects = $this->projectService->search(userId: auth()->id(), needle: $request->validated('query'));
        $pinnedDatasets = $this->datasetService->getPinnedDatasets(userId: auth()->id());

        $data = [
            'projects' => ProjectResourceCollection::make($projects),
            'pinned_datasets' => DatasetResourceCollection::make($pinnedDatasets),
        ];

        return Response::success(
            message: 'Ok',
            code: HttpResponse::HTTP_OK,
            data: $data,
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

    public function update(Project $project, ProjectUpdateRequest $request)
    {
        if ($project->user_id !== auth()->id()) {
            return Response::error(
                message: 'Project does not belong to you.',
                code: HttpResponse::HTTP_FORBIDDEN,
            );
        }

        $project = $this->projectService->update($request->validated(), $project);

        return Response::success(
            message: 'Project updated successfully.',
            code: HttpResponse::HTTP_OK,
            data: ProjectResource::make($project),
        );
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            return Response::error(
                message: 'Project does not belong to you.',
                code: HttpResponse::HTTP_FORBIDDEN,
            );
        }
        $project->datasets()->each(function ($dataset) {
            $dataset->delete();
            DeleteDatasetFromMinio::dispatch($dataset->getKey());
        });

        $project->delete();

        return Response::success(
            message: 'Project deleted successfully.',
            code: HttpResponse::HTTP_OK,
        );
    }
}
