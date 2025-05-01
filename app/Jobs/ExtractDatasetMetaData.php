<?php

namespace App\Jobs;

use App\Models\Dataset;
use App\Services\Dataset\DatasetService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtractDatasetMetaData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $datasetId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DatasetService $datasetService): void
    {
        $dataset = $datasetService->findBy($this->datasetId);

        $url = "http://qanun_api_analyser:8001/dataset/{$dataset->getKey()}/extract-metadata";

        $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])
            ->get($url);

        if($response->successful())
            $datasetService->update($dataset, ['metadata' => $response->json()]);
        else{
            Log::channel('anylyzer_call')->info("Failed to extract metadata for dataset {$dataset->getKey()}", [
                'response' => $response->json(),
                'status' => $response->status(),
                'url' => $url
            ]);
        }
    }
}
