<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class RecommendationLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'recommendation_logs';
    protected $guarded = [];
}
