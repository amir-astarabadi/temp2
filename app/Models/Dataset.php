<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Observers\DatasetObserver;
use Illuminate\Support\Arr;
use Exception;

#[ObservedBy(DatasetObserver::class)]
class Dataset extends Model
{
    use HasFactory, SoftDeletes;

    public $casts = [
        'metadata' => 'array',
    ];

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

    public function dataentries()
    {
        return $this->hasMany(DataEntry::class, 'dataset_id', 'id');
    }

    public function charts()
    {
        return $this->hasMany(Chart::class, 'dataset_id');
    }

    public function getIsPinnedAttribute(): bool
    {
        return is_null($this->pinned_at) ? false : true;
    }

    public function getColumnsAttribute(): array
    {
        if(is_null($this->metadata)){
            return [];
        }
        return array_column($this->metadata, 'column');
    }


    public function getCategoricalColumnsAttribute(): array
    {
        if(is_null($this->metadata)){
            return [];
        }

        $categoricalColumns = Arr::where($this->metadata, fn($record) => $record['type'] === 'categorical');

        return array_column($categoricalColumns, 'column');
    }
}
