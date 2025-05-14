<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    use HasFactory;

    public $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'chart_layout' => 'array',
    ];

    public const TYPES = [
        'pie',
        'line',
        'bar',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public static function getTypes()
    {
        return implode(',', self::TYPES);
    }
}
