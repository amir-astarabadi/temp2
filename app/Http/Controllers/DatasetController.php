<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dataset\DatasetCreateRequest;
use App\Http\Requests\DatasetIndexRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Http\Resources\Project\ProjectResourceCollection;
use App\Services\Storage\DatasetStorageService;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Dataset\DatasetService;
use App\Jobs\UploadDatasetToMinio;
use App\Responses\Response;

class DatasetController extends Controller
{
    public function __construct(private DatasetService $datasetService) {}

    public function index(DatasetIndexRequest $request)
    {
        $projects = $this->datasetService->search(auth()->id(), $request->validated('query'));

        return ProjectResourceCollection::make($projects);
    }

    public function store(DatasetCreateRequest $request, DatasetStorageService $storageService)
    {
        $tempPath = $storageService->storeTemp($request->file('dataset'));
        $finalPath = $storageService->getDestination(
            filename: $request->file('dataset')->getClientOriginalName(),
            ownerId: auth()->id(),
            projectId: $request->validated('project_id')
        );

        $datastData = array_merge(
            $request->validated(),
            [
                'user_id' => auth()->id(),
                'file_path' => $finalPath,
                'type' => $request->file('dataset')->getClientOriginalExtension(),
            ]
        );
        
        $dataset = $this->datasetService->create($datastData);

        UploadDatasetToMinio::dispatch($tempPath, $finalPath, $dataset->getKey());

        return Response::success(
            message: 'Dataset uploaded successfully.',
            data: DatasetResource::make($dataset),
            code: HttpResponse::HTTP_CREATED
        );
    }
}
