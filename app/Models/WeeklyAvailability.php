<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyAvailability extends Model
{
    protected $table = 'weekly_availability';
    protected $primaryKey = 'availability_id';
    protected $guarded = [];

    public function group(): BelongsTo
    {
        return $this->belongsTo(KelompokWarga::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(MitWeek::class, 'week_id', 'week_id');
    }
}
