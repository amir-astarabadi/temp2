<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DatasetStorageService
{
    public function getDestination(string $filename, int $ownerId, int $projectId): string
    {
        return $this->getFilePath($ownerId, $projectId) . $this->getFileName($filename);
    }

    public function storeTemp(UploadedFile $file): string
    {
        $tempName =  Str::random(20) . '.' . $file->getClientOriginalExtension();

        return $file->move(sys_get_temp_dir(), $tempName)->getPathname();
    }

    private function getFileName(string $originalFileName): string
    {
        $prefix = now()->format('Y_m_d_i_s_');
        $originalFileName = str_replace(' ', '_', $originalFileName);
        return $prefix . $originalFileName;
    }

    private function getFilePath(int $ownerId, int $projectId): string
    {

        return "$ownerId/$projectId/";
    }
}
