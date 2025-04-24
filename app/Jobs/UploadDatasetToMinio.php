<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Storage;
use App\Enums\DatasetStatusEnum;
use App\Enums\StorageDiskEnum;

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

        if (!file_exists($this->from)) {
            throw new \Exception("Source file does not exist: {$this->from}");
        }
        if (!is_readable($this->from)) {
            throw new \Exception("Source file is not readable: {$this->from}");
        }
        $stream = fopen($this->from, 'rb');
        if (!$stream) {
            throw new \Exception("Failed to open stream from: {$this->from}");
        }
        Storage::disk(config('filesystems.default'))->put($this->to, $stream);
        
        fclose($stream);

        unlink($this->from);

        $datasetService->update($this->datasetId, ['status' => DatasetStatusEnum::UPLOADED->value]);
    }
}
