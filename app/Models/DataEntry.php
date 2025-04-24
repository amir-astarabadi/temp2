<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DataEntry extends Model
{
    protected $connection = "mongodb";

    protected $collection = "data_entries";
}
