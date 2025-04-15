<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dataset\DatasetCreateRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Services\Storage\DatasetStorageService;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Dataset\DatasetService;
use App\Jobs\UploadDatasetToMinio;
use App\Responses\Response;

class DatasetController extends Controller
{
    public function __construct(private DatasetService $datasetService) {}

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
                'owner_id' => auth()->id(),
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
