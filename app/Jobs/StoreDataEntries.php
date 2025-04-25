<?php

namespace App\Jobs;

use App\Services\DatasetEntry\DataEntryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Dataset\DatasetService;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\DatasetStatusEnum;
use App\Imports\DataEntryImport;
use App\Models\Dataset;
use Exception;

class StoreDataEntries implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $datasetId){}

    public function handle(DatasetService $datasetService, DataEntryService $dataEntryService): void
    {
        $dataset = Dataset::findOrFail($this->datasetId);
        try{
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::INSERTING->value]);
            $tempImport = Excel::toCollection(null, $dataset->file_path, config('filesystems.default'));

            $totalRows = $tempImport[0]->count();
            Excel::import(
                new DataEntryImport($this->datasetId, $totalRows),
                $dataset->file_path,
                config('filesystems.default'),
            );
            DataEntryImport::insertBufferEntry();
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::INSERTED->value]);
        }catch(Exception $e){
            $dataEntryService->delete($dataset->getKey());
            $datasetService->update($dataset, ['status' => DatasetStatusEnum::ERROR->value]);
            dd($e->getMessage());
            return;
        }
    }
}
