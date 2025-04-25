<?php

namespace App\Models;

use App\Observers\DatasetObserver;
use Exception;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(DatasetObserver::class)]
class Dataset extends Model
{
    use HasFactory, SoftDeletes;

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

    public function project():BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
