<?php

namespace App\Http\Controllers;

use App\Http\Resources\Project\ProjectResourceCollection;
use App\Http\Requests\Dataset\DatasetCreateRequest;
use App\Http\Requests\Dataset\DatasetUpdateRequest;
use App\Services\Storage\DatasetStorageService;
use App\Http\Resources\Dataset\DatasetResource;
use Illuminate\Http\Response as HttpResponse;
use App\Http\Requests\DatasetIndexRequest;
use App\Jobs\DeleteDatasetFromMinio;
use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Bus;
use App\Jobs\UploadDatasetToMinio;
use App\Jobs\StoreDataEntries;
use App\Responses\Response;
use App\Models\Dataset;

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
            new StoreDataEntries($dataset->getKey())
        ])->dispatch();

        return Response::success(
            message: 'Dataset uploaded successfully.',
            data: DatasetResource::make($dataset->refresh()),
            code: HttpResponse::HTTP_CREATED
        );
    }

    public function update(DatasetUpdateRequest $request, Dataset $dataset)
    {
        if ($dataset->user_id !== auth()->id()) {
            return Response::error("Datasest does not belong to you.", code: HttpResponse::HTTP_FORBIDDEN);
        }

        $this->datasetService->update($dataset, $request->validated());

        return Response::success(
            message: 'Dataset updated successfully.',
            data: DatasetResource::make($dataset->refresh())
        );
    }

    public function pin(Dataset $dataset)
    {
        if ($dataset->user_id !== auth()->id()) {
            return Response::error("Datasest does not belong to you.", code: HttpResponse::HTTP_FORBIDDEN);
        }

        if (!$this->datasetService->pin($dataset)) {
            return Response::error("Dataset pinning failed.", code: HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::success(
            message: 'Dataset pinned successfully.',
            data: DatasetResource::make($dataset->refresh())
        );
    }

    public function unpin(Dataset $dataset)
    {
        if ($dataset->user_id !== auth()->id()) {
            return Response::error("Datasest does not belong to you.", code: HttpResponse::HTTP_FORBIDDEN);
        }

        if (!$this->datasetService->unpin($dataset)) {
            return Response::error("Dataset pinning failed.", code: HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::success(
            message: 'Dataset un pinned successfully.',
            data: DatasetResource::make($dataset->refresh())
        );
    }

    public function destroy(Dataset $dataset)
    {
        if ($dataset->user_id !== auth()->id()) {
            return Response::error("Datasest does not belong to you.", code: HttpResponse::HTTP_FORBIDDEN);
        }

        $dataset->delete();
        DeleteDatasetFromMinio::dispatch($dataset->getKey());

        return Response::success(
            message: "Dataset deleted successfully."
        );
    }
}
