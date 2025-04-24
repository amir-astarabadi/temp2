<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Dataset extends Model
{
    use HasFactory;

    public function getFileUrlAttribute()
    {
        try{
            return Storage::disk('dataset')->temporaryUrl($this->file_path, now()->addDays(1));
        }catch(Exception $e){
            return "no path set!";
        }
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
