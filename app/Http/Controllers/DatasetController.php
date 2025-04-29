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
use stdClass;

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

    public function metadata(Dataset $dataset)
    {
        if ($dataset->user_id !== auth()->id()) {
            return Response::error("Datasest does not belong to you.", code: HttpResponse::HTTP_FORBIDDEN);
        }

        $metadata = [
            "subject_id" => [
                "type" => "numeric",
                "min" => 1.0,
                "max" => 10000.0,
                "mean" => 5001.562779357984,
                "median" => 4999.5,
                "missing" => 0,
                "unique_values" => 9844
            ],
            "age" => [
                "type" => "numeric",
                "min" => -1.7070645652527,
                "max" => 1.70475006074055,
                "mean" => 7.362389175897015e-17,
                "median" => 0.0277564310150521,
                "missing" => 0,
                "unique_values" => 60
            ],
            "sex" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 4946,
                    "1" => 4898
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "weight" => [
                "type" => "numeric",
                "min" => -3.02317959515361,
                "max" => 3.04918244881979,
                "mean" => 2.5984902973754174e-16,
                "median" => -0.0117665048272206,
                "missing" => 0,
                "unique_values" => 9844
            ],
            "height" => [
                "type" => "numeric",
                "min" => -3.01691094832739,
                "max" => 3.0200919221608,
                "mean" => 1.3430946724559187e-15,
                "median" => -0.0161006844329794,
                "missing" => 0,
                "unique_values" => 9844
            ],
            "cholesterol" => [
                "type" => "numeric",
                "min" => -3.03717609456557,
                "max" => 3.06338952122482,
                "mean" => -3.897735446063126e-16,
                "median" => 0.0,
                "missing" => 0,
                "unique_values" => 9746
            ],
            "blood_pressure" => [
                "type" => "numeric",
                "min" => -2.98599567199443,
                "max" => 2.94616216701777,
                "mean" => -1.9849578660506658e-16,
                "median" => 0.0,
                "missing" => 0,
                "unique_values" => 9747
            ],
            "calorie_intake" => [
                "type" => "numeric",
                "min" => -3.00609459285152,
                "max" => 3.0443376180906,
                "mean" => -5.491115260356524e-16,
                "median" => 0.01367937080443705,
                "missing" => 0,
                "unique_values" => 9844
            ],
            "diabetes" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "1" => 6531,
                    "0" => 3313
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "checkup_date" => [
                "type" => "categorical",
                "top_values" => [
                    "2020-09-15" => 26,
                    "2020-03-19" => 23,
                    "2021-06-09" => 23,
                    "2020-09-22" => 22,
                    "2020-03-10" => 22,
                    "2021-01-22" => 22,
                    "2021-04-16" => 22,
                    "2021-07-29" => 22,
                    "2021-08-21" => 21,
                    "2021-06-11" => 21
                ],
                "missing" => 0,
                "unique_values" => 732
            ],
            "bmi" => [
                "type" => "numeric",
                "min" => -2.88568977860425,
                "max" => 3.09221311003822,
                "mean" => 2.0643561806926925e-16,
                "median" => -0.045779647554512304,
                "missing" => 0,
                "unique_values" => 9844
            ],
            
            "exercise_frequency_0" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8426,
                    "1" => 1418
                ],
                "missing" => 0,
                "unique_values" => 2
            ],            
            "cancer stage" => [
                "type" => "categorical",
                "top_values" => [
                    "Stage 2" => 5590,
                    "Stage 1" => 1371,
                ],
                "missing" => 0,
                "unique_values" => 2
            ],

 
            "exercise_frequency_5" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8432,
                    "1" => 1412
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "exercise_frequency_3" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8379,
                    "1" => 1465
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "exercise_frequency_1" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8455,
                    "1" => 1389
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "exercise_frequency_2" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8473,
                    "1" => 1371
                ],
                "missing" => 0,
                "unique_values" => 2
            ],
            "exercise_frequency_6" => [
                "type" => "categorical (numeric labels)",
                "top_values" => [
                    "0" => 8436,
                    "1" => 1408
                ],
                "missing" => 0,
                "unique_values" => 2
            ]
        ];

        $metadata = $this->preserveNumericKeys($metadata);

        return Response::success(
            message: 'Dataset metadata fetched successfully.',
            data: $metadata
        );
    }

    private function preserveNumericKeys($array)
    {
        $result = new stdClass();
    
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Recursively handle nested arrays
                $result->$key = $this->preserveNumericKeys($value);
            } else {
                $result->$key = $value;
            }
        }
    
        return $result;
    }
}
