<?php

namespace App\Jobs;

use App\Services\DatasetEntry\DataEntryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Dataset\DatasetService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Enums\DatasetStatusEnum;
use App\Imports\DataEntryImport;
use App\Models\Dataset;
use Exception;

class StoreDataEntries implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $datasetId) {}

    public function handle(DatasetService $datasetService, DataEntryService $dataEntryService): void
    {
        $dataset = $datasetService->findBy($this->datasetId);
        if (!$dataset) {
            Log::channel('datasets')->warning("$this->datasetId dataset does not exist:");
            return;
        }

        try {
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::INSERTING->value]);
            $tempImport = Excel::toCollection(null, $dataset->file_path, config('filesystems.default'));

            $totalRows = $tempImport[0]->count();
            Excel::import(
                new DataEntryImport($dataset->getKey(), $totalRows),
                $dataset->file_path,
                config('filesystems.default'),
            );
            DataEntryImport::insertBufferEntry();
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::INSERTED->value]);
        } catch (Exception $e) {
            $dataEntryService->delete($dataset->getKey());
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::ERROR->value]);
            Log::channel('datasets')->warning("error in storing $this->datasetId dataset: " . $e->getMessage());

            return;
        }
    }
}
