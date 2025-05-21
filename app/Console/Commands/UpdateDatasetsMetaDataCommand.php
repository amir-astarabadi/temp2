<?php

namespace App\Console\Commands;

use App\Jobs\ExtractDatasetMetaData;
use App\Models\Dataset;
use Illuminate\Console\Command;

class UpdateDatasetsMetaDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanun:update-datasets-meta-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'base new algorithms we may need to update old datasets metadata';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Dataset::chunkById(1000, function ($datasetChunk) {
            foreach ($datasetChunk as $dataset) {
                $this->info("Updating dataset {$dataset->getKey()} metadata");
                ExtractDatasetMetaData::dispatch($dataset->getKey());;
            }
        });
    }
}
