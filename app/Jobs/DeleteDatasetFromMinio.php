<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Storage;

class DeleteDatasetFromMinio implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $datasetId) {}

    /**
     * Execute the job.
     */
    public function handle(DatasetService $datasetService): void
    {
        $dataset = $datasetService->findBy(value: $this->datasetId, withTrashed: true);

        if (Storage::disk(config('filesystems.default'))->exists($dataset->file_path)) {
            Storage::disk(config('filesystems.default'))->delete($dataset->file_path);
        }
    }
}
