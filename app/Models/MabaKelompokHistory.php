<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MabaKelompokHistory extends Model
{
    protected $table = 'maba_kelompok_history';
    protected $primaryKey = 'history_id';
    public $timestamps = false;
    protected $guarded = [];

    public function maba(): BelongsTo
    {
        return $this->belongsTo(Maba::class, 'maba_id', 'maba_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(KelompokWarga::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(MitWeek::class, 'week_id', 'week_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
