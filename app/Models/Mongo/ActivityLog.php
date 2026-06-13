<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class ActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'activity_logs';
    protected $guarded = [];
}
