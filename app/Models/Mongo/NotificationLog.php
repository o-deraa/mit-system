<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class NotificationLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notification_logs';
    protected $guarded = [];
}
