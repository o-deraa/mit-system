<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class RevisionHistory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'revision_histories';
    protected $guarded = [];
}
