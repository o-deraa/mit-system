<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitWeek extends Model
{
    protected $table = 'mit_week';
    protected $primaryKey = 'week_id';
    protected $guarded = [];
    protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
];
}
