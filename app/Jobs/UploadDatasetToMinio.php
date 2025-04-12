<?php

namespace App\Jobs;

use App\Services\Dataset\DatasetService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
        Storage::disk('dataset')->put($this->to, $stream);
        
        fclose($stream);

        unlink($this->from);

        $datasetService->update($this->datasetId, ['status' => 'uploaded']);
    }
}
