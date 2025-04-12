<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dataset extends Model
{
    use HasFactory;

    public function getFileUrlAttribute()
    {
        return Storage::disk('dataset')->temporaryUrl($this->file_path, now()->addDays(1));
    }
}
