<?php

namespace App\Jobs;

use App\Services\Dataset\DatasetService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtractDatasetMetaData implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $datasetId)
    {
        // $this->onQueue('extract_metadata');
    }

    /**
     * Execute the job.
     */
    public function handle(DatasetService $datasetService): void
    {
        $dataset = $datasetService->findBy($this->datasetId);

        $url = config('analyser.extract_metadata') . $this->datasetId;

        $response = Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->get($url);

        if ($response->successful())
            $datasetService->update($dataset, ['metadata' => $response->json()]);
        else {
            Log::channel('anylyzer_call')->info("Failed to extract metadata for dataset {$dataset->getKey()}", [
                'response' => $response->json(),
                'status' => $response->status(),
                'url' => $url
            ]);
        }
    }
}
