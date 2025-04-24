<?php

namespace App\Imports;

use App\Models\DataEntry;
use App\Services\DatasetEntry\DataEntryService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class DataEntryImport implements OnEachRow, WithChunkReading
{
    static array $headers = [];

    static array $dataBuffer = [];

    private static ?DataEntryService $dataEntryService = null;

    public function __construct(
        private readonly int $datasetId,
        private readonly int $totalRows
    ) 
    {
        self::$dataEntryService = app(DataEntryService::class);
    }

    public function onRow(Row $row)
    {
        if ($row->getIndex() === 1) {
            self::$headers = $row->toArray();
            return;
        }

        if (empty(array_filter($row->toArray()))) {
            return;
        }

        $decoratedData = self::decorateData($row->toArray());

        if (empty($decoratedData)) {
            return;
        }

        self::$dataBuffer[] = [
            'dataset_id' => $this->datasetId,
            'row_index' => $row->getIndex(),
            'data' => $decoratedData,
        ];

        if (count(self::$dataBuffer) >= $this->chunkSize() || $row->getIndex() === ($this->totalRows - 1)) {
            self::insertBufferEntry();
        }
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function chunkReading(): bool
    {
        return true;
    }

    public static function insertBufferEntry(): void
    {
        if (!empty(self::$dataBuffer)) {
            self::$dataEntryService->insert(self::$dataBuffer);
            self::$dataBuffer = [];
        }
    }

    public function decorateData(array $rawData): array
    {
        $decoratedData = [];
        foreach ($rawData as $key => $value) {
            if (!isset(self::$headers[$key])) {
                continue;
            }
            $decoratedData[self::$headers[$key]] = $value;
        }

        return $decoratedData;
    }
}
