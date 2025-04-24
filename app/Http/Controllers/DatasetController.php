<?php

namespace App\Http\Controllers;

use App\Enums\DatasetStatusEnum;
use App\Http\Resources\Project\ProjectResourceCollection;
use App\Http\Requests\Dataset\DatasetCreateRequest;
use App\Services\Storage\DatasetStorageService;
use App\Http\Resources\Dataset\DatasetResource;
use Illuminate\Http\Response as HttpResponse;
use App\Http\Requests\DatasetIndexRequest;
use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Bus;
use App\Jobs\UploadDatasetToMinio;
use App\Jobs\StoreDataEntries;
use App\Responses\Response;
use Dflydev\DotAccessData\Data;

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

        Bus::chain([
            new UploadDatasetToMinio($tempPath, $finalPath, $dataset->getKey()),
            new StoreDataEntries($dataset->getKey()),
            fn() => $this->datasetService->update($dataset, ['status' => DatasetStatusEnum::INSERTED->value]),
        ])->dispatch();

        return Response::success(
            message: 'Dataset uploaded successfully.',
            data: DatasetResource::make($dataset->refresh()),
            code: HttpResponse::HTTP_CREATED
        );
    }
}
