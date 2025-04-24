<?php

namespace App\Enums;

enum DatasetStatusEnum: string
{
    case UPLOADING = 'uploading';
    case UPLOADED = 'uploaded';
    case INSERTING = 'inserting';
    case INSERTED = 'inserted';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case ERROR = 'error';
}
