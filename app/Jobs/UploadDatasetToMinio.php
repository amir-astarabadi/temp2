<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Enums\DatasetStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Models\Dataset;

class UploadDatasetToMinio implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly int $datasetId
    ) {}

    public function handle(DatasetService $datasetService): void
    {
        if (!Dataset::find($this->datasetId)) {
            Log::channel('datasets')->warning("$this->datasetId dataset does not exist:");
            return;
        }

        if (!file_exists($this->from)) {
            Log::channel('datasets')->warning("Source file does not exist: {$this->from}");
            return;
        }
        if (!is_readable($this->from)) {
            Log::channel('datasets')->warning("Source file is not readable: {$this->from}");
            return;
        }
        
        $stream = fopen($this->from, 'rb');
        if (!$stream) {
            Log::channel('datasets')->warning("Failed to open stream from: {$this->from}");
            return;
        }

        Storage::disk(config('filesystems.default'))->put($this->to, $stream);

        fclose($stream);

        unlink($this->from);

        $datasetService->update($this->datasetId, ['status' => DatasetStatusEnum::UPLOADED->value]);
    }
}
