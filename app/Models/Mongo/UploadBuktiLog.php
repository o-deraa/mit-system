<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class UploadBuktiLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'upload_bukti_logs';
    protected $guarded = [];
}
